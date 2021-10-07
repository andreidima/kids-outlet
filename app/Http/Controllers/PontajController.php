<?php

namespace App\Http\Controllers;

use App\Models\Pontaj;
use App\Models\Angajat;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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

        $pontaje = Pontaj::with('angajat')
            ->when($search_nume, function (Builder $query, $search_nume) {
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
        $search_data_inceput = \Request::get('search_data_inceput') ?? \Carbon\Carbon::now()->startOfWeek()->toDateString();
        $search_data_sfarsit = \Request::get('search_data_sfarsit') ?? \Carbon\Carbon::now()->endOfWeek()->toDateString();


        if (\Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput) > 35){
            return back()->with('error', 'Selectează te rog intervale mai mici de 35 de zile, pentru ca extragerea datelor din baza de date să fie eficientă!');
        }

        // $pontaje = Pontaj::with('angajat')
        //     ->when($search_nume, function (Builder $query, $search_nume) {
        //         $query->whereHas('angajat', function (Builder $query) use ($search_nume) {
        //             $query->where('nume', 'like', '%' . $search_nume . '%');
        //         });
        //     })
        //     ->whereDate('data', '>=', $search_data_inceput)
        //     ->whereDate('data', '<=', $search_data_sfarsit)
        //     ->get()
        //     ->sortBy('angajat.nume');

        // $pontaje = Pontaj::
        //     when($search_nume, function (Builder $query, $search_nume) {
        //         $query->whereHas('angajat', function (Builder $query) use ($search_nume) {
        //             $query->where('nume', 'like', '%' . $search_nume . '%');
        //         });
        //     })
        //     ->whereDate('data', '>=', $search_data_inceput)
        //     ->whereDate('data', '<=', $search_data_sfarsit)
        //     ->join('angajati', 'angajati.id', '=', 'angajat_id')
        //     ->orderBy('angajati.nume')
        //     ->groupBy('angajat_id')
        //     ->paginate();

        // $pontaje = DB::table('pontaje')
        //     ->join('angajati', 'angajati.id', '=', 'angajat_id')
        //     ->select('pontaje.*', 'angajati.nume')
        //     ->when($search_nume, function (Builder $query, $search_nume) {
        //         $query->whereHas('angajat', function (Builder $query) use ($search_nume) {
        //             $query->where('nume', 'like', '%' . $search_nume . '%');
        //         });
        //     })
        //     ->whereDate('data', '>=', $search_data_inceput)
        //     ->whereDate('data', '<=', $search_data_sfarsit)
        //     // ->orderBy('angajati.nume')
        //     ->groupBy('pontaje.angajat_id')
        //     ->get();

        // dd($pontaje);

        $angajati = Angajat::with(['pontaj'=> function($query) use ($search_data_inceput, $search_data_sfarsit){
                $query->whereDate('data', '>=', $search_data_inceput)
                    ->whereDate('data', '<=', $search_data_sfarsit);
            }])
            ->when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            ->orderBy('nume')
            // ->groupBy('angajat_id')
            ->paginate(10);

        // dd($angajati);

        return view('pontaje.index.lunar', compact('angajati', 'search_nume', 'search_data_inceput', 'search_data_sfarsit'));
    }
}
