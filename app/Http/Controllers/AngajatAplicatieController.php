<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Angajat;
use App\Models\Pontaj;
use App\Models\Produs;
use App\Models\ProdusOperatie;
use App\Models\NormaLucrata;
use App\Models\LogareAplicatieAngajat;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class AngajatAplicatieController extends Controller
{
    /**
     * Se returneaza pagina pentru logare prin cod de acces
     */
    public function autentificare(Request $request)
    {
        $angajat = $request->session()->forget('angajat');

        return view('aplicatie_angajati/autentificare');
    }

    /**
     * Se logheaza userul in aplicatie pe baza codului de acces
     */
    public function postAutentificare(Request $request)
    {
        // Salvare date despre incercarea de logare
        $logare = new LogareAplicatieAngajat;
        $logare->cod_de_acces = $request->cod_de_acces;
        $logare->ip_address = $request->ip();
        $logare->user_agent = $request->header('User-Agent');
        $logare->status = "esuata";
        $logare->save();

        $request->validate(
                [
                    'cod_de_acces' => 'required|exists:angajati,cod_de_acces',
                ],
                [
                    'cod_de_acces.required' => ('Câmpul cod de acces este obligatoriu. <br> The access code field is mandatory.'),
                    'cod_de_acces.exists' => ('Câmpul cod de acces selectat nu este valid. <br> The selected access code field is not valid.'),
                ]
            );

        $angajat = Angajat::with('roluri')->select('id', 'nume', 'sectia', 'activ', 'limba_aplicatie')->where('cod_de_acces', $request->cod_de_acces)->first();

        // Daca s-a ajuns in acest punct, inseamna ca logarea este reusita, si se salveaza acest lucru in baza de date
        $logare->status = "reusita";
        $logare->angajat = $angajat->nume;
        $logare->activ = $angajat->activ;
        $logare->update();

        if ($angajat->activ === 1){
            $request->session()->put('angajat', $angajat);
        } else {
            return back ()->with('error',
                    (
                        ($angajat->limba_aplicatie === 1) ?
                            (
                                'Acest cont este dezactivat!'
                            )
                            :
                            (
                                'මෙම ගිණුම අබල කර ඇත!'
                                . ' <br> ' .
                                'This account is disabled!'
                            )
                    ));
        }

        return redirect('aplicatie-angajati/meniul-principal');
    }

    /**
     * Se returneaza pagina pentru logare prin cod de acces
     */
    public function deconectare(Request $request)
    {
        $angajat = $request->session()->forget('angajat');
        echo "<a href=”javascript:close_window();”>close</a>";
        return redirect('/');
    }

    /**
     * Se afiseaza meniul principal
     */
    public function meniulPrincipal(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }

        // Sterge atribute legate de comenzi sau pontaj, pastrare doar atributele angajatului
        $angajat = new Angajat( $request->session()->get('angajat')->only('id', 'nume', 'sectia', 'limba_aplicatie') );
        $request->session()->put('angajat', $angajat);

        // Sterge data_pontaj
        $request->session()->forget('data_pontaj');

        return view('/aplicatie_angajati/meniul_principal', compact('angajat'));
    }

    /**
     *
     */
    public function adaugaComandaPasul1(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }

        $angajat = $request->session()->get('angajat');

        if (($angajat->sectia === "Moda") || ($angajat->sectia === "Sectie") ){
            $produse = Produs::where('activ', 1)->where('sectia', 'Sectie')->latest()->get();
        } elseif ($angajat->sectia === "Mostre"){
            $produse = Produs::where('activ', 1)->latest()->get(); // se afiseaza toate
        } else {
            $produse = Produs::where('activ', 1)->latest()->get(); // se afiseaza toate
        }

        // dd($produse->toArray());
        return view('aplicatie_angajati/comenzi/adauga_comanda_pasul_1', compact('angajat', 'produse'));
    }

    /**
     * Se seteaza numarul de faza
     */
    public function postAdaugaComandaPasul1(Request $request)
    {
        $angajat = $request->session()->get('angajat');

        $request->validate(
            [
                'id' => 'required|exists:produse'
            ],
            // [
            //     'id.required' => (($angajat->limba_aplicatie === 1) ? 'Câmpul numar de faza este obligatoriu.' : 'Câmpul numar de faza este obligatoriu. <br> අදියර අංක ක්ෂේත්රය අනිවාර්ය වේ. <br> The phase number field is mandatory.')
            // ]
            );

        $produs = Produs::find($request->id);

        $angajat->produs_id = $produs->id;
        $angajat->produs_nume = $produs->nume;

        $request->session()->put('angajat', $angajat);

        return redirect('/aplicatie-angajati/adauga-comanda-pasul-2');
    }

    /**
     *
     */
    public function adaugaComandaPasul2(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }

        $angajat = $request->session()->get('angajat');
        return view('aplicatie_angajati/comenzi/adauga_comanda_pasul_2', compact('angajat'));
    }

    /**
     * Se seteaza numarul de faza
     */
    public function postAdaugaComandaPasul2(Request $request)
    {
        $angajat = $request->session()->get('angajat');

        $produs_operatie = ProdusOperatie::where('produs_id', $angajat->produs_id)->where('numar_de_faza', $request->numar_de_faza)->first();

        $request->validate(
                [
                    'numar_de_faza' => ['required',
                        function ($attribute, $value, $fail) use ($angajat, $produs_operatie) {
                            if($produs_operatie === null){
                                $produs = Produs::find($angajat->produs_id);
                                $fail (
                                    ($angajat->limba_aplicatie === 1) ?
                                    ('Produsul ' . $produs->nume . ' nu are faza ' . $value)
                                    :
                                    ('Produsul ' . $produs->nume . ' nu are faza ' . $value . '. ' . 'නිපැයුම ' . $produs->nume . ' එයට අදියරක් නොමැත ' . $value . '. ' . 'The product ' . $produs->nume . ' it has no phase ' . $value . '.')
                                );
                            }
                        },
                    ],
                ],
                [
                    'numar_de_faza.required' => (($angajat->limba_aplicatie === 1) ? 'Câmpul numar de faza este obligatoriu.' : 'අදියර අංක ක්ෂේත්රය අනිවාර්ය වේ. <br> The phase number field is mandatory.')
                ]
            );

        $angajat->numar_de_faza = $produs_operatie->numar_de_faza;
        $angajat->operatie_nume = $produs_operatie->nume;
        // $angajat->pret_pe_bucata = $produs_operatie->pret;

        $request->session()->put('angajat', $angajat);

        return redirect('/aplicatie-angajati/adauga-comanda-pasul-3');
    }

    /**
     *
     */
    public function adaugaComandaPasul3(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }

        $request->session()->put('submitForm', 'true');

        $angajat = $request->session()->get('angajat');
        return view('aplicatie_angajati/comenzi/adauga_comanda_pasul_3', compact('angajat'));
    }

    /**
     * Se seteaza numarul de faza
     */
    public function postAdaugaComandaPasul3(Request $request)
    {
        if(!empty($request->session()->get('submitForm'))){
            $angajat = $request->session()->get('angajat');

            $request->validate(
                    [
                        'numar_de_bucati' => 'required|numeric|between:1,9999',
                    ],
                    [
                        'numar_de_bucati.required' => (($angajat->limba_aplicatie === 1) ? 'Câmpul numar de bucati este obligatoriu.' :
                                                            'කෑලි ක්ෂේත්රයේ සංඛ්යාව අනිවාර්ය වේ. <br> The number of pieces field is mandatory.'),
                        'numar_de_bucati.numeric' => (($angajat->limba_aplicatie === 1) ? 'Câmpul numar de bucati trebuie să fie un număr.' :
                                                            'කෑලි ක්ෂේත්රයේ සංඛ්යාව අංකයක් විය යුතුය. <br> The number of pieces field must be a number.'),
                        'numar_de_bucati.between' => (($angajat->limba_aplicatie === 1) ? 'Câmpul numar de bucati trebuie să fie între 1 și 9999.' :
                                                            'කෑලි ක්ෂේත්‍ර ගණන 1 සහ 9999 අතර විය යුතුය. <br> The number of pieces field must be between 1 and 9999.'),
                    ]
                );

            $produs_operatie = ProdusOperatie::with('produs')->where('produs_id', $angajat->produs_id)->where('numar_de_faza', $angajat->numar_de_faza)->first();

            // In prima faza norma daca era pentru acelasi numar de faza se aduna la aceasi inregistrare
            // Pentru un control maxim, acum norma se adauga individual de fiecare data
            // $norma_lucrata = NormaLucrata::firstOrNew([
            //     'angajat_id' => $angajat->id,
            //     'numar_de_faza' => $angajat->numar_de_faza
            // ]);
            $norma_lucrata = NormaLucrata::make();
            $norma_lucrata->angajat_id = $angajat->id;
            $norma_lucrata->data = Carbon::now();
            $norma_lucrata->produs_operatie_id = $produs_operatie->id;
            // Se verifica sa nu se depaseasca norma
            if (($produs_operatie->norma_totala_efectuata + $request->numar_de_bucati) > ($produs_operatie->produs->cantitate ?? 0)){
                return back()->with('error',
                    (
                        ($angajat->limba_aplicatie === 1) ?
                            (
                                'Cantitatea pe care doriți să o introduceți depășește norma totală pentru Faza "' . $produs_operatie->numar_de_faza .
                                '". Cantitatea maximă pe care o mai puteți adăuga este "' . (($produs_operatie->produs->cantitate ?? 0) - $produs_operatie->norma_totala_efectuata) . '"!'
                            )
                            :
                            (
                                ' ඔබට ඇතුළත් කිරීමට අවශ්‍ය මුදල අදියර සඳහා වන මුළු අනුපාතය ඉක්මවයි "' . $produs_operatie->numar_de_faza .
                                '". ඔබට එකතු කළ හැකි උපරිම මුදල වේ "' . (($produs_operatie->produs->cantitate ?? 0) - $produs_operatie->norma_totala_efectuata) . '"!'
                                . ' <br> ' .
                                ' The amount you want to enter exceeds the total rate for the Phase "' . $produs_operatie->numar_de_faza .
                                '". The maximum amount you can add is "' . (($produs_operatie->produs->cantitate ?? 0) - $produs_operatie->norma_totala_efectuata) . '"!'
                            )
                    ));
            } else {
                $produs_operatie->norma_totala_efectuata += $request->numar_de_bucati;
                $produs_operatie->save();

                $norma_lucrata->cantitate = $request->numar_de_bucati;
                $norma_lucrata->save();
            }

            $angajat->cantitate = $request->numar_de_bucati;

            $request->session()->forget('submitForm');

            $request->session()->put('angajat', $angajat);
        }

        return redirect('/aplicatie-angajati/adauga-comanda-pasul-4');
    }

    /**
     *
     */
    public function adaugaComandaPasul4(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }

        $angajat = $request->session()->get('angajat');

        return view('aplicatie_angajati/comenzi/adauga_comanda_pasul_4', compact('angajat'));
    }

    /**
     *
     */
    public function realizat(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }

        $angajat = $request->session()->get('angajat');

        $search_data_inceput = \Request::get('search_data_inceput') ? Carbon::parse(\Request::get('search_data_inceput')) : Carbon::today();
        $search_data_sfarsit = \Request::get('search_data_sfarsit') ? Carbon::parse(\Request::get('search_data_sfarsit')) : Carbon::today();
        // $search_data_inceput = \Request::get('search_data_inceput');
        // $search_data_sfarsit = \Request::get('search_data_sfarsit');

        if ($search_data_inceput->diffInDays($search_data_sfarsit) > 65){
            return back()->with('error',
                    (
                        ($angajat->limba_aplicatie === 1) ?
                            (
                                'Vă rog căutați o perioadă de maxim 65 de zile.'
                            )
                            :
                            (
                                'කරුණාකර දින 65ක උපරිම කාලයක් සඳහා සොයන්න.'
                                . ' <br> ' .
                                'Please search for a maximum period of 65 days.'
                            )
                    ));
        }

        // if ($search_data_inceput && $search_data_sfarsit){
            $norme_lucrate = NormaLucrata::with('produs_operatie.produs')
                ->where('angajat_id', $angajat->id)
                ->whereDate('data', '>=', $search_data_inceput)
                ->whereDate('data', '<=', $search_data_sfarsit)
                ->orderBy('data')
                ->orderBy('produs_operatie_id')
                ->get();
        // } else{
        //     $norme_lucrate = '';
        // }

        // dd($angajat, $norme_lucrate);

        return view('aplicatie_angajati/realizat/realizat', compact('angajat', ($norme_lucrate ? 'norme_lucrate' : ''), 'search_data_inceput', 'search_data_sfarsit'));
    }

    public function stergeNormaLucrata(Request $request, NormaLucrata $norma_lucrata)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }
        $angajat = $request->session()->get('angajat');


        // if (
        //         ($norma_lucrata->angajat_id === $angajat->id)
        //         &&
        //         (
        //             (
        //                 (Carbon::now()->day < 6)
        //                 &&
        //                 ($norma_lucrata->data >= Carbon::now()->subMonthsNoOverflow(1)->startOfMonth()->toDateString())
        //             )
        //             ||
        //             (
        //                 (Carbon::now()->day >= 6)
        //                 &&
        //                 ($norma_lucrata->data >= Carbon::now()->startOfMonth()->toDateString())
        //             )
        //         )
        //     )

        // $data_stergere_lucru_pana_la = \Carbon\Carbon::parse(\App\Models\Variabila::where('variabila', 'data_stergere_lucru_pana_la')->value('valoare'));
        // if ($data_stergere_lucru_pana_la->lessThan(\Carbon\Carbon::parse($norma_lucrata->data)))

        if  (
                ($norma_lucrata->angajat_id === $angajat->id)
                &&
                \Carbon\Carbon::parse($norma_lucrata->data)->isCurrentMonth()
            )
        {
            $norma_lucrata->produs_operatie->norma_totala_efectuata -= $norma_lucrata->cantitate;
            $norma_lucrata->produs_operatie->save();
            $norma_lucrata->delete();

            // $produs_operatie = ProdusOperatie::where('produs_id', $angajat->produs_id)->where('numar_de_faza', $angajat->numar_de_faza)->first();

            return back()->with('success',
                    (
                        ($angajat->limba_aplicatie === 1) ?
                            (
                                'Comanda a fost ștearsă cu succes!'
                            )
                            :
                            (
                                'ඇණවුම සාර්ථකව මකා ඇත!'
                                . ' <br> ' .
                                'The order has been successfully deleted!'
                            )
                    ));
        } else {
            return back()->with('error',
                    (
                        ($angajat->limba_aplicatie === 1) ?
                            (
                                'Această comandă nu poate fi ștearsă!'
                            )
                            :
                            (
                                'මෙම ඇණවුම මකා දැමිය නොහැක!'
                                . ' <br> ' .
                                'This order cannot be deleted!'
                            )
                    ));
        }
    }

    /**
     *
     */
    public function pontaj(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }
        $angajat = $request->session()->get('angajat');

        // dd($angajat->pontaj_azi()->first()->data);

        $pontaj = Pontaj::where('angajat_id' , $angajat->id)->where('data', Carbon::today())->first();
        if ($pontaj === null){
            // dd('$pontaj');
            $pontaj = new Pontaj;
            $pontaj->angajat_id = $angajat->id;
            $pontaj->data = Carbon::now();
        }
        // dd($angajat, $pontaj);

        switch ($request->moment) {
            case 'sosire':
                if ( !empty ($pontaj->ora_sosire) ){
                    // ora de sosire este deja setata
                } else {
                    $pontaj->ora_sosire = Carbon::now()->toTimeString();
                    $pontaj->save();
                }
                break;
            case 'plecare':
                if ( !empty ($pontaj->ora_plecare) ){
                    // ora de plecare este deja setata
                } else {
                    $pontaj->ora_plecare = Carbon::now()->toTimeString();
                    $pontaj->save();
                }
                break;
        }

        // Readucerea modelului din baza de date, pentru ca altfel nu se regasesc modificarile facute „relatiei” pontaj
        $angajat = Angajat::find($angajat->id);

        $request->session()->put('angajat', $angajat);

        return view('aplicatie_angajati/pontaj/pontaj', compact('angajat', 'pontaj'));
    }

    /**
     * Pontaj de catre pontator
     */
    public function pontajPontator(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }
        $angajat = $request->session()->get('angajat');

        $data_pontaj = $request->session()->get('data_pontaj') ?? Carbon::now()->toDateString();
        $request->session()->put('data_pontaj', $data_pontaj);

        $angajati = $angajat->angajati_de_pontat()
            // ->with('pontaj_azi')
            ->with(['pontaj' => function ($query) use ($data_pontaj) {
                $query->where('data', $data_pontaj);
            }])
            ->orderBy('nume')->get();

        // $data = \Request::get('search_data') ?? Carbon::now()->toTimeString();

        return view('aplicatie_angajati/pontajPontator/pontaj', compact('angajat', 'angajati', 'data_pontaj'));
    }

    /**
     * Pontaj de catre pontator
     * Modificare
     */
    public function pontajModificaPontator(Request $request, Angajat $angajat_de_pontat = null)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }
        $angajat = $request->session()->get('angajat');

        if(empty($request->session()->get('data_pontaj'))){
            return redirect('/aplicatie-angajati/meniul-principal');
        }
        $data_pontaj = $request->session()->get('data_pontaj');

        $pontaj = Pontaj::firstOrNew([
            'angajat_id' => $angajat_de_pontat->id,
            'data' => $data_pontaj
        ]);

        return view('aplicatie_angajati/pontajPontator/pontajModifica', compact('angajat', 'pontaj', 'data_pontaj'));
    }

    /**
     * Pontaj de catre pontator
     */
    public function postPontajPontator(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }
        $angajat = $request->session()->get('angajat');

        if(empty($request->session()->get('data_pontaj'))){
            return redirect('/aplicatie-angajati/meniul-principal');
        }
        $data_pontaj = \Request::get('data_pontaj') ?? $request->session()->get('data_pontaj');
        $request->session()->put('data_pontaj', $data_pontaj);

        $pontaj = Pontaj::firstOrNew([
            'angajat_id' => $request->angajat_id,
            'data' => Carbon::parse($data_pontaj)
        ]);

        // dd($pontaj);

        switch ($request->moment) {
            case 'sosire':
                if ( !empty ($pontaj->ora_sosire) ){
                    // ora de sosire este deja setata
                } else {
                    $pontaj->ora_sosire = $request->ora;
                    $pontaj->save();
                }
                break;
            case 'plecare':
                if ( !empty ($pontaj->ora_plecare) ){
                    // ora de plecare este deja setata
                } else {
                    $pontaj->ora_plecare = $request->ora;
                    $pontaj->save();
                }
                break;
            case 'modificare_particularizata':
                $request->validate(
                        [
                            'ora_sosire' => '',
                            'ora_plecare' => 'nullable|after:ora_sosire'
                        ]
                    );
                $pontaj->ora_sosire = $request->ora_sosire;
                $pontaj->ora_plecare = $request->ora_plecare;
                $pontaj->concediu = $request->concediu;
                $pontaj->save();
                break;
        }

        return redirect('/aplicatie-angajati/pontaj');
    }

    /**
     * Pontaj de catre pontator
     */
    public function pontajPonteazaToti(Request $request, $moment = null)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }
        $angajat = $request->session()->get('angajat');

        if(empty($request->session()->get('data_pontaj'))){
            return redirect('/aplicatie-angajati/meniul-principal');
        }
        $data_pontaj = $request->session()->get('data_pontaj');

        $angajati = $angajat->angajati_de_pontat()->get();

        switch ($moment) {
            case 'sosire':
                foreach ($angajati as $angajat){
                    $pontaj = Pontaj::firstOrNew([
                        'angajat_id' => $angajat->id,
                        'data' => $data_pontaj
                    ]);
                    empty($pontaj->concediu) ? ($pontaj->concediu = 0) : '';
                    // if (empty($pontaj->ora_sosire) && ($pontaj->concediu === 0)){
                    if ($pontaj->concediu === 0){
                        $pontaj->ora_sosire = $request->ora_sosire;
                        $pontaj->save();
                    }
                }
                break;
            case 'plecare':
                foreach ($angajati as $angajat){
                    $pontaj = Pontaj::firstOrNew([
                        'angajat_id' => $angajat->id,
                        'data' => $data_pontaj
                    ]);
                    empty($pontaj->concediu) ? ($pontaj->concediu = 0) : '';
                    // if (empty($pontaj->ora_plecare) && ($pontaj->concediu === 0)){
                    if ($pontaj->concediu === 0){
                        $pontaj->ora_plecare = $request->ora_plecare;
                        $pontaj->save();
                    }
                }
                break;
            case 'concediu_odihna_da':
                foreach ($angajati as $angajat){
                    $pontaj = Pontaj::firstOrNew([
                        'angajat_id' => $angajat->id,
                        'data' => $data_pontaj
                    ]);
                    $pontaj->concediu = 2; // medical
                    $pontaj->save();
                }
                break;
            case 'concediu_odihna_nu':
                foreach ($angajati as $angajat){
                    $pontaj = Pontaj::firstOrNew([
                        'angajat_id' => $angajat->id,
                        'data' => $data_pontaj
                    ]);
                    $pontaj->concediu = 0; // medical
                    $pontaj->save();
                }
                break;
        }

        return redirect('/aplicatie-angajati/pontaj');
    }

    /**
     * Verificare Pontaj de catre pontator
     */
    public function pontajVerificaPontator(Request $request)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }
        $angajat = $request->session()->get('angajat');

        $search_data_inceput = \Request::get('search_data_inceput') ?? \Carbon\Carbon::now()->startOfWeek()->toDateString();
        $search_data_sfarsit = \Request::get('search_data_sfarsit') ?? \Carbon\Carbon::parse($search_data_inceput)->addDays(4)->toDateString();

        if (\Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput) > 35){
            return back()->with('error', 'Selectează te rog intervale mai mici de 35 de zile, pentru ca extragerea datelor din baza de date să fie eficientă!');
        }

        switch ($request->input('action')) {
            case 'saptamana_anterioara':
                    $search_data_inceput = \Carbon\Carbon::parse($search_data_inceput)->subDays(7)->startOfWeek()->toDateString();
                    $search_data_sfarsit = \Carbon\Carbon::parse($search_data_inceput)->addDays(4)->toDateString();
                break;
            case 'saptamana_urmatoare':
                    $search_data_inceput = \Carbon\Carbon::parse($search_data_sfarsit)->addDays(7)->startOfWeek()->toDateString();
                    $search_data_sfarsit = \Carbon\Carbon::parse($search_data_inceput)->addDays(4)->toDateString();
                break;
        }

        $angajati = $angajat->angajati_de_pontat()
            ->with(['pontaj'=> function($query) use ($search_data_inceput, $search_data_sfarsit){
                $query->whereDate('data', '>=', $search_data_inceput)
                    ->whereDate('data', '<=', $search_data_sfarsit);
            }])
            ->where('activ', 1)
            ->orderBy('nume')
            // ->groupBy('angajat_id')
            // ->paginate(10);
            ->get();

        return view('aplicatie_angajati/pontaj/pontaj_verifica', compact('angajat', 'angajati', 'search_data_inceput', 'search_data_sfarsit'));
    }


    /**
     *
     */
    public function veziFazeProduse(Request $request, Produs $produs = null)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }

        $angajat = $request->session()->get('angajat');
        $produse = Produs::where('activ', 1)->latest()->get();

        isset($produs) ? ($produse_operatii = $produs->produse_operatii) : ($produse_operatii = null);

        return view('aplicatie_angajati/vezi_faze_produse', compact('angajat', 'produse', 'produse_operatii'));
    }

    public function stergeNormaLucrataDinContSefSectie(Request $request, NormaLucrata $norma_lucrata)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }
        $angajat = $request->session()->get('angajat');

        if (\Carbon\Carbon::parse($norma_lucrata->data)->isCurrentMonth()
            ||
            (
                \Carbon\Carbon::parse($norma_lucrata->data)->isLastMonth()
                &&
                \Carbon\Carbon::now()->day <= 14
            )
        ){
            $norma_lucrata->produs_operatie->norma_totala_efectuata -= $norma_lucrata->cantitate;
            $norma_lucrata->produs_operatie->save();
            $norma_lucrata->delete();

            return back()->with('success', 'Comanda a fost ștearsă cu succes!');
        } else {
            return back()->with('error', 'Această comandă nu poate fi ștearsă!');
        }
    }

    public function veziNormeProdusOperatie(Request $request, ProdusOperatie $produs_operatie = null)
    {
        if(empty($request->session()->get('angajat'))){
            return redirect('/aplicatie-angajati');
        }

        $angajat = $request->session()->get('angajat');
        $norme_lucrate = $produs_operatie->norme_lucrate;

        $return_url = url()->previous();

        return view('aplicatie_angajati/vezi_norme', compact('angajat', 'norme_lucrate', 'return_url'));
    }

    // public function blocheazaDeblocheazaIntroducereComenzi()
    // {
    //     $acces_introducere_comenzi = \App\Models\Variabila::where('variabila', 'acces_introducere_comenzi')->first();
    //     ($acces_introducere_comenzi->valoare === 'da') ? ($acces_introducere_comenzi->valoare = 'nu') : ($acces_introducere_comenzi->valoare = 'da');
    //     $acces_introducere_comenzi->save();

    //     return back();
    // }

    // public function produse(Request $request)
    // {
    //     $angajat = $request->session()->get('angajat');
    //     if(($angajat->id ?? '') !== 4){ // Conturile ce pot muta lucrul pe luna trecuta, Mocanu Geanina id=4
    //         return redirect('/aplicatie-angajati');
    //     }

    //     $produse = Produs::select('nume', 'activ')
    //         ->orderBy('activ', 'desc')->latest()->simplePaginate(25);

    //     return view('aplicatie_angajati/produse/index', compact('angajat', 'produse'));
    // }

    public function mutaLucrulPeLunaAnterioara(Request $request)
    {
        $angajat = $request->session()->get('angajat');
        if(($angajat->id ?? '') !== 4){ // Conturile ce pot muta lucrul pe luna trecuta, Mocanu Geanina id=4
            return redirect('/aplicatie-angajati');
        }

        $norme_lucrate = NormaLucrata::select('id', 'data')
            ->where('data', '>=', Carbon::today()->startOfMonth())
            ->where('data', '<=', Carbon::today()->startOfMonth()->addDays(14))
            ->get();

        // Daca a fost apasat butonul de mutare al lucrului, acesta va fi mutat
        if ($request->action === 'mutaLucrul'){
            if ($norme_lucrate->count() === 0){
                return redirect('/aplicatie-angajati/muta-lucrul-pe-luna-anterioara')->with('warning', 'Nu exista „norme lucrate” de mutat!');
            }
            NormaLucrata::select('id', 'data')
                ->where('data', '>=', Carbon::today()->startOfMonth())
                ->where('data', '<=', Carbon::today()->startOfMonth()->addDays(14))
                ->update(['data' => Carbon::today()->subMonthNoOverflow()->endOfMonth()]);
            return redirect('/aplicatie-angajati/muta-lucrul-pe-luna-anterioara')->with('status', 'Au fost mutate cu succes un număr de ' . $norme_lucrate->count() . ' ”norme lucrate”!');
        }

        return view('aplicatie_angajati/mutaLucrulPeLunaAnterioara', compact('angajat', 'norme_lucrate'));
    }
}
