<?php

namespace App\Http\Controllers;

use App\Models\Produs;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AngajatAplicatieProdusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $angajat = $request->session()->get('angajat');
        if( // Conturile ce pot vedea produsele
            (($angajat->id ?? '') !== 4) // Mocanu Geanina
            && (($angajat->id ?? '') !== 162) // Toader Maria
            && (($angajat->id ?? '') !== 16) // Fodoroiu Geta
            && (($angajat->id ?? '') !== 231) // Gologus Maricica
            && (($angajat->id ?? '') !== 234) // Munteanu Genoveva
            && (($angajat->id ?? '') !== 124) // W Rampati Dewayalage Anoja Nilathi Sandamali
        ){
            return redirect('/aplicatie-angajati');
        }

        $produse = Produs::select('id', 'nume', 'activ')
            ->orderBy('activ', 'desc')->latest()->simplePaginate(25);

        return view('aplicatie_angajati/produse/index', compact('produse'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('aplicatie_angajati.angajati.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $angajat = Angajat::create($this->validateRequest($request));

        // return redirect('/aplicatie-angajati/angajati')->with('status', 'Angajatul "' . $angajat->nume . '" a fost adăugat cu succes!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Angajat  $angajat
     * @return \Illuminate\Http\Response
     */
    public function show(Angajat $angajat)
    {
        // return view('aplicatie_angajati.angajati.show', compact('angajat'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produs  $produs
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Produs $produs)
    {
        $angajat = $request->session()->get('angajat');
        if( // Conturile ce pot vedea produsele
            (($angajat->id ?? '') !== 4) // Mocanu Geanina id=4
            && (($angajat->id ?? '') !== 162) // Toader Maria id=162
            && (($angajat->id ?? '') !== 16) // Fodoroiu Geta
            && (($angajat->id ?? '') !== 231) // Gologus Maricica
            && (($angajat->id ?? '') !== 234) // Munteanu Genoveva
            && (($angajat->id ?? '') !== 124) // W Rampati Dewayalage Anoja Nilathi Sandamali
        ){
            return redirect('/aplicatie-angajati');
        }

        return view('aplicatie_angajati.produse.edit', compact('produs'));
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
        $angajat = $request->session()->get('angajat');
        if( // Conturile ce pot vedea produsele
            (($angajat->id ?? '') !== 4) // Mocanu Geanina id=4
            && (($angajat->id ?? '') !== 162) // Toader Maria id=162
            && (($angajat->id ?? '') !== 16) // Fodoroiu Geta
            && (($angajat->id ?? '') !== 231) // Gologus Maricica
            && (($angajat->id ?? '') !== 234) // Munteanu Genoveva
            && (($angajat->id ?? '') !== 124) // W Rampati Dewayalage Anoja Nilathi Sandamali
        ){
            return redirect('/aplicatie-angajati');
        }

        echo $request->cantitate . '<br><br>';
        foreach($produs->produse_operatii as $operatie){
            if ($request->cantitate < $operatie->norma_totala_efectuata){
                return back()->with('error', 'Nu puteti seta această cantitate. Deja sunt norme adăugate ce depășesc această cantitate. Verificați și ștergeti acele norme mai întâi!');
            }
        }

        $produs->update($this->validateRequest($request, $produs));

        return redirect('/aplicatie-angajati/produse')->with('status', 'Produseul "' . $produs->nume . '" a fost modificat cu succes!');
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
            'cantitate' => 'required|integer|min:0|max:99999',
            'activ' => 'nullable|integer|between:0,1'
        ]);
    }
}
