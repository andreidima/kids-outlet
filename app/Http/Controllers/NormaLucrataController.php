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

        $norme_lucrate = NormaLucrata::with('angajat')
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NormeLucrate  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function show(NormeLucrate $norma_lucrata)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NormeLucrate  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function edit(NormeLucrate $norma_lucrata)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NormeLucrate  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NormeLucrate $norma_lucrata)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NormeLucrate  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function destroy(NormeLucrate $norma_lucrata)
    {
        //
    }
}
