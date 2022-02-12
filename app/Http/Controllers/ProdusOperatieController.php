<?php

namespace App\Http\Controllers;

use App\Models\Produs;
use App\Models\ProdusOperatie;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProdusOperatieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search_nume = \Request::get('search_nume');

        $produse_operatii = ProdusOperatie::with('produs')
            ->when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            // ->when($search_telefon, function ($query, $search_telefon) {
            //     return $query->where('telefon', 'like', '%' . $search_telefon . '%');
            // })
            ->latest()
            ->simplePaginate(25);

        return view('produse_operatii.index', compact('produse_operatii', 'search_nume'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $produse = Produs::orderBy('nume')->get();

        $last_url = $request->last_url;

        return view('produse_operatii.create', compact('produse', 'last_url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $produs_operatie = ProdusOperatie::create($this->validateRequest($request));

        return redirect($request->last_url)->with('status', 'Operația „' . $produs_operatie->nume . '” pentru  produsul „' . ($produs_operatie->produs->nume ?? '') . '” a fost adăugată cu succes!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProdusOperatie  $produs_operatie
     * @return \Illuminate\Http\Response
     */
    public function show(ProdusOperatie $produs_operatie)
    {
        return view('produse_operatii.show', compact('produs_operatie'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProdusOperatie  $produs_operatie
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, ProdusOperatie $produs_operatie)
    {
        $produse = Produs::orderBy('nume')->get();

        $last_url = $request->last_url;

        return view('produse_operatii.edit', compact('produse', 'produs_operatie', 'last_url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProdusOperatie  $produs_operatie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProdusOperatie $produs_operatie)
    {
        $produs_operatie->update($this->validateRequest($request, $produs_operatie));

        return redirect($request->last_url)->with('status', 'Operația „' . $produs_operatie->nume . '” pentru  produsul „' . ($produs_operatie->produs->nume ?? '') . '” a fost modificată cu succes!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProdusOperatie  $produs_operatie
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProdusOperatie $produs_operatie)
    {
        if (!count($produs_operatie->norme_lucrate)) {
            $produs_operatie->delete();
            return back()->with('status', 'Operația „' . $produs_operatie->nume . '” pentru  produsul „' . ($produs_operatie->produs->nume ?? '') . '” a fost ștearsă cu succes!');
        } else {
            return back()->with('error',
                'Operația „' . $produs_operatie->nume . '” pentru  produsul „' . ($produs_operatie->produs->nume ?? '') .
                '” nu poate fi ștearsă pentru că are norme lucrate. Ștergeți mai întâi normele lucrate!');
        }
    }

    /**
     * Validate the request attributes.
     *
     * @return array
     */
    protected function validateRequest(Request $request, $produs_operatie = null)
    {
        return request()->validate(
            [
                'produs_id' => 'required',
                'nume' => 'nullable|max:100',
                'numar_de_faza' => [
                    'nullable',
                    'max:50',
                    Rule::unique('App\Models\ProdusOperatie')->where(function ($query) use ($request) {
                        $query->where('produs_id', $request->produs_id)
                            ->where('numar_de_faza', $request->numar_de_faza);
                    })->ignore($produs_operatie),
                ],
                'timp' => '',
                'pret' => 'nullable|numeric|between:0,9999|regex:/^\d*(\.\d{1,4})?$/',
                'pret_pe_minut' => 'nullable|numeric|between:0,9999|regex:/^\d*(\.\d{1,5})?$/',
                'timp_total' => 'nullable|numeric|between:0,9999|regex:/^\d*(\.\d{1,5})?$/',
                'norma' => 'nullable|numeric|between:0,9999|regex:/^\d*(\.\d{1,5})?$/',
                'pret_100_pe_minut' => 'nullable|numeric|between:0,9999|regex:/^\d*(\.\d{1,5})?$/',
                'pret_100_pe_faze' => 'nullable|numeric|between:0,9999|regex:/^\d*(\.\d{1,5})?$/',
                'J' => 'nullable|numeric|between:0,9999|regex:/^\d*(\.\d{1,5})?$/',
                'norma_totala' => 'nullable|numeric|between:0,99999',
                'norma_totala_efectuata' => 'nullable|numeric|between:0,99999',
                'observatii' => 'nullable|max:1000',
            ],
            [
                'pret.regex' => 'Prețul poate avea la partea zecimală maxim 5 cifre.',
                'pret_pe_minut.regex' => 'Prețul pe minut poate avea la partea zecimală maxim 5 cifre.',
                'timp_total.regex' => 'Timpul total poate avea la partea zecimală maxim 5 cifre.',
                'norma.regex' => 'Norma poate avea la partea zecimală maxim 5 cifre.',
                'pret_100_pe_minut.regex' => 'Prețul 100 pe minut poate avea la partea zecimală maxim 5 cifre.',
                'pret_100_pe_faze.regex' => 'Prețul 100 pe faze poate avea la partea zecimală maxim 5 cifre.',
                'J.regex' => 'J poate avea la partea zecimală maxim 5 cifre.',
            ]
        );
    }
}
