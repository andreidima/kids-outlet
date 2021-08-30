<?php

namespace App\Http\Controllers;

use App\Models\Pontaj;
use App\Models\Angajat;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;

class PontajController extends Controller
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

        $pontaje = Pontaj::
            when($search_nume, function (Builder $query, $search_nume) {
                $query->whereHas('angajat', function (Builder $query) use ($search_nume) {
                    $query->where('nume', 'like', '%' . $search_nume . '%');
                });
            })
            ->when($search_data, function ($query, $search_data) {
                return $query->whereDate('data', '=', $search_data);
            })
            ->latest()
            ->simplePaginate(25);

        return view('pontaje.index', compact('pontaje', 'search_nume', 'search_data'));
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

        return view('pontaje.create', compact('angajati'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pontaj = Pontaj::create($this->validateRequest($request));

        return redirect(session('previous-url'))->with('status', 'Pontajul pentru "' . $pontaj->angajat->nume ?? '' . '" a fost adăugat cu succes!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pontaj  $pontaj
     * @return \Illuminate\Http\Response
     */
    public function show(Pontaj $pontaj)
    {
        return view('pontaje.show', compact('pontaj'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pontaj  $pontaj
     * @return \Illuminate\Http\Response
     */
    public function edit(Pontaj $pontaj)
    {
        $angajati = Angajat::orderBy('nume')->get();

        return view('pontaje.edit', compact('pontaj', 'angajati'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pontaj  $pontaj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pontaj $pontaj)
    {
        $pontaj->update($this->validateRequest($request));

        return redirect(session('previous-url'))->with('status', 'Pontajul pentru "' . $pontaj->angajat->nume . '" a fost modificat cu succes!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pontaj  $pontaj
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pontaj $pontaj)
    {
        $pontaj->delete();
        return back()->with('status', 'Pontajul pentru "' . $pontaj->angajat->nume . '" a fost șters cu succes!');
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
            'data' => 'required',
            'ora_sosire' => 'nullable',
            'ora_plecare' => 'nullable'
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
        $search_data_inceput = \Request::get('search_data_inceput') ?? \Carbon\Carbon::now();
        $search_data_sfarsit = \Request::get('search_data_sfarsit') ?? \Carbon\Carbon::now();

        $pontaje = Pontaj::with('angajat')
            // ->select('id', 'angajat_id', 'data', 'ora_sosire', 'ora_plecare')
            ->when($search_nume, function (Builder $query, $search_nume) {
                $query->whereHas('angajat', function (Builder $query) use ($search_nume) {
                    $query->where('nume', 'like', '%' . $search_nume . '%');
                });
            })
            // ->whereYear('data', Carbon::parse($search_data)->year)
            // ->whereMonth('data', Carbon::parse($search_data)->month)
            ->whereDate('data', '>=', $search_data_inceput)
            ->whereDate('data', '<=', $search_data_sfarsit)
            ->get()
            ->sortBy('angajat.nume');

        // dd($pontaje);

        return view('pontaje.index.lunar', compact('pontaje', 'search_nume', 'search_data_inceput', 'search_data_sfarsit'));
    }
}
