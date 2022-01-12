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
        $norma_lucrata = NormaLucrata::make($this->validateRequest($request));

        $produs_operatie = ProdusOperatie::where('numar_de_faza', $request->numar_de_faza)->first();

        if (($produs_operatie->norma_efectuata + $request->cantitate) > $produs_operatie->norma){
            return back()->with('error', 'Cantitatea pe care doriți să o introduceți depășește norma totală pentru Faza "' . $request->numar_de_faza . '". Mai puteți adăuga maxim "' . ($produs_operatie->norma - $produs_operatie->norma_efectuata ?? '') . '"!');
        } else {
            $norma_lucrata->save();

            $produs_operatie->norma_efectuata += $request->cantitate;
            $produs_operatie->save();

            return redirect('norme-lucrate')->with('status', 'Norma Lucrată pentru angajatul "' . ($norma_lucrata->angajat->nume ?? '') . '" și numărul de fază "' . ($norma_lucrata->numar_de_faza ?? '') . '" a fost adăugată cu succes!');
        }
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
        $produs_operatie = ProdusOperatie::where('numar_de_faza', $norma_lucrata->numar_de_faza)->first();
        // Se verifica sa nu se depaseasca norma
        // din norma efectuata pentru produs_operatie, se scade toata norma lucrata veche, se adauga cantitatea noua din request, si se verifica cu norma stabilita pentru produs_operatie
        if (($produs_operatie->norma_efectuata - $norma_lucrata->cantitate + $request->cantitate) > $produs_operatie->norma){
            return back()->with('error', 'Cantitatea pe care doriți să o introduceți depășește norma totală pentru Faza "' . $norma_lucrata->numar_de_faza . '". Cantitatea maximă este "' . ($produs_operatie->norma - $produs_operatie->norma_efectuata + $norma_lucrata->cantitate ?? '') . '"!');
        } else {
            $produs_operatie->norma_efectuata = $produs_operatie->norma_efectuata - $norma_lucrata->cantitate + $request->cantitate;
            $produs_operatie->save();

            $norma_lucrata->cantitate = $request->cantitate;
            $norma_lucrata->save();

            return redirect('norme-lucrate')->with('status', 'Norma Lucrată pentru numărul de fază "' . ($norma_lucrata->numar_de_faza ?? '') . '" a fost modificată cu succes!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NormaLucrata  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function destroy(NormaLucrata $norma_lucrata)
    {
        if ($produs_operatie = ProdusOperatie::where('numar_de_faza', $norma_lucrata->numar_de_faza)->first()){
            $produs_operatie->norma_efectuata -= $norma_lucrata->cantitate;
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
            'angajat_id' => 'required',
            'numar_de_faza' => 'required|exists:produse_operatii' ,
            'cantitate' => 'required|integer|between:1,9999',
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
            ->with('norme_lucrate.produs_operatie')
            ->when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            ->orderBy('nume')
            ->paginate(10);

        // dd($angajati);

        return view('norme_lucrate.index.lunar', compact('angajati', 'search_nume', 'search_data_inceput', 'search_data_sfarsit'));
    }
}
