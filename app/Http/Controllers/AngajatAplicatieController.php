<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Angajat;
use App\Models\Pontaj;
use App\Models\Produs;
use App\Models\ProdusOperatie;
use App\Models\NormaLucrata;

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
        $request->validate(
                [
                    'cod_de_acces' => 'required|exists:angajati,cod_de_acces',
                ]
            );

        $angajat = Angajat::with('roluri')->select('id', 'nume')->where('cod_de_acces', $request->cod_de_acces)->first();

        $request->session()->put('angajat', $angajat);

        return redirect('aplicatie-angajati/meniul-principal');
    }

    /**
     * Se returneaza pagina pentru logare prin cod de acces
     */
    public function deconectare(Request $request)
    {
        $angajat = $request->session()->forget('angajat');

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
        $angajat = new Angajat( $request->session()->get('angajat')->only('id', 'nume') );
        $request->session()->put('angajat', $angajat);

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
        $produse = Produs::where('activ', 1)->get();
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
                return back()->with('error', 'Cantitatea pe care doriți să o introduceți depășește norma totală pentru Faza "' . $norma_lucrata->numar_de_faza . '". Cantitatea maximă pe care o mai puteți adăuga este "' . ($produs_operatie->norma_totala - $produs_operatie->norma_totala_efectuata) . '"!');
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

        $search_data_inceput = \Request::get('search_data_inceput') ? \Carbon\Carbon::parse(\Request::get('search_data_inceput')) : \Carbon\Carbon::today();
        $search_data_sfarsit = \Request::get('search_data_sfarsit') ? \Carbon\Carbon::parse(\Request::get('search_data_sfarsit')) : \Carbon\Carbon::today();
        // $search_data_inceput = \Request::get('search_data_inceput');
        // $search_data_sfarsit = \Request::get('search_data_sfarsit');

        if ($search_data_inceput->diffInDays($search_data_sfarsit) > 35){
            return back()->with('error', 'Vă rog căutați o perioadă de maxim 35 de zile.');
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

        if (($norma_lucrata->data === \Carbon\Carbon::now()->toDateString()) && ($norma_lucrata->angajat_id === $angajat->id)){
            $norma_lucrata->produs_operatie->norma_totala_efectuata -= $norma_lucrata->cantitate;
            $norma_lucrata->produs_operatie->save();
            $norma_lucrata->delete();

            $produs_operatie = ProdusOperatie::where('produs_id', $angajat->produs_id)->where('numar_de_faza', $angajat->numar_de_faza)->first();

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

        $pontaj = Pontaj::where('angajat_id' , $angajat->id)->where('data', \Carbon\Carbon::today())->first();
        if ($pontaj === null){
            // dd('$pontaj');
            $pontaj = new Pontaj;
            $pontaj->angajat_id = $angajat->id;
            $pontaj->data = \Carbon\Carbon::now();
        }
        // dd($angajat, $pontaj);

        switch ($request->moment) {
            case 'sosire':
                if ( !empty ($pontaj->ora_sosire) ){
                    // ora de sosire este deja setata
                } else {
                    $pontaj->ora_sosire = \Carbon\Carbon::now()->toTimeString();
                    $pontaj->save();
                }
                break;
            case 'plecare':
                if ( !empty ($pontaj->ora_plecare) ){
                    // ora de plecare este deja setata
                } else {
                    $pontaj->ora_plecare = \Carbon\Carbon::now()->toTimeString();
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

        $angajati = $angajat->angajati_de_pontat()
            ->with('pontaj_azi')
            ->orderBy('nume')->get();

        return view('aplicatie_angajati/pontajPontator/pontaj', compact('angajat', 'angajati'));
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

        $pontaj = Pontaj::firstOrCreate([
            'angajat_id' => $angajat_de_pontat->id,
            'data' => Carbon::now()->toDateString()
        ]);

        return view('aplicatie_angajati/pontajPontator/pontajModifica', compact('angajat', 'pontaj'));
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

        // dd($request);

        $pontaj = Pontaj::firstOrCreate([
            'angajat_id' => $request->angajat_id,
            'data' => Carbon::parse($request->data)->toDateString()
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

        $angajati = $angajat->angajati_de_pontat()->get();

        switch ($moment) {
            case 'sosire':
                foreach ($angajati as $angajat){
                    $pontaj = Pontaj::firstOrCreate([
                        'angajat_id' => $angajat->id,
                        'data' => Carbon::now()->toDateString()
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
                    $pontaj = Pontaj::firstOrCreate([
                        'angajat_id' => $angajat->id,
                        'data' => Carbon::now()->toDateString()
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

}
