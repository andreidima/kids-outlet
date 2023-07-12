<?php

namespace App\Http\Controllers;

use App\Models\Produs;
use App\Models\ProdusOperatie;
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
        // dd($request->operatii);
        $this->validateRequest($request);
        $produs = Produs::create($request->only('nume', 'cantitate', 'sectia', 'activ'));
        if ($request->operatii){
            foreach ($request->operatii as $operatie_formular) {
                $operatie_produs = new ProdusOperatie;
                $operatie_produs->numar_de_faza = $operatie_formular[1];
                $operatie_produs->nume = $operatie_formular[2];
                $operatie_produs->timp = $operatie_formular[3];
                $operatie_produs->pret = $operatie_formular[4];
                $operatie_produs->pret_pe_minut = $operatie_formular[5];
                $operatie_produs->timp_total = $operatie_formular[6];
                $operatie_produs->norma = $operatie_formular[7];
                $operatie_produs->pret_100_pe_minut = $operatie_formular[8];
                $operatie_produs->pret_100_pe_faze = $operatie_formular[9];
                $operatie_produs->J = $operatie_formular[10];
                $produs->produse_operatii()->save($operatie_produs);
            }
        }

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
        $operatii = $produs->produse_operatii;

        return view('produse.edit', compact('produs', 'operatii'));
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
        // $produs->update($this->validateRequest($request));
        $this->validateRequest($request);
        $produs->update($request->only('nume', 'cantitate', 'sectia', 'activ'));
        if ($request->operatii){
            foreach ($request->operatii as $operatie_formular) {
                echo $operatie_formular[1] . '<br>';
                $produs_operatie = $produs->produse_operatii()->where('numar_de_faza', $operatie_formular[1])->first();
                if ($produs_operatie) {
                    $produs_operatie->nume = $operatie_formular[2];
                    $produs_operatie->timp = $operatie_formular[3];
                    $produs_operatie->pret = $operatie_formular[4];
                    $produs_operatie->pret_pe_minut = $operatie_formular[5];
                    $produs_operatie->timp_total = $operatie_formular[6];
                    $produs_operatie->norma = $operatie_formular[7];
                    $produs_operatie->pret_100_pe_minut = $operatie_formular[8];
                    $produs_operatie->pret_100_pe_faze = $operatie_formular[9];
                    $produs_operatie->J = $operatie_formular[10];
                    $produs_operatie->save();
                }
            }
        }

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
        if (count($produs->normeLucrate)) {
            return back()->with('error', 'Produsul "' . $produs->nume . '" nu poate fi șters pentru că are adăugate norme lucrate. Ștergeti mai întâi normele lucrate adăugate acestuia!');
        }

        $produs->produse_operatii()->delete();
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
            'nume' => 'required|max:100',
            'cantitate' => 'required|integer|min:0|max:9999',
            'sectia' => 'required|max:100',
            'activ' => '',
            // 'client_pret' => 'nullable|numeric|between:0.00,99999.99',
            // 'cost_produs' => 'nullable|numeric|between:0.00,99999.99',
            // 'cantitate' => 'nullable|numeric|between:0,99999',
            // 'observatii' => 'nullable|max:1000',
            'nr_operatii' => '',
            'xls' => '',
            'operatii' => 'required_with:xls',
            'operatii.*.1' => 'required|integer|between:0,300',
            'operatii.*.2' => 'required|max:300',
            'operatii.*.3' => 'required|numeric',
            'operatii.*.4' => 'required|numeric',
            'operatii.*.5' => 'required|numeric',
            'operatii.*.6' => 'required|numeric',
            'operatii.*.7' => 'required|numeric',
            'operatii.*.8' => 'required|numeric',
            'operatii.*.9' => 'required|numeric',
            'operatii.*.10' => 'required|numeric',
        ],
        [
            'operatii.required_with' => 'Generați mai întâi operațiile'
        ]
        );
    }

    public function duplica(Produs $produs)
    {
        $clone_produs = $produs->replicate();
        $clone_produs->activ = 0;
        $clone_produs->save();

        foreach ($produs->produse_operatii as $produs_operatie) {
            $clone_produs_operatie = $produs_operatie->replicate();
            $clone_produs_operatie->norma_totala = 0;
            $clone_produs_operatie->norma_totala_efectuata = 0;
            $clone_produs->produse_operatii()->save($clone_produs_operatie);
            // $clone_produs_operatie->save();
        }

        return back()->with('status', 'Produsul ' . $produs->nume . ' a fost duplicat cu success');
    }
}
