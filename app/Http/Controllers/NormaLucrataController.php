<?php

namespace App\Http\Controllers;

use App\Models\NormaLucrata;
use App\Models\Angajat;
use App\Models\Produs;
use App\Models\ProdusOperatie;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

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
        $search_data = $data ?? \Request::get('search_data');

        $norme_lucrate = NormaLucrata::with('angajat', 'produs_operatie.produs')
            ->when($search_nume, function (Builder $query, $search_nume) {
                $query->whereHas('angajat', function (Builder $query) use ($search_nume) {
                    $query->where('nume', 'like', '%' . $search_nume . '%');
                });
            })
            // ->when($angajat, function (Builder $query, $angajat) {
            //     $query->whereHas('angajat', function (Builder $query) use ($angajat) {
            //         $query->where('id', $angajat);
            //     });
            // })
            ->when($search_data, function ($query, $search_data) {
                return $query->whereDate('data', '=', $search_data);
            })
            ->latest()
            ->simplePaginate(25);

        $request->session()->forget('norme_lucrate_return_url');
        $request->session()->get('norme_lucrate_afisare_tabelara_return_url') ?? $request->session()->put('norme_lucrate_afisare_tabelara_return_url', url()->previous());

        return view('norme_lucrate.index', compact('norme_lucrate', 'search_nume', 'search_data', 'angajat'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $angajat_id = null, $data = null)
    {
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
                $query->whereDate('data', '>=', $search_data_inceput)
                    ->whereDate('data', '<=', $search_data_sfarsit);
            }])
            ->with('norme_lucrate.produs_operatie.produs')
            ->when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            ->where('id', '>', 3) // Conturile de angajat pentru Andrei Dima
            ->orderBy('nume')
            // ->take(2)
            // ->paginate(10);
            ->get();

        // foreach ($angajati as $angajat){
        //     echo $angajat->nume . '<br>';
        //     foreach ($angajat->norme_lucrate as $norma_lucrata){
        //         echo $norma_lucrata->produs_operatie->produs->nume . ' --- ' . $norma_lucrata->produs_operatie->nume . ' --- ' . $norma_lucrata->cantitate;
        //         echo '<br>';
        //     }
        //         echo '<br>';
        // }
        // dD($angajati->groupby('norme_lucrate'));
        // foreach ($angajati->groupby('norme_lucrate.produs_operatie.produs') as $angajat){
        //     echo $angajat->first()->norme_lucrate->first() . '<br>';
        // }

        $produse = Produs::whereHas('produse_operatii', function ($query) use ($search_data_inceput, $search_data_sfarsit){
                return $query->whereHas('norme_lucrate', function ($query) use ($search_data_inceput, $search_data_sfarsit){
                    return $query->whereDate('data', '>=', $search_data_inceput)
                        ->whereDate('data', '<=', $search_data_sfarsit);
                });
            })

                // ->whereDate('norme_lucrate.data', '>=', $search_data_inceput)
                // ->whereDate('norme_lucrate.data', '<=', $search_data_sfarsit)
            // with(['produse_operatii.norme_lucrate'=> function($query) use ($search_data_inceput, $search_data_sfarsit){
            //     $query->whereDate('data', '>=', $search_data_inceput)
            //         ->whereDate('data', '<=', $search_data_sfarsit);
            // }])
            ->get();

        foreach ($produse as $produs){
            echo $produs->nume . '<br>';
        }


        switch ($request->input('action')) {
            case 'export_excel':
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
                $sheet->setCellValue('B4', 'Nume Prenume');
                // $sheet->getColumnDimension('D')->setWidth(40, 'pt');
                for ($ziua = 0; $ziua <= Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++){
                    $sheet->setCellValueByColumnAndRow(($ziua+3), 4 , Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('DD'));
                }

                // $sheet->setCellValueByColumnAndRow(($ziua+3), 4 , 'Total ore lucrate');

                $rand = 5;
                foreach ($angajati as $angajat){
                    // $timp_total = Carbon::today();

                    $sheet->setCellValue('A' . $rand, $rand-4);
                    $sheet->setCellValue('B' . $rand, $angajat->nume);

                    // for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++){
                    //     if (\Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isWeekday()){
                    //         foreach ($angajat->pontaj->where('data', \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString()) as $pontaj){
                    //             switch ($pontaj->concediu){
                    //                     case '0':
                    //                         if ($pontaj->ora_sosire && $pontaj->ora_plecare){
                    //                             switch (\Carbon\Carbon::parse($pontaj->ora_plecare)->diffInHours(\Carbon\Carbon::parse($pontaj->ora_sosire))){
                    //                                 case 1:
                    //                                 case 2: $sheet->setCellValueByColumnAndRow(($ziua+3), $rand, 2);
                    //                                     break;
                    //                                 case 3:
                    //                                 case 4:
                    //                                 case 5: $sheet->setCellValueByColumnAndRow(($ziua+3), $rand, 4);;
                    //                                     break;
                    //                                 case 6:
                    //                                 case 7:
                    //                                 case 9:
                    //                                 case 10:
                    //                                 case 11:
                    //                                 case 12: $sheet->setCellValueByColumnAndRow(($ziua+3), $rand, 8);
                    //                                     break;
                    //                                 default: $sheet->setCellValueByColumnAndRow(($ziua+3), $rand, \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInHours(\Carbon\Carbon::parse($pontaj->ora_sosire)));
                    //                                     break;
                    //                             }
                    //                         }
                    //                         break;
                    //                     case '1':
                    //                         $sheet->setCellValueByColumnAndRow(($ziua+3), $rand, 'M');
                    //                         break;
                    //                     case '2':
                    //                         $sheet->setCellValueByColumnAndRow(($ziua+3), $rand, 'O');
                    //                         break;
                    //                     case '3':
                    //                         $sheet->setCellValueByColumnAndRow(($ziua+3), $rand, 'Î');
                    //                         break;
                    //                     case '4':
                    //                         $sheet->setCellValueByColumnAndRow(($ziua+3), $rand, 'N');
                    //                         break;
                    //             }
                    //         }
                    //     }

                        // $sheet->getCellByColumnAndRow(($ziua+3), $rand)->getStyle()
                        //     ->getBorders()
                        //     ->getOutline()
                        //     ->setBorderStyle(Border::BORDER_THIN);
                            // ->setColor(new Color('FFFF0000'));;
                    // }

                    // $sheet->setCellValueByColumnAndRow(($ziua+3), $rand, number_format(\Carbon\Carbon::parse($timp_total)->floatDiffInHours(\Carbon\Carbon::today()), 4));

                    $rand ++;
                }

                // Se parcug toate coloanele si se stabileste latimea AUTO
                // foreach ($sheet->getColumnIterator() as $column) {
                //     $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                // }
                // S-au parcurs coloanele, avem indexul ultimei coloane, se pot aplica functii acum
                // $sheet->mergeCells('A1:' . $column->getColumnIndex() . '1');
                // $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getAlignment()->setHorizontal('center');
                // $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getFont()->setBold(true);
                // $sheet->getStyle('A4:' . $column->getColumnIndex() . $rand)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="Raport Norme lucrate.xlsx"');
                $writer->save('php://output');
                exit();

                break;
            default:
                    $request->session()->forget('norme_lucrate_afisare_tabelara_return_url');

                    return view('norme_lucrate.index.lunar', compact('angajati', 'search_nume', 'search_data_inceput', 'search_data_sfarsit'));
                break;
        }
    }
}
