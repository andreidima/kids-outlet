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
            when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            ->when($search_telefon, function ($query, $search_telefon) {
                return $query->where('telefon', 'like', '%' . $search_telefon . '%');
            })
            ->latest()
            ->simplePaginate(25);

        return view('angajati.index', compact('angajati', 'search_nume', 'search_telefon'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('angajati.create');
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
        return view('angajati.edit', compact('angajat'));
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
        $angajat->delete();
        return redirect('/angajati')->with('status', 'Angajatul "' . $angajat->nume . '" a fost șters cu succes!');
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
            'cod_de_acces' => [
                'nullable',
                'max:50',
                Rule::unique('App\Models\Angajat')->ignore($angajat),
            ]
        ]);
    }
}