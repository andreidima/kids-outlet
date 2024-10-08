<?php

namespace App\Http\Controllers;

use App\Models\Angajat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AngajatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search_nume = \Request::get('search_nume');
        $search_telefon = \Request::get('search_telefon');

        $angajati = Angajat::
            where('id', '>', '3') // Se sare peste conturile de test Andrei Dima
            ->when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            ->when($search_telefon, function ($query, $search_telefon) {
                return $query->where('telefon', 'like', '%' . $search_telefon . '%');
            })
            ->orderBy('activ', 'desc')
            ->orderBy('prod', 'asc')
            ->orderBy('nume', 'asc')
            ->simplePaginate(100);

        return view('angajati.index', compact('angajati', 'search_nume', 'search_telefon'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $angajati = Angajat::select('id', 'nume')
            ->where('id', '>', '3') // Se sare peste conturile de test Andrei Dima
            ->where('activ', 1)
            ->orderBy('nume', 'asc')
            ->get();

        return view('angajati.create', compact('angajati'));
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
        $angajat->produseOperatii()->sync($request->angajatProduseOperatii);

        return redirect('/angajati')->with('status', 'Angajatul "' . $angajat->nume . '" a fost adăugat cu succes!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Angajat  $angajat
     * @return \Illuminate\Http\Response
     */
    public function show(Angajat $angajat)
    {
        return view('angajati.show', compact('angajat'));
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
            ->where('id', '>', '3') // Se sare peste conturile de test Andrei Dima
            ->where('activ', 1)
            ->orderBy('nume', 'asc')
            ->get();

        return view('angajati.edit', compact('angajat', 'angajati'));
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
        // dd($request);
        $angajat->update($this->validateRequest($request, $angajat));
        $angajat->angajati_pontatori()->sync($request->angajat_pontatori);

        return redirect('/angajati')->with('status', 'Angajatul "' . $angajat->nume . '" a fost modificat cu succes!');
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
            'nume' => 'required|max:100',
            'telefon' => 'nullable|max:50',
            'cod_de_acces' => [
                'nullable',
                'min:8',
                'max:50',
                Rule::unique('App\Models\Angajat')->ignore($angajat),
            ],
            'sectia' => 'nullable|max:500',
            'firma' => 'required|max:500',
            'prod' => 'nullable|max:200',
            'ore_angajare' => 'required|numeric|between:1,12',
            'avans' => 'required|numeric|max:99999',
            'foaie_pontaj' => 'nullable|max:200',
            'limba_aplicatie' => 'required',
            'activ' => 'nullable|integer|between:0,1',

            'banca_angajat_nume' => 'nullable|max:200',
            'banca_angajat_cnp' => 'nullable|max:200',
            'banca_iban' => 'nullable|max:200',
            'banca_detalii_1' => 'nullable|max:200',
            'banca_detalii_2' => 'nullable|max:200',
        ]);
    }
}
