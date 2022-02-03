<?php

namespace App\Http\Controllers;

use App\Models\NormaLucrata;
use App\Models\Angajat;
use App\Models\Produs;
use App\Models\ProdusOperatie;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class NormaLucrataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $angajat = null, $data = null)
    {
        $search_nume = \Request::get('search_nume');
        $search_data = $data ?? \Request::get('search_data');

        $norme_lucrate = NormaLucrata::with('angajat', 'produs_operatie.produs')
            ->when($search_nume, function (Builder $query, $search_nume) {
                $query->whereHas('angajat', function (Builder $query) use ($search_nume) {
                    $query->where('nume', 'like', '%' . $search_nume . '%');
                });
            })
            ->when($angajat, function (Builder $query, $angajat) {
                $query->whereHas('angajat', function (Builder $query) use ($angajat) {
                    $query->where('id', $angajat);
                });
            })
            ->when($search_data, function ($query, $search_data) {
                return $query->whereDate('data', '=', $search_data);
            })
            ->latest()
            ->simplePaginate(25);

        $request->session()->forget('norme_lucrate_return_url');

        return view('norme_lucrate.index', compact('norme_lucrate', 'search_nume', 'search_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $angajati = Angajat::orderBy('nume')->get();
        $produse = Produs::orderBy('nume')->get();

        $request->session()->get('norme_lucrate_return_url') ?? $request->session()->put('norme_lucrate_return_url', url()->previous());

        return view('norme_lucrate.create', compact('angajati', 'produse'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $produs_operatie = ProdusOperatie::where('produs_id', $request->produs_id)->where('numar_de_faza', $request->numar_de_faza)->first();
        $produs_operatie->norma_totala_efectuata += $request->cantitate;
        $produs_operatie->save();

        $norma_lucrata = NormaLucrata::make($request->except('produs_id', 'numar_de_faza', 'date'));
        $norma_lucrata->produs_operatie_id = $produs_operatie->id;
        $norma_lucrata->save();

        return redirect($request->session()->get('norme_lucrate_return_url') ?? ('/norme-lucrate'))
            ->with('status', 'Norma Lucrată pentru angajatul "' . ($norma_lucrata->angajat->nume ?? '') . '" a fost adăugată cu succes!');
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
    public function edit(Request $request, NormaLucrata $norma_lucrata)
    {
        $angajati = Angajat::orderBy('nume')->get();
        $produse = Produs::orderBy('nume')->get();

        $request->session()->get('norme_lucrate_return_url') ?? $request->session()->put('norme_lucrate_return_url', url()->previous());

        return view('norme_lucrate.edit', compact('norma_lucrata', 'angajati', 'produse'));
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
        $this->validateRequest($request);

        $produs_operatie = ProdusOperatie::where('produs_id', $request->produs_id)->where('numar_de_faza', $request->numar_de_faza)->first();
        $produs_operatie->norma_totala_efectuata += $request->cantitate - $norma_lucrata->cantitate;
        $produs_operatie->save();

        $norma_lucrata->data = $request->data;
        $norma_lucrata->produs_operatie_id = $produs_operatie->id;
        $norma_lucrata->cantitate = $request->cantitate;
        $norma_lucrata->save();

        return redirect($request->session()->get('norme_lucrate_return_url') ?? ('/norme-lucrate'))
            ->with('status', 'Norma Lucrată pentru angajatul "' . ($norma_lucrata->angajat->nume ?? '') . '" a fost modificată cu succes!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NormaLucrata  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, NormaLucrata $norma_lucrata)
    {
        if ($produs_operatie = $norma_lucrata->produs_operatie){
            $produs_operatie->norma_totala_efectuata -= $norma_lucrata->cantitate;
            $produs_operatie->save();
        }

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
            'angajat_id' => ($request->_method !== "PATCH") ? 'required' : '',
            'data' => 'required',
            'produs_id' => 'required',
            'numar_de_faza' => [
                'required',
                Rule::exists('produse_operatii', 'numar_de_faza')
                ->where('produs_id', $request->produs_id),
            ],
            'cantitate' => ['required', 'integer', 'between:1,9999',
                // function ($attribute, $value, $fail) use ($request) {
                //     $produs_operatie = ProdusOperatie::where('produs_id', $request->produs_id)->where('numar_de_faza', $request->numar_de_faza)->first();
                //     if($produs_operatie){
                //         if (($request->_method !== "PATCH") &&
                //             (($produs_operatie->norma_totala_efectuata + $request->cantitate) > $produs_operatie->norma_totala))
                //         {
                //             $fail('Cantitatea pe care doriți să o introduceți depășește norma totală pentru Faza "' . $request->numar_de_faza .
                //                 '". Mai puteți adăuga maxim "' . ($produs_operatie->norma_totala - $produs_operatie->norma_totala_efectuata ?? '') . '"!');
                //         } else {
                //         }
                //     }
                // },
            ],
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
        $search_data_sfarsit = \Request::get('search_data_sfarsit') ?? \Carbon\Carbon::parse($search_data_inceput)->addDays(4)->toDateString();

        if (\Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput) > 35){
            return back()->with('error', 'Selectează te rog intervale mai mici de 35 de zile, pentru ca extragerea datelor din baza de date să fie eficientă!');
        }

        switch ($request->input('action')) {
            case 'saptamana_anterioara':
                    $search_data_inceput = \Carbon\Carbon::parse($search_data_inceput)->subDays(7)->startOfWeek()->toDateString();
                    $search_data_sfarsit = \Carbon\Carbon::parse($search_data_inceput)->addDays(4)->toDateString();
                break;
            case 'saptamana_urmatoare':
                    $search_data_inceput = \Carbon\Carbon::parse($search_data_sfarsit)->addDays(7)->startOfWeek()->toDateString();
                    $search_data_sfarsit = \Carbon\Carbon::parse($search_data_inceput)->addDays(4)->toDateString();
                break;
        }

        $angajati = Angajat::with(['norme_lucrate'=> function($query) use ($search_data_inceput, $search_data_sfarsit){
                $query->whereDate('created_at', '>=', $search_data_inceput)
                    ->whereDate('created_at', '<=', $search_data_sfarsit);
            }])
            ->with('norme_lucrate.produs_operatie')
            ->when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            ->where('id', '>', 3) // Conturile de angajat pentru Andrei Dima
            ->orderBy('nume')
            ->paginate(10);
            // ->get();

        // dd($angajati);

        return view('norme_lucrate.index.lunar', compact('angajati', 'search_nume', 'search_data_inceput', 'search_data_sfarsit'));
    }
}
