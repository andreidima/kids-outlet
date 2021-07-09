<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $angajat = \App\Models\Angajat::select('id', 'nume')->where('cod_de_acces', $request->cod_de_acces)->first();

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
        $angajat = new \App\Models\Angajat( $request->session()->get('angajat')->only('id', 'nume') );
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
                    'numar_de_faza' => 'required',
                ]
            );

        $angajat = $request->session()->get('angajat');
        $angajat->numar_de_faza = $request->numar_de_faza;

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
                    'numar_de_bucati' => 'required|numeric',
                ]
            );

        $angajat = $request->session()->get('angajat');
        $angajat->numar_de_bucati = $request->numar_de_bucati;

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
        return view('aplicatie_angajati/pontaj/pontaj', compact('angajat'));
    }

}
