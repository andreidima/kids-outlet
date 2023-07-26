<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Avans;
use App\Models\Angajat;
use \Carbon\Carbon;

class AvansController extends Controller
{
    public function index(Request $request){
        $searchLuna = $request->searchLuna ?? Carbon::now()->isoFormat('MM');
        $searchAn = $request->searchAn ?? Carbon::now()->isoFormat('YYYY');

        $request->validate(['searchLuna' => 'numeric|between:1,12', 'searchAn' => 'numeric|between:2023,2040']);

        $searchData = Carbon::today();
        $searchData->day = 1;
        $searchData->month = $searchLuna;
        $searchData->year = $searchAn;

        // Se verifica daca sunt generate avansurile pe luna cautata, daca nu se creaza acum si se salveaza in DB
        $angajati = Angajat::select('id')->where('activ', 1)
                ->with(['avansuri' => function($query) use ($searchData){
                    $query->whereDate('data', $searchData);
                }])
                ->get();
        foreach ($angajati as $angajat){
            if ($angajat->avansuri->count() === 0){
                $avans = Avans::create(['angajat_id' => $angajat->id, 'suma' => 0, 'data' => $searchData]);
            }
        }

        // Daca se apasa pe butorunl „calculeazaAutomatAvansurile”, se genereaza avansurile si se salveaza in baza de date
        if ($request->input('action') === 'calculeazaAutomatAvansurile'){
            $angajati = Angajat::where('activ', 1)
                ->with(['avansuri' => function($query) use ($searchData){
                    $query->whereDate('data', $searchData);
                }])
                ->with(['pontaj' => function($query) use ($searchData){
                    $query->whereMonth('data', $searchData)
                        ->whereYear('data', $searchData);
                }])
                ->get();

            foreach ($angajati as $angajat){
                $zilePontate = $angajat->pontaj->whereIn('concediu', [0,1,2,3])->count();

                if ($zilePontate >= 10){
                    $angajat->avansuri->first()->update(['suma' => $angajat->avans]);
                } else if ($zilePontate >= 7){
                    $angajat->avansuri->first()->update(['suma' => 300]);
                } else{
                    $angajat->avansuri->first()->update(['suma' => 0]);
                }

                // echo ('Nume: ' . $angajat->nume . " <br>Zile pontate = " . $zilePontate);
                // echo "<br><br>";
            }
        }

        $angajati = Angajat::where('activ', 1)
            ->with(['avansuri'=> function($query) use ($searchData){
                $query->whereDate('data', $searchData);
            }])
            ->orderBy('prod')
            ->orderBy('nume')
            ->get();

        return view('avansuri.index', compact('angajati', 'searchData', 'searchLuna', 'searchAn'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function axiosActualizareSuma(Request $request)
    {
        switch ($_GET['request']) {
            case 'actualizareSuma':
                // $avans = Avans::where('id', $request->avansId)->first()->get();
                $avans = Avans::find($request->avansId);
                $avans->suma = $request->avansSuma;
                $avans->save();

                return response()->json([
                    'raspuns' => "Actualizat",
                    'avansId' => $avans->id,
                ]);
            break;
            default:
                break;
        }
    }
}
