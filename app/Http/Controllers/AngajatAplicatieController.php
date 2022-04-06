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
                ]
            );

        // Daca s-a ajuns in acest punct, inseamna ca logarea este reusita, si se salveaza acest lucru in baza de date
        $logare->status = "reusita";
        $logare->update();

        $angajat = Angajat::with('roluri')->select('id', 'nume', 'sectia')->where('activ', 1)->where('cod_de_acces', $request->cod_de_acces)->first();

        if ($angajat){
            $request->session()->put('angajat', $angajat);
        } else {
            return back ()->with('error', 'Acest cont este dezactivat!');
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
        $angajat = new Angajat( $request->session()->get('angajat')->only('id', 'nume', 'sectia') );
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

        if ($angajat->sectia === "Moda"){
            $produse = Produs::where('activ', 1)->where('sectia', 'Sectie')->latest()->get();
        } else{
            $produse = Produs::where('activ', 1)->where('sectia', $angajat->sectia)->latest()->get();
        }
        // dd($produse->toArray());
        return view('aplicatie_angajati/comenzi/adauga_comanda_pasul_1', compact('angajat', 'produse'));
    }

    /**
     * Se seteaza numarul de faza
     */
    public function postAdaugaComandaPasul1(Request $request)
    {
        $request->validate(
                [
                    'id' => 'required|exists:produse'
                ]
            );

        $angajat = $request->session()->get('angajat');
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
                                $fail ('Produsul ' . $produs->nume . ' nu are faza ' . $value);
                            }
                        },
                    ],
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
        $request->validate(
                [
                    'numar_de_bucati' => 'required|numeric|between:1,9999',
                ]
            );

        if(!empty($request->session()->get('submitForm'))){
            $angajat = $request->session()->get('angajat');

            $produs_operatie = ProdusOperatie::where('produs_id', $angajat->produs_id)->where('numar_de_faza', $angajat->numar_de_faza)->first();

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
            // din norma efectuata pentru produs_operatie, se scade toata norma lucrata veche, se adauga cantitatea noua din request, si se verifica cu norma stabilita pentru produs_operatie
            if (($produs_operatie->norma_totala_efectuata + $request->numar_de_bucati) > $produs_operatie->norma_totala){
                return back()->with('error', 'Cantitatea pe care doriți să o introduceți depășește norma totală pentru Faza "' . $produs_operatie->numar_de_faza . '". Cantitatea maximă pe care o mai puteți adăuga este "' . ($produs_operatie->norma_totala - $produs_operatie->norma_totala_efectuata) . '"!');
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
            return back()->with('error', 'Vă rog căutați o perioadă de maxim 65 de zile.');
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

        if (
                ($norma_lucrata->angajat_id === $angajat->id)
                &&
                (
                    (
                        (Carbon::now()->day < 4)
                        &&
                        ($norma_lucrata->data >= Carbon::now()->subMonthsNoOverflow(1)->startOfMonth()->toDateString())
                    )
                    ||
                    (
                        (Carbon::now()->day >= 4)
                        &&
                        ($norma_lucrata->data >= Carbon::now()->startOfMonth()->toDateString())
                    )
                )
            )
        {
            $norma_lucrata->produs_operatie->norma_totala_efectuata -= $norma_lucrata->cantitate;
            $norma_lucrata->produs_operatie->save();
            $norma_lucrata->delete();

            // $produs_operatie = ProdusOperatie::where('produs_id', $angajat->produs_id)->where('numar_de_faza', $angajat->numar_de_faza)->first();

            return back()->with('success', 'Comanda a fost ștearsă cu succes!');
        } else {
            return back()->with('error', 'Această comandă nu poate fi ștearsă!');
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
                    if (empty($pontaj->ora_sosire) && ($pontaj->concediu === 0)){
                        $pontaj->ora_sosire = Carbon::now()->toTimeString();
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
                    if (empty($pontaj->ora_plecare) && ($pontaj->concediu === 0)){
                        $pontaj->ora_plecare = Carbon::now()->toTimeString();
                        $pontaj->save();
                    }
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

    /**
     *
     */
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

}
