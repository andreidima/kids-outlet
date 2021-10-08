<?php

namespace App\Http\Controllers;

use App\Models\NormaLucrata;
use App\Models\Angajat;
use App\Models\ProdusOperatie;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class NormaLucrataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search_nume = \Request::get('search_nume');
        $search_data = \Request::get('search_data');

        $norme_lucrate = NormaLucrata::with('angajat', 'produs_operatie.produs')
            ->when($search_nume, function (Builder $query, $search_nume) {
                $query->whereHas('angajat', function (Builder $query) use ($search_nume) {
                    $query->where('nume', 'like', '%' . $search_nume . '%');
                });
            })
            ->when($search_data, function ($query, $search_data) {
                return $query->whereDate('created_at', '=', $search_data);
            })
            ->latest()
            ->simplePaginate(25);

        return view('norme_lucrate.index', compact('norme_lucrate', 'search_nume', 'search_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $angajati = Angajat::orderBy('nume')->get();

        session(['previous-url' => url()->previous()]);

        return view('norme_lucrate.create', compact('angajati'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $norma_lucrata = NormaLucrata::create($this->validateRequest($request));

        return redirect(session('previous-url'))->with('status', 'Norma Lucrată pentru numărul de fază "' . ($norma_lucrata->numar_de_faza ?? '') . '" a fost adăugată cu succes!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NormaLucrata  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function show(NormaLucrata $norma_lucrata)
    {
        return view('norme_lucrate.show', compact('norma_lucrata'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NormaLucrata  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function edit(NormaLucrata $norma_lucrata)
    {
        $angajati = Angajat::orderBy('nume')->get();

        return view('norme_lucrate.edit', compact('norma_lucrata', 'angajati'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NormaLucrata  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NormaLucrata $norma_lucrata)
    {
        $norma_lucrata->update($this->validateRequest($request));

        return redirect(session('previous-url'))->with('status', 'Norma Lucrată pentru numărul de fază "' . ($norma_lucrata->numar_de_faza ?? '') . '" a fost modificată cu succes!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NormaLucrata  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function destroy(NormaLucrata $norma_lucrata)
    {
        $norma_lucrata->delete();
        return back()->with('status', 'Norma Lucrată pentru numărul de fază "' . ($norma_lucrata->numar_de_faza ?? '') . '" a fost ștearsă cu succes!');
    }

    /**
     * Validate the request attributes.
     *
     * @return array
     */
    protected function validateRequest(Request $request)
    {
        return request()->validate([
            'angajat_id' => 'required',
            'numar_de_faza' => 'required|exists:produse_operatii' ,
            'cantitate' => 'required',
        ]);
    }

    /**
     * Afisare lunara
     *
     * @return array
     */
    protected function afisareLunar(Request $request)
    {
        $search_nume = \Request::get('search_nume');
        $search_data_inceput = \Request::get('search_data_inceput') ?? \Carbon\Carbon::now()->startOfWeek()->toDateString();
        $search_data_sfarsit = \Request::get('search_data_sfarsit') ?? \Carbon\Carbon::now()->endOfWeek()->toDateString();


        if (\Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput) > 35){
            return back()->with('error', 'Selectează te rog intervale mai mici de 35 de zile, pentru ca extragerea datelor din baza de date să fie eficientă!');
        }

        $angajati = Angajat::with(['norme_lucrate'=> function($query) use ($search_data_inceput, $search_data_sfarsit){
                $query->whereDate('created_at', '>=', $search_data_inceput)
                    ->whereDate('created_at', '<=', $search_data_sfarsit);
            }])
            ->when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            ->orderBy('nume')
            ->paginate(10);

        return view('norme_lucrate.index.lunar', compact('angajati', 'search_nume', 'search_data_inceput', 'search_data_sfarsit'));
    }
}
