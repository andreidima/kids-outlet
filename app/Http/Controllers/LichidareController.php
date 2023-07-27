<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Avans;
use App\Models\Angajat;
use App\Models\Produs;
use App\Models\ProdusOperatie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;

use Illuminate\Support\Facades\Response;

class LichidareController extends Controller
{
    public function index(Request $request){
        // Daca nu sunt introduse de utilizator, se afiseaza luna anterioara
        $searchLuna = $request->searchLuna ?? Carbon::now()->subMonth()->isoFormat('MM');
        $searchAn = $request->searchAn ?? Carbon::now()->subMonth()->isoFormat('YYYY');

        $request->validate(['searchLuna' => 'numeric|between:1,12', 'searchAn' => 'numeric|between:2023,2040']);

        $searchData = Carbon::today();
        $searchData->day = 1;
        $searchData->month = $searchLuna;
        $searchData->year = $searchAn;


        switch ($request->input('action')) {
            case 'exportExcelToate':
                $search_data_inceput = Carbon::parse($searchData);
                $search_data_sfarsit = Carbon::parse($searchData)->endOfMonth();

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
                    ->with(['avansuri'=> function($query) use ($searchData){
                        $query->whereDate('data', $searchData);
                    }])
                    ->where('activ', 1) // Contul este activ
                    ->orderBy('prod')
                    ->orderBy('nume')
                    ->get();

                $produseIds = [];
                foreach ($angajati as $angajat){
                    foreach ($angajat->norme_lucrate as $norma_lucrata) {
                        if (!in_array( $norma_lucrata->produs_operatie->produs->id, $produseIds,)){
                            array_push($produseIds, $norma_lucrata->produs_operatie->produs->id);
                        }
                    }
                }
                $produse = Produs::whereIn('id', $produseIds)->get();


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

                        // AVANS
                        $sheet->setCellValueByColumnAndRow((($index+5)), $rand , $avansPlatit = $angajat->avansuri->first()->suma);
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
            default:
                    return view('lichidare.index', compact('searchData', 'searchLuna', 'searchAn'));
                break;
        }
    }
}
