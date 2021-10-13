<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Angajat;
use App\Models\Pontaj;
use App\Models\ProdusOperatie;
use App\Models\NormaLucrata;

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

        $angajat = Angajat::select('id', 'nume')->where('cod_de_acces', $request->cod_de_acces)->first();

        $request->session()->put('angajat', $angajat);

        return redirect('aplicatie-angajati/meniul-principal');
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
        return view('aplicatie_angajati/comenzi/adauga_comanda_pasul_1', compact('angajat'));
    }

    /**
     * Se seteaza numarul de faza
     */
    public function postAdaugaComandaPasul1(Request $request)
    {
        $request->validate(
                [
                    'numar_de_faza' => 'required|exists:produse_operatii'
                ]
            );

        $produs_operatie = ProdusOperatie::where('numar_de_faza', $request->numar_de_faza)->first();

        $angajat = $request->session()->get('angajat');
        $angajat->numar_de_faza = $produs_operatie->numar_de_faza;
        $angajat->produs = $produs_operatie->produs->nume ?? '';
        $angajat->operatie = $produs_operatie->nume;

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
        $request->validate(
                [
                    'numar_de_bucati' => 'required|numeric|between:1,9999',
                ]
            );

        $angajat = $request->session()->get('angajat');


        $produs_operatie = ProdusOperatie::where('numar_de_faza', $angajat->numar_de_faza)->first();

        $norma_lucrata = NormaLucrata::firstOrNew([
            'angajat_id' => $angajat->id,
            'numar_de_faza' => $angajat->numar_de_faza
        ]);

        // Se verifica sa nu se depaseasca norma
        // din norma efectuata pentru produs_operatie, se scade toata norma lucrata veche, se adauga cantitatea noua din request, si se verifica cu norma stabilita pentru produs_operatie
        if (($produs_operatie->norma_efectuata + $request->numar_de_bucati) > $produs_operatie->norma){
            return back()->with('error', 'Cantitatea pe care doriți să o introduceți depășește norma totală pentru Faza "' . $norma_lucrata->numar_de_faza . '". Cantitatea maximă pe care o mai puteți adăuga este "' . ($produs_operatie->norma - $produs_operatie->norma_efectuata) . '"!');
        } else {
            $produs_operatie->norma_efectuata += $request->numar_de_bucati;
            $produs_operatie->save();

            $norma_lucrata->cantitate += $request->numar_de_bucati;
            $norma_lucrata->save();
        }

        // $norma_lucrata = NormaLucrata::make();
        // $norma_lucrata->angajat_id = $angajat->id;
        // $norma_lucrata->numar_de_faza = $angajat->numar_de_faza;
        // $norma_lucrata->cantitate = $request->numar_de_bucati;
        // $norma_lucrata->save();

        $angajat->cantitate = $request->numar_de_bucati;
        $angajat->cantitate_total = $norma_lucrata->cantitate;

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

        $angajat = $request->session()->get('angajat');
        return view('aplicatie_angajati/comenzi/adauga_comanda_pasul_3', compact('angajat'));
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

}
