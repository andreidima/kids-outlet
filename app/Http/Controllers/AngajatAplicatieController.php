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

        return view('angajati_aplicatie/logare');
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

        return redirect('angajati_aplicatie/meniul_principal');
    }








    public function adaugaComandaNoua(Request $request)
    {
        $angajat_comanda = $request->session()->forget('angajat_comanda');

        return redirect('/adauga-comanda-pasul-1');
    }

    /**
     * Se returneaza pagina pentru logare prin cod de acces
     */
    public function adaugaComandaPasul1(Request $request)
    {
        return view('comenzi/adauga-comanda-pasul-1');
    }

    /**
     * Se logheaza userul in aplicatie pe baza codului de acces
     */
    public function postAdaugaComandaPasul1(Request $request)
    {
        $request->validate(
                [
                    'cod_de_acces' => 'required|exists:angajati,cod_de_acces',
                ]
            );

        $angajat_comanda = \App\Models\Angajat::select('id', 'nume')->where('cod_de_acces', $request->cod_de_acces)->first();

        $request->session()->put('angajat_comanda', $angajat_comanda);

        return redirect('/adauga-comanda-pasul-2');
    }

    /**
     * Afiseaza pagina pentru selectarea numarului de faza
     */
    public function adaugaComandaPasul2(Request $request)
    {
        if(empty($request->session()->get('angajat_comanda'))){
            return redirect('/adauga-comanda-noua');
        }else{
            $angajat_comanda = $request->session()->get('angajat_comanda');
            return view('comenzi/adauga-comanda-pasul-2', compact('angajat_comanda'));
        }
    }

    /**
     * Se seteaza numarul de faza
     */
    public function postAdaugaComandaPasul2(Request $request)
    {
        $request->validate(
                [
                    'numar_de_faza' => 'required',
                ]
            );

        $angajat_comanda = $request->session()->get('angajat_comanda');
        $angajat_comanda->numar_de_faza = $request->numar_de_faza;

        $request->session()->put('angajat_comanda', $angajat_comanda);

        return redirect('/adauga-comanda-pasul-3');
    }

    /**
     *
     */
    public function adaugaComandaPasul3(Request $request)
    {
        if(empty($request->session()->get('angajat_comanda'))){
            return redirect('/adauga-comanda-noua');
        }else{
            $angajat_comanda = $request->session()->get('angajat_comanda');
            return view('comenzi/adauga-comanda-pasul-3', compact('angajat_comanda'));
        }
    }

    /**
     * Se seteaza numarul de faza
     */
    public function postAdaugaComandaPasul3(Request $request)
    {
        $request->validate(
                [
                    'numar_de_bucati' => 'required|numeric',
                ]
            );

        $angajat_comanda = $request->session()->get('angajat_comanda');
        $angajat_comanda->numar_de_bucati = $request->numar_de_bucati;

        $request->session()->put('angajat_comanda', $angajat_comanda);

        return redirect('/adauga-comanda-pasul-4');
    }

    /**
     *
     */
    public function adaugaComandaPasul4(Request $request)
    {
        if(empty($request->session()->get('angajat_comanda'))){
            return redirect('/adauga-comanda-noua');
        }else{
            $angajat_comanda = $request->session()->get('angajat_comanda');
            return view('comenzi/adauga-comanda-pasul-4', compact('angajat_comanda'));
        }
    }

}
