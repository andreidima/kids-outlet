<?php

namespace App\Http\Controllers;

use App\Models\Pontaj;
use App\Models\Angajat;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\Storage;

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
    public function create(Request $request, $angajat = null, $data = null)
    {
        $pontaj = Pontaj::firstOrNew([
            'angajat_id' => $angajat,
            'data' => $data
        ]);

        $request->session()->get('pontaj_return_url') ?? $request->session()->put('pontaj_return_url', url()->previous());

        return view('pontaje.create', compact('pontaj'));
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

        return redirect($request->session()->get('pontaj_return_url') ?? ('/pontaje/afisare_lunar'))
            ->with('status', 'Pontajul pentru „' . ($pontaj->angajat->nume ?? '') . '” a fost adăugat cu succes!');
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
    public function edit(Request $request, Pontaj $pontaj)
    {
        $angajati = Angajat::orderBy('nume')->get();

        $request->session()->get('pontaj_return_url') ?? $request->session()->put('pontaj_return_url', url()->previous());

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
        $pontaj->update($this->validateRequest($request, $pontaj));

        return redirect($request->session()->get('pontaj_return_url') ?? ('/pontaje/afisare_lunar'))
            ->with('status', 'Pontajul pentru „' . $pontaj->angajat->nume . '" a fost modificat cu succes!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pontaj  $pontaj
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Pontaj $pontaj)
    {
        $pontaj->delete();

        return redirect($request->session()->get('pontaj_return_url') ?? ('/pontaje/afisare_lunar'))
            ->with('status', 'Pontajul pentru "' . $pontaj->angajat->nume . '" a fost șters cu succes!');
    }

    /**
     * Validate the request attributes.
     *
     * @return array
     */
    protected function validateRequest(Request $request, Pontaj $pontaj = null)
    {
        return request()->validate(
            [
                'angajat_id' => 'required',
                'data' => 'required',
                // 'data' => ($request->_method !== "PATCH") ?
                //     [
                //         'required',
                //         Rule::unique('pontaje')->where(function ($query) use ($request) {
                //             return $query->where('angajat_id', $request->angajat_id)
                //                 ->where('data', $request->data);
                //         }),
                //     ]
                //     :
                //     [
                //         'required',
                //         Rule::unique('pontaje')->ignore($pontaj->id)->where(function ($query) use ($request) {
                //             return $query->where('angajat_id', $request->angajat_id)
                //                 ->where('data', $request->data);
                //         }),
                //     ],
                'ora_sosire' => 'nullable',
                'ora_plecare' => 'nullable|after:ora_sosire',
                'concediu' => 'nullable',
                'return_url' => 'nullable',
            ],
            [
                'data.unique' => 'Există deja un pontaj pentru acest angajat, pentru data calendaristică selectată'
            ]
        );
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
        $search_data_sfarsit = \Request::get('search_data_sfarsit') ?? \Carbon\Carbon::parse($search_data_inceput)->addDays(5)->toDateString();

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

        $angajati = Angajat::with(['pontaj'=> function($query) use ($search_data_inceput, $search_data_sfarsit){
                $query->whereDate('data', '>=', $search_data_inceput)
                    ->whereDate('data', '<=', $search_data_sfarsit);
            }])
            ->when($search_nume, function ($query, $search_nume) {
                return $query->where('nume', 'like', '%' . $search_nume . '%');
            })
            ->where('id', '>', 3) // Conturile de angajat pentru Andrei Dima
            ->where('activ', 1)
            ->orderBy('firma')
            ->orderBy('nume')
            // ->groupBy('angajat_id')
            // ->paginate(10);
            ->get();

        switch ($request->input('action')) {
            case 'export_pdf':
                    // if ($request->view_type === 'export-html') {
                    //     return view('pontaje.export.pontajePdf', compact('angajati', 'search_data'));
                    // } elseif ($request->view_type === 'export-pdf') {
                        $pdf = \PDF::loadView('pontaje.export.pontajePdf', compact('angajati', 'search_data_inceput', 'search_data_sfarsit'))
                            ->setPaper('a4', 'landscape');
                        $pdf->getDomPDF()->set_option("enable_php", true);
                        // return $pdf->download('Raport Pontaj, ' .
                        //     \Carbon\Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') .
                        //     ' - ' .
                        //     \Carbon\Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY') .
                        //     '.pdf');
                        return $pdf->stream();
                    // }
                break;
            case 'export_excel':


                // foreach ($angajati->groupby('firma') as $angajati_per_firma){
                //     foreach ($angajati_per_firma as $angajat){

                //         echo $angajat->nume . ' - ';
                //         echo $angajat->pontaj->count();
                //         echo '<br><br>';
                //     }
                // }
                // dd('stop');

                $spreadsheet = new Spreadsheet();
                // $sheet = $spreadsheet->getActiveSheet();
                // // $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

                // $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


                // $sheet->setCellValue('A1', 'FOAIE COLECTIVA DE PREZENTA (PONTAJ) PETITERaport Pontaj - ' . Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') . ' - ' . Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY'));
                // $sheet->getStyle('A1')->getFont()->setSize(14);
                // $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // // $sheet->setCellValue('A2', Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') . ' - ' . Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY'));
                // // $sheet->getStyle('A2')->getFont()->setSize(14);

                // $sheet->setCellValue('A4', 'Nr. Crt.');
                // $sheet->getStyle('A4')->getAlignment()->setTextRotation(90);

                // $sheet->setCellValue('B4', 'Nume Prenume');
                // // $sheet->getColumnDimension('D')->setWidth(40, 'pt');
                // for ($ziua = 0; $ziua <= Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++){
                //     $sheet->setCellValueByColumnAndRow(($ziua+5), 4 , Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('DD'));
                // }

                // // $sheet->setCellValueByColumnAndRow(($ziua+5), 4 , 'Total ore lucrate');

                // $rand = 5;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder( new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder() );

                $foaie_numar = 0;
                foreach ($angajati->groupby('firma') as $angajati_per_firma){

                    // Create a new worksheet called "My Data"
                    $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $angajati_per_firma->first()->firma);

                    // Attach the "My Data" worksheet as the first worksheet in the Spreadsheet object
                    $spreadsheet->addSheet($myWorkSheet, $foaie_numar);

                    $spreadsheet->setActiveSheetIndex($foaie_numar);

                    $foaie_numar++;

                    // $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    // $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

                    $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


                    $sheet->setCellValue('A1', 'FOAIE COLECTIVA DE PREZENTA (PONTAJ) - ' . $angajati_per_firma->first()->firma);
                    $sheet->getStyle('A1')->getFont()->setSize(14);
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                    $sheet->setCellValue('A2', Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') . ' - ' . Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY'));
                    $sheet->getStyle('A2')->getFont()->setSize(12);
                    $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                    // $sheet->setCellValue('A2', Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') . ' - ' . Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY'));
                    // $sheet->getStyle('A2')->getFont()->setSize(14);

                    $sheet->setCellValue('A4', 'Nr. Crt.');
                    $sheet->getStyle('A4')->getFont()->setSize(8);
                    $sheet->mergeCells('A4:A5');
                    $sheet->getColumnDimension('A')->setWidth(3);
                    $sheet->getStyle('A4')->getAlignment()->setTextRotation(90);

                    $sheet->setCellValue('B4', "Numele și Prenumele");
                    $sheet->getStyle('B4')->getFont()->setSize(10);
                    $sheet->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getColumnDimension('B')->setAutoSize(true);
                    $sheet->mergeCells('B4:B5');

                    $sheet->setCellValue('C4', "Numar de\nmarca");
                    $sheet->getStyle('C4')->getFont()->setSize(8);
                    $sheet->mergeCells('C4:C5');
                    $sheet->getColumnDimension('C')->setWidth(5);
                    $sheet->getStyle('C4')->getAlignment()->setTextRotation(90);

                    $sheet->setCellValue('D4', "Meseria sau\nfunctia");
                    $sheet->getStyle('D4')->getFont()->setSize(8);
                    $sheet->mergeCells('D4:D5');
                    $sheet->getColumnDimension('D')->setWidth(5);
                    $sheet->getStyle('D4')->getAlignment()->setTextRotation(90);

                    // $sheet->getColumnDimension('D')->setWidth(40, 'pt');
                    for ($ziua = 0; $ziua <= Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++){
                        $sheet->getColumnDimensionByColumn($ziua+5)->setWidth(3);
                        $sheet->setCellValueByColumnAndRow(($ziua+5), 5 , Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('D'));
                    }
                    $sheet->getStyle('E5:' . $sheet->getCellByColumnAndRow(($ziua+4), 5)->getColumn() . '5')->getFont()->setSize(10);
                    $sheet->getStyle('E5:' . $sheet->getCellByColumnAndRow(($ziua+4), 5)->getColumn() . '5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $sheet->setCellValue('E4', "ORE ZILNIC");
                    $sheet->mergeCells('E4:' . $sheet->getCellByColumnAndRow(($ziua+4), 5)->getColumn() . '4');
                    $sheet->getStyle('E4:' . $sheet->getCellByColumnAndRow(($ziua+4), 5)->getColumn() . '4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    // Se continua titlul tabelului de la ultima zi ramasa + primele 4 coloana
                    $sheet->setCellValueByColumnAndRow(($ziua+5), 4, "Total ore\nlucrate");
                    $sheet->getStyle('A6:' . $column->getColumnIndex() . '4')->getAlignment()->setHorizontal('center');
                    $sheet->mergeCells(
                        $sheet->getCellByColumnAndRow(($ziua+5), 4)->getColumn() . '4'
                        . ':' .
                        $sheet->getCellByColumnAndRow(($ziua+5), 5)->getColumn() . '5'
                        );
                    $sheet->getStyleByColumnAndRow(($ziua+5), 4)->getAlignment()->setTextRotation(90);


                    $sheet->getRowDimension('5')->setRowHeight(35);

                    // $sheet->setCellValueByColumnAndRow(($ziua+5), 4 , 'Total ore lucrate');

                    $rand = 5;

                        // if ($angajati_per_firma->first()->firma){
                        //     $sheet->setCellValue('A' . $rand, 'Firma ' . $angajati_per_firma->first()->firma);
                        // } else {
                        //     $sheet->setCellValue('A' . $rand, 'Firma nesetată');
                        // }

                        $rand ++;
                        $nr_crt_angajat = 1;

                        foreach ($angajati_per_firma as $angajat){
                            if ($angajat->pontaj->count() > 0){ // se exporta in excel doar cei care au pontaj

                                $sheet->setCellValue('A' . $rand, $nr_crt_angajat);
                                $sheet->setCellValue('B' . $rand, $angajat->nume);

                                $numar_total_de_ore = 0;

                                for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++){
                                    // if (\Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isWeekday()){
                                        foreach ($angajat->pontaj->where('data', \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString()) as $pontaj){
                                            switch ($pontaj->concediu){
                                                    case '0':
                                                        // if ($pontaj->ora_sosire && $pontaj->ora_plecare){
                                                        //     // se calculaeaza secundele lucrate
                                                        //     $secunde = \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInSeconds(\Carbon\Carbon::parse($pontaj->ora_sosire));
                                                        //     // daca sunt mai mult de 8 ore, se reduce la 8 ore
                                                        //     ($secunde > 28800) ? $secunde = 28800 : '';
                                                        //     // se aduna la timpul total
                                                        //     $timp_total->addSeconds($secunde);

                                                        //     $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, \Carbon\Carbon::parse($secunde)->isoFormat('HH:mm'));
                                                        // }
                                                        if ($pontaj->ora_sosire && $pontaj->ora_plecare){
                                                                // $numar_de_ore = round(
                                                                //     \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInMinutes(\Carbon\Carbon::parse($pontaj->ora_sosire))
                                                                //     / 60 )
                                                            $numar_de_ore = Carbon::parse($pontaj->ora_plecare)->diffInHours(Carbon::parse($pontaj->ora_sosire));

                                                            if ($numar_de_ore < 8) {
                                                                $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, $numar_de_ore);
                                                                $numar_total_de_ore += $numar_de_ore;
                                                            }else{
                                                                $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, 8);
                                                                $numar_total_de_ore += 8;
                                                            }
                                                            // switch (\Carbon\Carbon::parse($pontaj->ora_plecare)->diffInHours(\Carbon\Carbon::parse($pontaj->ora_sosire))){
                                                            //     case 0:
                                                            //     case 1:
                                                            //     case 2: $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, 2);
                                                            //         break;
                                                            //     case 3:
                                                            //     case 4:
                                                            //     case 5: $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, 4);;
                                                            //         break;
                                                            //     case 6:
                                                            //     case 7:
                                                            //     case 8:
                                                            //     case 9:
                                                            //     case 10:
                                                            //     case 11:
                                                            //     case 12:
                                                            //     case 13:
                                                            //     case 14:
                                                            //     case 15:
                                                            //     case 16:
                                                            //     case 17:
                                                            //     case 18: $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, 8);
                                                            //         break;
                                                            //     default: $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInHours(\Carbon\Carbon::parse($pontaj->ora_sosire)));
                                                            //         break;
                                                            // }
                                                        }
                                                        break;
                                                    case '1':
                                                        $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, 'CM');
                                                        break;
                                                    case '2':
                                                        $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, 'CO');
                                                        break;
                                                    case '3':
                                                        $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, 'Î');
                                                        break;
                                                    case '4':
                                                        $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, 'N');
                                                        break;
                                            }
                                        }
                                    // }

                                    // $sheet->getCellByColumnAndRow(($ziua+5), $rand)->getStyle()
                                    //     ->getBorders()
                                    //     ->getOutline()
                                    //     ->setBorderStyle(Border::BORDER_THIN);
                                        // ->setColor(new Color('FFFF0000'));;
                                }

                                // $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, number_format(\Carbon\Carbon::parse($timp_total)->floatDiffInHours(\Carbon\Carbon::today()), 4));

                                // Afisarea totalului de ore
                                $sheet->setCellValueByColumnAndRow(($ziua+5), $rand, $numar_total_de_ore);

                                $rand ++;
                                $nr_crt_angajat ++;
                            }
                        }
                        $rand ++;

                    // Se parcug toate coloanele si se stabileste latimea AUTO
                    foreach ($sheet->getColumnIterator() as $column) {
                    //     // $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                    //     $sheet->getColumnDimension($column->getColumnIndex())->setWidth(3);
                    //     $sheet->getColumnDimension('B')->setAutoSize(true);
                    }
                    // S-au parcurs coloanele, avem indexul ultimei coloane, se pot aplica functii acum
                    $sheet->mergeCells('A1:' . $column->getColumnIndex() . '1');
                    $sheet->mergeCells('A2:' . $column->getColumnIndex() . '2');
                    $sheet->getStyle('A6:' . $column->getColumnIndex() . '4')->getAlignment()->setHorizontal('center');
                    // $sheet->getStyle('A6:' . $column->getColumnIndex() . '4')->getFont()->setBold(true);
                    $sheet->getStyle('A6:' . $column->getColumnIndex() . $rand)->getFont()->setSize(10);
                    $sheet->getStyle('A6:' . $column->getColumnIndex() . $rand)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A6:' . $column->getColumnIndex() . $rand)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }

                // // Se parcug toate coloanele si se stabileste latimea AUTO
                // foreach ($sheet->getColumnIterator() as $column) {
                //     $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                // }
                // // S-au parcurs coloanele, avem indexul ultimei coloane, se pot aplica functii acum
                // $sheet->mergeCells('A1:' . $column->getColumnIndex() . '1');
                // $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getAlignment()->setHorizontal('center');
                // $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getFont()->setBold(true);
                // $sheet->getStyle('A4:' . $column->getColumnIndex() . $rand)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);





                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="Raport Pontaje.xlsx"');
                $writer->save('php://output');
                exit();

                // try {
                //     Storage::makeDirectory('fisiere_temporare');
                //     $writer = new Xlsx($spreadsheet);
                //     $writer->save(storage_path(
                //         'app/fisiere_temporare/' .
                //         'Raport Pontaje' . '.xlsx'
                //     ));
                // } catch (Exception $e) { }

                // return response()->download(storage_path(
                //     'app/fisiere_temporare/' .
                //     'Raport Pontaje' . '.xlsx'
                // ));

                break;
            default:
                $request->session()->forget('pontaj_return_url');

                return view('pontaje.index.lunar', compact('angajati', 'search_nume', 'search_data_inceput', 'search_data_sfarsit'));
                break;
        }

    }
}
