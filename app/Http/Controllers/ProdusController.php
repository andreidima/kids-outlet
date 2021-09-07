<?php

namespace App\Http\Controllers;

use App\Models\Produs;
use Illuminate\Http\Request;

class ProdusController extends Controller
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

        $produse = Produs::
            when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            ->when($search_telefon, function ($query, $search_telefon) {
                return $query->where('telefon', 'like', '%' . $search_telefon . '%');
            })
            ->latest()
            ->simplePaginate(25);

        return view('produse.index', compact('produse', 'search_nume', 'search_telefon'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('produse.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $produs = Produs::create($this->validateRequest($request));

        return redirect('/produse')->with('status', 'Produsul "' . $produs->nume . '" a fost adăugat cu succes!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produs  $produs
     * @return \Illuminate\Http\Response
     */
    public function show(Produs $produs)
    {
        return view('produse.show', compact('produs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produs  $produs
     * @return \Illuminate\Http\Response
     */
    public function edit(Produs $produs)
    {
        return view('produse.edit', compact('produs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produs  $produs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Produs $produs)
    {
        $produs->update($this->validateRequest($request));

        return redirect('/produse')->with('status', 'Produsul "' . $produs->nume . '" a fost modificat cu succes!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produs  $produs
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produs $produs)
    {
        $produs->delete();
        return redirect('/produse')->with('status', 'Produsul "' . $produs->nume . '" a fost șters cu succes!');
    }

    /**
     * Validate the request attributes.
     *
     * @return array
     */
    protected function validateRequest(Request $request)
    {
        return request()->validate([
            'nume' => 'nullable|max:100',
            'client_pret' => 'nullable|numeric|between:0.00,99999.99',
            'cost_produs' => 'nullable|numeric|between:0.00,99999.99',
            'cantitate' => 'nullable|numeric|between:0,99999',
            'observatii' => 'nullable|max:1000',
        ]);
    }
}
