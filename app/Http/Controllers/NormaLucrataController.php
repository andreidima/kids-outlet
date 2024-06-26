<?php

namespace App\Http\Controllers;

use App\Models\NormaLucrata;
use App\Models\Angajat;
use App\Models\Produs;
use App\Models\ProdusOperatie;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;

use Illuminate\Support\Facades\Response;


use Carbon\Carbon;

class NormaLucrataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Angajat $angajat = null, $data = null)
    {
        // $search_nume = $angajat ? Angajat::find($angajat)->nume : \Request::get('search_nume');
        $search_nume = $angajat->nume ?? \Request::get('search_nume');
        $search_data = $data ? ($data . ',' . $data) : \Request::get('search_data');
        $search_produs_id = \Request::get('search_produs_id');
        $search_numar_de_faza = \Request::get('search_numar_de_faza');

        $norme_lucrate = NormaLucrata::with('angajat', 'produs_operatie.produs')
            ->when($search_nume, function (Builder $query, $search_nume) {
                $query->whereHas('angajat', function (Builder $query) use ($search_nume) {
                    $query->where('nume', 'like', '%' . $search_nume . '%');
                });
            })
            // ->when($search_produs_id, function (Builder $query, $search_produs_id) {
            //     $query->whereHas('produs_operatie', function (Builder $query) use ($search_produs_id) {
            //         $query->where('produs_id', $search_produs_id);
            //     });
            // })
            // ->when($search_numar_de_faza, function (Builder $query, $search_numar_de_faza) {
            //     $query->whereHas('produs_operatie', function (Builder $query) use ($search_numar_de_faza) {
            //         $query->where('numar_de_faza', $search_numar_de_faza);
            //     });
            // })
            ->whereHas('produs_operatie', function (Builder $query) use ($search_produs_id, $search_numar_de_faza) {
                $query
                    ->when($search_produs_id, function ($query, $search_produs_id) {
                        return $query->where('produs_id', $search_produs_id);
                    })
                    ->when($search_numar_de_faza, function ($query, $search_numar_de_faza) {
                        return $query->where('numar_de_faza', $search_numar_de_faza);
                    });
            })
            ->when($search_data, function ($query, $search_data) {
                $search_data = explode(',', $search_data);
                $search_data[0] = date($search_data[0]);
                // $search_data[1] = date($search_data[1]);
                // dd(gettype($search_data[0]));
                return $query->whereBetween('data', $search_data);
            })
            ->latest()
            ->simplePaginate(25);

        $produse = Produs::latest()->get();

        $request->session()->forget('norme_lucrate_return_url');
        $request->session()->get('norme_lucrate_afisare_tabelara_return_url') ?? $request->session()->put('norme_lucrate_afisare_tabelara_return_url', url()->previous());

        return view('norme_lucrate.index', compact('norme_lucrate', 'produse', 'search_nume', 'search_produs_id', 'search_numar_de_faza', 'search_data', 'angajat'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $angajat_id = null, $data = null)
    {
        // Id = 4, norme lucrate, nu poate umbla la actiuni
        if (auth()->user()->id == 4){
            return back();
        }

        $angajati = Angajat::orderBy('nume')->get();
        $produse = Produs::orderBy('nume')->get();

        $norma_lucrata = new NormaLucrata;
        $norma_lucrata->angajat_id = $angajat_id;
        $norma_lucrata->data = $data;

        $request->session()->get('norme_lucrate_return_url') ?? $request->session()->put('norme_lucrate_return_url', url()->previous());

        return view('norme_lucrate.create', compact('norma_lucrata', 'angajati', 'produse', 'angajat_id', 'data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Id = 4, norme lucrate, nu poate umbla la actiuni
        if (auth()->user()->id == 4){
            return back();
        }

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
        // Id = 4, norme lucrate, nu poate umbla la actiuni
        if (auth()->user()->id == 4){
            return back();
        }

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
        // Id = 4, norme lucrate, nu poate umbla la actiuni
        if (auth()->user()->id == 4){
            return back();
        }

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
        // Id = 4, norme lucrate, nu poate umbla la actiuni
        if (auth()->user()->id == 4){
            return back();
        }

        if ($norma_lucrata->produs_operatie){
            $norma_lucrata->produs_operatie->norma_totala_efectuata -= $norma_lucrata->cantitate;
            $norma_lucrata->produs_operatie->update();
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
        $search_angajat_id = \Request::get('search_angajat_id');
        $search_nume = \Request::get('search_nume');
        (!isset($search_nume) && isset($search_angajat_id)) ? ($search_nume = Angajat::find($search_angajat_id)->nume) : '';

        $search_data_inceput = \Request::get('search_data_inceput') ?? \Carbon\Carbon::now()->startOfWeek()->toDateString();
        $search_data_sfarsit = \Request::get('search_data_sfarsit') ?? \Carbon\Carbon::parse($search_data_inceput)->addDays(4)->toDateString();

        if (\Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput) > 65){
            return back()->with('error', 'Selectează te rog intervale mai mici de 65 de zile, pentru ca extragerea datelor din baza de date să fie eficientă!');
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

        $angajati_in_search = Angajat::select('id', 'nume')->where('id', '>', 3)->orderBy('nume')->get();

        $angajati = Angajat::with(['norme_lucrate'=> function($query) use ($search_data_inceput, $search_data_sfarsit){
                $query
                    ->with('produs_operatie.produs')
                    ->whereDate('data', '>=', $search_data_inceput)
                    ->whereDate('data', '<=', $search_data_sfarsit);
            }])
            ->with(['pontaj'=> function($query) use ($search_data_inceput, $search_data_sfarsit){
                $query->whereDate('data', '>=', $search_data_inceput)
                    ->whereDate('data', '<=', $search_data_sfarsit);
                    // ->where('concediu', '>', 0); // daca este 0, inseamna ca nu a fost in concediu
                }])
            // ->with('norme_lucrate.produs_operatie.produs')
            ->when($search_nume, function ($query, $search_nume) {
                $cuvinte_in_nume = explode(' ', $search_nume);
                foreach($cuvinte_in_nume as $cuvant){
                    $query->where('nume', 'like', '%' . $cuvant . '%');
                }
                return $query;
                })
            ->where('activ', 1) // Contul este activ
            ->where('id', '>', 3) // Conturile de angajat pentru Andrei Dima
            ->orderBy('prod')
            ->orderBy('nume')
            ->get();


        // Comentat si schimbat la 15.07.2023, pentru ca dura foarte mult, erau multe operatiuni asupra bazei de date
        // $produse = Produs::whereHas('produse_operatii', function ($query) use ($search_data_inceput, $search_data_sfarsit){
        //         return $query->whereHas('norme_lucrate', function ($query) use ($search_data_inceput, $search_data_sfarsit){
        //             return $query->whereDate('data', '>=', $search_data_inceput)
        //                 ->whereDate('data', '<=', $search_data_sfarsit);
        //         });
        //     })
        //     ->get();
        $produseIds = [];
        foreach ($angajati as $angajat){
            foreach ($angajat->norme_lucrate as $norma_lucrata) {
                if (!in_array( $norma_lucrata->produs_operatie->produs->id, $produseIds,)){
                    array_push($produseIds, $norma_lucrata->produs_operatie->produs->id);
                }
            }
        }
        $produse = Produs::whereIn('id', $produseIds)->get();

        switch ($request->input('action')) {
            case 'export_excel':
                $salariul_minim_pe_economie = intval(\App\Models\Variabila::where('variabila', 'salariul_minim_pe_economie')->value('valoare'));

                $zile_nelucratoare = DB::table('zile_nelucratoare')->whereDate('data', '>=', $search_data_inceput)->whereDate('data', '<=', $search_data_sfarsit)->pluck('data')->all();
                $numar_de_zile_lucratoare = 0;
                for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++){
                    if(
                            (\Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isWeekday())
                            &&
                            !in_array(\Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString(), $zile_nelucratoare)
                        ){
                        $numar_de_zile_lucratoare ++;
                    }
                }

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                // $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


                $sheet->setCellValue('A1', 'Norme lucrate - ' . Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') . ' - ' . Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY'));
                $sheet->getStyle('A1')->getFont()->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // $sheet->setCellValue('A2', Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') . ' - ' . Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY'));
                // $sheet->getStyle('A2')->getFont()->setSize(14);

                $sheet->setCellValue('A4', 'Nr.');
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->setCellValue('B4', 'Nume Prenume');
                $sheet->getColumnDimension('B')->setAutoSize(true);
                foreach ($produse as $index=>$produs){
                    $sheet->setCellValueByColumnAndRow(($index+3), 4 , str_replace(" ","\n",$produs->nume));
                    $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+3) . '4')->getAlignment()->setWrapText(true);
                    $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+3), 4)->getColumn())->setWidth(10);
                }
                isset($index) ? '' : ($index = 0); // Daca nu exista nici un produs de afisat, se da automat o valoare indexului, pentru a nu genera eroare

                $sheet->setCellValueByColumnAndRow(($index+4), 4 , 'REALIZAT');
                $sheet->setCellValueByColumnAndRow(($index+5), 4 , 'AVANS');
                $sheet->setCellValueByColumnAndRow(($index+6), 4 , 'CO');
                $sheet->setCellValueByColumnAndRow(($index+7), 4 , 'MEDICALE');
                $sheet->setCellValueByColumnAndRow(($index+8), 4 , 'SALARIU DE BAZA');
                $sheet->setCellValueByColumnAndRow(($index+9), 4 , 'PUS');
                $sheet->setCellValueByColumnAndRow(($index+10), 4 , 'REALIZAT TOTAL');
                $sheet->setCellValueByColumnAndRow(($index+11), 4 , 'LICHIDARE');

                $rand = 5;

                // $angajati->sortBy('prod');
                foreach ($angajati->groupby('prod') as $angajati_per_prod){

                    if ($angajati_per_prod->first()->prod){
                        $sheet->setCellValue('A' . $rand, 'Prod ' . $angajati_per_prod->first()->prod);
                    } else {
                        $sheet->setCellValue('A' . $rand, 'Prod ?');
                    }

                    $rand ++;
                    $rand_initial = $rand;

                    $nr_crt_angajat = 1;

                    foreach ($angajati_per_prod as $angajat){
                        // $timp_total = Carbon::today();

                        $sheet->setCellValue('A' . $rand, $nr_crt_angajat);
                        $sheet->setCellValue('B' . $rand, $angajat->nume);

                        $suma_totala_formula = '=';
                        foreach ($produse as $index=>$produs){
                            $suma = 0;
                            foreach ($produs->produse_operatii as $produs_operatie){
                                foreach ($angajat->norme_lucrate->where('produs_operatie_id', $produs_operatie->id) as $norma_lucrata){
                                    $suma += $norma_lucrata->cantitate * $produs_operatie->pret;
                                }
                            }
                            if ($suma > 0){
                                $sheet->setCellValueByColumnAndRow(($index+3), $rand , $suma);
                            }
                            $suma_totala_formula .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+3) . $rand . '+';
                        }

                        // Stergerea ultimului „+” din formula
                        $suma_totala_formula = substr($suma_totala_formula, 0, -1);

                        // REALIZAT
                        $sheet->setCellValueByColumnAndRow(($index+4), $rand , $suma_totala_formula);
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+4), $rand)->getColumn())->setAutoSize(true);
// dd($angajati->where('id', 4)->first()->pontaj->whereIn('concediu', [0,1,2,3])->count());
                        // AVANS
                        if (isset($angajat->avans)){
                            // $sheet->setCellValueByColumnAndRow(($index+5), $rand , $angajat->avans);

                            // Zile pontate
                            $zilePontate = $angajat->pontaj->whereIn('concediu', [0,1,2,3])->count();
                            // Avans platit
                            if ($zilePontate >= 10){
                                $sheet->setCellValueByColumnAndRow(($index+5), $rand , $avansPlatit = $angajat->avans);
                            } else if ($zilePontate >= 7){
                                $sheet->setCellValueByColumnAndRow(($index+5), $rand , $avansPlatit = 300);
                            } else{
                                $sheet->setCellValueByColumnAndRow(($index+5), $rand , $avansPlatit = 0);
                            }
                        }
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+5), $rand)->getColumn())->setAutoSize(true);

                        // CO
                        // MEDICALE
                        $zile_concediu_medical = 0;
                        $zile_concediu_de_odihna = 0;
                        foreach($angajat->pontaj as $pontaj){
                            if ($pontaj->concediu === 1){
                                $zile_concediu_medical ++;
                            }else if ($pontaj->concediu === 2){
                                $zile_concediu_de_odihna ++;
                            }
                        }

                        if ($zile_concediu_de_odihna > 0){
                            $sheet->setCellValueByColumnAndRow(($index+6), $rand , '=' . $salariul_minim_pe_economie . '/' . $numar_de_zile_lucratoare . '*' . $zile_concediu_de_odihna);
                        }
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+6), $rand)->getColumn())->setAutoSize(true);
                        if ($zile_concediu_medical > 0){
                            $sheet->setCellValueByColumnAndRow(($index+7), $rand , '=' . $salariul_minim_pe_economie . '/' . $numar_de_zile_lucratoare . '*' . $zile_concediu_medical . '*0.75');
                        }
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+7), $rand)->getColumn())->setAutoSize(true);

                        // SALARIU DE BAZA
                        $sheet->setCellValueByColumnAndRow(($index+8), $rand , '=' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+10) . $rand);
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+8), $rand)->getColumn())->setAutoSize(true);

                        // PUS
                        $sheet->setCellValueByColumnAndRow(($index+9), $rand , '=' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+8) . $rand . '-' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+10) . $rand);
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+9), $rand)->getColumn())->setAutoSize(true);

                        // REALIZAT TOTAL
                        $sheet->setCellValueByColumnAndRow(($index+10), $rand , '=' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+4) . $rand . '+' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+6) . $rand . '+' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+7) . $rand);
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+10), $rand)->getColumn())->setAutoSize(true);

                        // LICHIDARE
                        $sheet->setCellValueByColumnAndRow(($index+11), $rand , '=' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+8) . $rand . '-' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+5) . $rand);
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+11), $rand)->getColumn())->setAutoSize(true);


                        $rand ++;
                        $nr_crt_angajat ++;
                    }


                    // CALCUL TOTALURI
                    // REALIZAT
                    $sheet->setCellValueByColumnAndRow(($index+4), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+4) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+4) . ($rand-1) . ')');
                    // AVANS
                    $sheet->setCellValueByColumnAndRow(($index+5), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+5) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+5) . ($rand-1) . ')');
                    // CO
                    $sheet->setCellValueByColumnAndRow(($index+6), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+6) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+6) . ($rand-1) . ')');
                    // MEDICALE
                    $sheet->setCellValueByColumnAndRow(($index+7), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+7) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+7) . ($rand-1) . ')');
                    // SALARIU DE BAZA
                    $sheet->setCellValueByColumnAndRow(($index+8), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+8) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+8) . ($rand-1) . ')');
                    $sheet->getStyle(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+8) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+8) . ($rand-1)
                        )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFfe5858');
                    // PUS
                    $sheet->setCellValueByColumnAndRow(($index+9), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+9) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+9) . ($rand-1) . ')');
                    // REALIZAT TOTAL
                    $sheet->setCellValueByColumnAndRow(($index+10), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+10) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+10) . ($rand-1) . ')');
                    $sheet->getStyle(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+10) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+10) . ($rand-1)
                        )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('CC8ccd8c');
                    // LICHIDARE
                    $sheet->setCellValueByColumnAndRow(($index+11), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+11) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+11) . ($rand-1) . ')');
                    // Schimbare culoare la totaluri in rosu
                    $sheet->getStyle(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+4) . $rand . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+11) . $rand
                        )->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);


                    $rand += 2;
                }

                // Se parcug toate coloanele si se stabileste latimea AUTO
                foreach ($sheet->getColumnIterator() as $column) {
                    // $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                // S-au parcurs coloanele, avem indexul ultimei coloane, se pot aplica functii acum
                $sheet->mergeCells('A1:' . $column->getColumnIndex() . '1');
                $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getFont()->setBold(true);
                // $sheet->getStyle('A4:' . $column->getColumnIndex() . $rand)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                // Aliniere numere la dreapta
                // $sheet->getStyle('C6:' . $column->getColumnIndex() . $rand)->getAlignment()->setHorizontal('right');

                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="Raport Norme lucrate.xlsx"');
                $writer->save('php://output');
                exit();

                break;
            case 'exportExcelAvansuri':
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                // $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


                $sheet->setCellValue('A1', 'Avansuri - ' . Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') . ' - ' . Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY'));
                $sheet->getStyle('A1')->getFont()->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->setCellValue('A4', 'Nr.');
                // $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->setCellValue('B4', 'Nume Prenume');
                $sheet->getColumnDimension('B')->setAutoSize(true);

                $sheet->setCellValueByColumnAndRow((3), 4 , 'AVANS ÎN BAZA DE DATE');
                $sheet->setCellValueByColumnAndRow((4), 4 , 'ZILE PONTATE (inclusiv medical sau CO)');
                $sheet->setCellValueByColumnAndRow((5), 4 , 'AVANS DE PLĂTIT');
                $sheet->setCellValueByColumnAndRow((6), 4 , 'BANCĂ');
                $sheet->setCellValueByColumnAndRow((7), 4 , 'MÂNĂ');
                $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . '4')->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4) . '4')->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . '4')->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6) . '4')->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . '4')->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension($sheet->getCellByColumnAndRow((4), 4)->getColumn())->setWidth(10);
                $sheet->getColumnDimension($sheet->getCellByColumnAndRow((4), 4)->getColumn())->setWidth(10);
                $sheet->getColumnDimension($sheet->getCellByColumnAndRow((5), 4)->getColumn())->setWidth(10);
                $sheet->getColumnDimension($sheet->getCellByColumnAndRow((6), 4)->getColumn())->setWidth(10);
                $sheet->getColumnDimension($sheet->getCellByColumnAndRow((7), 4)->getColumn())->setWidth(10);

                $rand = 5;

                $formulaTotalAvansDePlatit = "=";
                $formulaTotalPlataPrinBanca = "=";
                $formulaTotalPlanaInMana = "=";

                foreach ($angajati->groupby('prod') as $angajati_per_prod){

                    if ($angajati_per_prod->first()->prod){
                        $sheet->setCellValue('A' . $rand, 'Prod ' . $angajati_per_prod->first()->prod);
                    } else {
                        $sheet->setCellValue('A' . $rand, 'Prod ?');
                    }

                    $rand ++;
                    $rand_initial = $rand;

                    $nr_crt_angajat = 1;

                    foreach ($angajati_per_prod as $angajat){
                        $sheet->setCellValue('A' . $rand, $nr_crt_angajat);
                        $sheet->setCellValue('B' . $rand, $angajat->nume);

                        // AVANS
                        if (isset($angajat->avans)){
                            $sheet->setCellValueByColumnAndRow((3), $rand , $angajat->avans);
                        }
                        // $sheet->getColumnDimension($sheet->getCellByColumnAndRow((3), $rand)->getColumn())->setAutoSize(true);

                        // Zile pontate
                        $sheet->setCellValueByColumnAndRow((4), $rand , $zilePontate = $angajat->pontaj->whereIn('concediu', [0,1,2,3])->count());

                        // Avans de platit
                        if ($zilePontate > 10){
                            $sheet->setCellValueByColumnAndRow((5), $rand , $avansDePlatit = $angajat->avans);
                        } else if ($zilePontate >= 7){
                            $sheet->setCellValueByColumnAndRow((5), $rand , $avansDePlatit = 300);
                        } else{
                            $sheet->setCellValueByColumnAndRow((5), $rand , $avansDePlatit = 0);
                        }

                        // Mod de plata
                        if ($angajat->firma){
                            if (($angajat->firma === "Petit Atelier S.R.L.") || ($angajat->firma === "Mate Andy Style") || ($angajat->firma === "Bensar S.R.L.")){ // plata prin banca
                                $sheet->setCellValueByColumnAndRow((6), $rand , $avansDePlatit);
                            } else{ // plata in mana
                                $sheet->setCellValueByColumnAndRow((7), $rand , $avansDePlatit);
                            }
                        }


                        $rand ++;
                        $nr_crt_angajat ++;
                    }

                    // CALCUL TOTALURI
                    // AVANS
                    $sheet->setCellValueByColumnAndRow((3), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . ($rand-1) . ')');
                    // AVANS DE PLATIT
                    $sheet->setCellValueByColumnAndRow((5), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . ($rand-1) . ')');
                    $formulaTotalAvansDePlatit .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . $rand . '+';
                    // Pata prin banca
                    $sheet->setCellValueByColumnAndRow((6), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6) . ($rand-1) . ')');
                    $formulaTotalPlataPrinBanca .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6) . $rand . '+';
                    // Pata in mana
                    $sheet->setCellValueByColumnAndRow((7), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . ($rand-1) . ')');
                    $formulaTotalPlanaInMana .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . $rand . '+';

                    // Schimbare culoare la totaluri in rosu
                    $sheet->getStyle(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . $rand
                        )->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

                    $rand += 2;
                }

                $rand += 1;

                $sheet->setCellValue('B' . $rand, 'TOTAL GENERAL');
                $sheet->getStyle('B' . $rand)->getAlignment()->setHorizontal('right');

                $sheet->setCellValue('E' . $rand, substr_replace($formulaTotalAvansDePlatit ,"", -1));
                $sheet->setCellValue('F' . $rand, substr_replace($formulaTotalPlataPrinBanca ,"", -1));
                $sheet->setCellValue('G' . $rand, substr_replace($formulaTotalPlanaInMana ,"", -1));
                // Schimbare culoare la totaluri in rosu
                $sheet->getStyle(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand . ':' .
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . $rand
                    )->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                // Set bold totaluri generale
                $sheet->getStyle('A' . $rand . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . $rand)->getFont()->setBold(true);

                $rand += 3;
                // Informare
                $sheet->setCellValue('A' . $rand++, 'AVANS DE PLĂTIT se calculeaza astfel:');
                $sheet->setCellValue('B' . $rand++, 'zilePontate > 10 - avansul se plătește integral');
                $sheet->setCellValue('B' . $rand++, 'zilePontate între 7 și 10 - avansul se plătește 300');
                $sheet->setCellValue('B' . $rand++, 'zilePontate < 7 - avansul se plătește 0');

                $rand += 1;
                // Informare
                $sheet->setCellValue('A' . $rand++, 'Plata prin banca: Petit Atelier S.R.L., Mate Andy Style, Bensar S.R.L.');

                // Se parcug toate coloanele si se stabileste latimea AUTO
                foreach ($sheet->getColumnIterator() as $column) {
                    // $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                // S-au parcurs coloanele, avem indexul ultimei coloane, se pot aplica functii acum
                $sheet->mergeCells('A1:' . $column->getColumnIndex() . '1');
                $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getFont()->setBold(true);

                // $sheet->getStyle('A4:' . $column->getColumnIndex() . $rand)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="Avansuri.xlsx"');
                $writer->save('php://output');
                exit();

                break;
            case 'exportExcelBancaBt':
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                // $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                $sheet->setCellValue('A1', 'NRCRT');
                $sheet->setCellValue('B1', 'SALARIAT');
                $sheet->setCellValue('C1', 'CNP');
                $sheet->setCellValue('D1', 'SUMA');
                $sheet->setCellValue('E1', 'IBAN');
                $sheet->setCellValue('F1', 'EXPLICATIE');

                $rand = 2;

                $nrCrt = 1;
                foreach ($angajati->whereIn('firma', ['Bensar S.R.L.', 'Petit Atelier S.R.L.'])->sortBy('banca_angajat_nume') as $index=>$angajat){
                    $sheet->setCellValue('A' . $rand, $nrCrt++);

                    $sheet->setCellValue('B' . $rand, $angajat->banca_angajat_nume);

                    // $sheet->setCellValueExplicit('C' . $rand, $angajat->banca_angajat_cnp, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // setarea tipului de text: number to text
                    $sheet->setCellValue('C' . $rand, $angajat->banca_angajat_cnp);
                    $sheet->getStyle('C' . $rand)->getNumberFormat()->setFormatCode('#'); // nu se va folosi notatia sciintifica E+

                    // Avans de platit
                    $zilePontate = $angajat->pontaj->whereIn('concediu', [0,1,2,3])->count();
                    if ($zilePontate > 10){
                        $sheet->setCellValueByColumnAndRow((4), $rand , $avansDePlatit = $angajat->avans);
                    } else if ($zilePontate >= 7){
                        $sheet->setCellValueByColumnAndRow((4), $rand , $avansDePlatit = 300);
                    } else{
                        $sheet->setCellValueByColumnAndRow((4), $rand , $avansDePlatit = 0);
                    }

                    $sheet->setCellValue('E' . $rand, $angajat->banca_iban);
                    // $sheet->setCellValue('F' . $rand, $angajat->banca_detalii_1 . " " . $angajat->banca_detalii_2);
                    $sheet->setCellValue('F' . $rand, 'AVANS ' . Carbon::parse($search_data_inceput)->isoformat('MMMM YYYY'));

                    $rand ++;
                }
                // Se parcug toate coloanele si se stabileste latimea AUTO
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                // $sheet->getColumnDimension('A')->setWidth(90);

                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="Avansuri BT.xlsx"');
                $writer->save('php://output');
                exit();

                break;
            case 'exportTxtBancaIng':
                // prepare content
                $content = "Cont sursa\tCont destinatie\tSuma\tBeneficiar\tDetalii 1\tDetalii 2\n";
                // aici trebuie cei de la o anumita firma !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                foreach ($angajati->where('firma', 'Mate Andy Style') as $angajat){

                    // $content .= $angajat->id . "\t";
                    $content .= "RO02INGB0000999912573918\t";
                    $content .= $angajat->banca_iban . "\t";

                    // Avans de platit
                    $zilePontate = $angajat->pontaj->whereIn('concediu', [0,1,2,3])->count();
                    if ($zilePontate > 10){
                        $content .= round($angajat->avans, 2) . "\t";
                    } else if ($zilePontate >= 7){
                        $content .= 300 . "\t";
                    } else{
                        $content .= 0 . "\t";
                    }

                    $content .= $angajat->banca_angajat_nume . "\t";
                    $content .= 'AVANS' . "\t";
                    $content .= Carbon::parse($search_data_inceput)->isoformat('MMMM YYYY') . "\t";

                    $content .= "\n";
                }
                // foreach ($logs as $log) {
                //   $content .= $logs->id;
                //   $content .= "\n";
                // }

                // file name that will be used in the download
                $fileName = "Avansuri MATE.txt";

                // use headers in order to generate the download
                $headers = [
                'Content-type' => 'text/plain',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
                'Content-Length' => strlen($content)
                ];

                // make a response, with the content, a 200 response code and the headers
                return Response::make($content, 200, $headers);
                break;
            default:
                    $request->session()->forget('norme_lucrate_afisare_tabelara_return_url');

                    return view('norme_lucrate.index.lunar', compact('angajati', 'angajati_in_search', 'search_nume','search_angajat_id', 'search_data_inceput', 'search_data_sfarsit'));
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NormaLucrata  $norma_lucrata
     * @return \Illuminate\Http\Response
     */
    public function mutaLucrulPeLunaAnterioara(Request $request)
    {
        $norme_lucrate = NormaLucrata::select('id', 'data')
            ->where('data', '>=', Carbon::today()->startOfMonth())
            ->where('data', '<=', Carbon::today()->startOfMonth()->addDays(14))
            ->get();

        // Daca a fost apasat butonul de mutare al lucrului, acesta va fi mutat
        if ($request->action === 'mutaLucrul'){
            if ($norme_lucrate->count() === 0){
                return redirect('/norme-lucrate/muta-lucrul-pe-luna-anterioara')->with('warning', 'Nu exista „norme lucrate” de mutat!');
            }
            NormaLucrata::select('id', 'data')
                ->where('data', '>=', Carbon::today()->startOfMonth())
                ->where('data', '<=', Carbon::today()->startOfMonth()->addDays(14))
                ->update(['data' => Carbon::today()->subMonthNoOverflow()->endOfMonth()]);
            return redirect('/norme-lucrate/muta-lucrul-pe-luna-anterioara')->with('status', 'Au fost mutate cu succes un număr de ' . $norme_lucrate->count() . ' ”norme lucrate”!');
        }

        return view('norme_lucrate/diverse/mutaLucrulPeLunaAnterioara', compact('norme_lucrate'));
    }
}
