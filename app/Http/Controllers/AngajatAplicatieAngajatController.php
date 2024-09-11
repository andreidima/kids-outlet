<?php

namespace App\Http\Controllers;

use App\Models\Angajat;
use App\Models\Produs;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AngajatAplicatieAngajatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $angajat = $request->session()->get('angajat');
        // if( // Conturile ce pot vedea angajatii
        //     (($angajat->id ?? '') !== 4) || // Mocanu Geanina id = 4
        //     (($angajat->id ?? '') !== 12) || // Duna Luminita
        //     (($angajat->id ?? '') !== 91) // Borchina Liliana
        //     )
        //     { // Conturile ce pot vedea angajatii, Mocanu Geanina id = 4
        //     return redirect('/aplicatie-angajati');
        // }

        $angajati = Angajat::where('id', '>', '3') // Se sare peste conturile de test Andrei Dima
            ->orderBy('nume')->get();

        return view('aplicatie_angajati.angajati.index', compact('angajati'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $angajati = Angajat::select('id', 'nume')
            // ->where('id', '>', '3') // Se sare peste conturile de test Andrei Dima
            ->whereIn('id', [4,73])
            ->where('id', '>', '3')
            ->where('activ', 1)
            ->orderBy('nume', 'asc')
            ->get();

        return view('aplicatie_angajati.angajati.create', compact('angajati'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $angajat = Angajat::create($this->validateRequest($request));
        $angajat->angajati_pontatori()->sync($request->angajat_pontatori);

        $angajat->avans = intval(\App\Models\Variabila::where('variabila', 'avans_la_salariu')->value('valoare'));
        $angajat->save();

        return redirect('/aplicatie-angajati/angajati')->with('status', 'Angajatul "' . $angajat->nume . '" a fost adăugat cu succes!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Angajat  $angajat
     * @return \Illuminate\Http\Response
     */
    public function show(Angajat $angajat)
    {
        return view('aplicatie_angajati.angajati.show', compact('angajat'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Angajat  $angajat
     * @return \Illuminate\Http\Response
     */
    public function edit(Angajat $angajat)
    {
        $angajati = Angajat::select('id', 'nume')
            // ->where('id', '>', '3') // Se sare peste conturile de test Andrei Dima
            ->whereIn('id', [4,73])
            ->where('id', '>', '3')
            ->where('activ', 1)
            ->orderBy('nume', 'asc')
            ->get();

        return view('aplicatie_angajati.angajati.edit', compact('angajat', 'angajati'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Angajat  $angajat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Angajat $angajat)
    {
        $angajat->update($this->validateRequest($request, $angajat));
        $angajat->angajati_pontatori()->sync($request->angajat_pontatori);

        return redirect('/aplicatie-angajati/angajati')->with('status', 'Angajatul "' . $angajat->nume . '" a fost modificat cu succes!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Angajat  $angajat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Angajat $angajat)
    {
        // $angajat->delete();
        // return redirect('/angajati')->with('status', 'Angajatul "' . $angajat->nume . '" a fost șters cu succes!');
    }

    /**
     * Validate the request attributes.
     *
     * @return array
     */
    protected function validateRequest(Request $request, $angajat = null)
    {
        return request()->validate([
            'nume' => 'nullable|max:100',
            'telefon' => 'nullable|max:50',
            // 'cod_de_acces' => [
            //     'required',
            //     'min:8',
            //     'max:50',
            //     Rule::unique('App\Models\Angajat')->ignore($angajat),
            // ],
            'sectia' => 'required|max:500',
            'firma' => 'required|max:500',
            'prod' => 'nullable|max:200',
            'ore_angajare' => 'required|numeric|between:1,12',
            'foaie_pontaj' => 'nullable|max:200',
            'limba_aplicatie' => 'required',
            'activ' => 'nullable|integer|between:0,1'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accesFaze(Angajat $angajat)
    {
        $angajat = Angajat::where('id', $angajat->id)->with('produseOperatii')->first();
        $produse = Produs::select('id', 'nume')
            ->with('produse_operatii', function ($query) {
                return $query->orderBy('numar_de_faza');
            })
            ->where('activ' , 1)->get();

        return view('angajati.diverse.accesFazeForm', compact('angajat', 'produse'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accesFazePost(Request $request, Angajat $angajat)
    {
        // dd($request->angajatProduseOperatii);
        $angajat->produseOperatii()->sync($request->angajatProduseOperatii);

        return redirect('/aplicatie-angajati/angajati')->with('status', 'Fazele angajatului "' . $angajat->nume . '" au fost modificate cu succes!');
    }
}
