<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Avans;
use App\Models\Angajat;
use \Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;

use Illuminate\Support\Facades\Response;

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



        switch ($request->input('action')) {
            case 'exportExcelAvansuri':
                $angajati = Angajat::where('activ', 1)
                    ->with(['avansuri'=> function($query) use ($searchData){
                        $query->whereDate('data', $searchData);
                    }])
                    ->orderBy('prod')
                    ->orderBy('nume')
                    ->get();

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                // $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


                // $sheet->setCellValue('A1', 'Avansuri - ' . Carbon::parse($searchData)->isoFormat('DD.MM.YYYY') . ' - ' . Carbon::parse($searchData)->isoFormat('DD.MM.YYYY'));
                $sheet->setCellValue('A1', 'Avansuri - ' . $searchLuna . '.' . $searchAn);
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
                $formulaTotalPlataInMana = "=";

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
                    $formulaTotalPlataInMana .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . $rand . '+';

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
                $sheet->setCellValue('G' . $rand, substr_replace($formulaTotalPlataInMana ,"", -1));
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
                $angajati = Angajat::
                    with(['avansuri'=> function($query) use ($searchData){
                        $query->whereDate('data', $searchData);
                    }])
                    ->where('banca_iban', 'like', '%BTRL%')
                    ->where('activ', 1)
                    ->orderBy('prod')
                    ->orderBy('banca_angajat_nume')
                    ->get();

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

                foreach ($angajati as $index=>$angajat){
                    $sheet->setCellValue('A' . $rand, $nrCrt++);

                    $sheet->setCellValue('B' . $rand, $angajat->banca_angajat_nume);

                    // $sheet->setCellValueExplicit('C' . $rand, $angajat->banca_angajat_cnp, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // setarea tipului de text: number to text
                    $sheet->setCellValue('C' . $rand, $angajat->banca_angajat_cnp);
                    $sheet->getStyle('C' . $rand)->getNumberFormat()->setFormatCode('#'); // nu se va folosi notatia sciintifica E+

                    // Avans de platit
                    $sheet->setCellValueByColumnAndRow((4), $rand , $angajat->avansuri->first()->suma);

                    $sheet->setCellValue('E' . $rand, $angajat->banca_iban);
                    // $sheet->setCellValue('F' . $rand, $angajat->banca_detalii_1 . " " . $angajat->banca_detalii_2);
                    $sheet->setCellValue('F' . $rand, 'AVANS ' . Carbon::parse($searchData)->isoformat('MMMM YYYY'));

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
                $angajati = Angajat::
                    with(['avansuri'=> function($query) use ($searchData){
                        $query->whereDate('data', $searchData);
                    }])
                    ->where('banca_iban', 'like', '%ING%')
                    ->where('activ', 1)
                    ->orderBy('prod')
                    ->orderBy('nume')
                    ->get();

                // prepare content
                $content = "Cont sursa\tCont destinatie\tSuma\tBeneficiar\tDetalii 1\tDetalii 2\n";

                foreach ($angajati as $angajat){
                    // $content .= $angajat->id . "\t";
                    $content .= "RO02INGB0000999912573918\t";
                    $content .= $angajat->banca_iban . "\t";

                    $content .= $angajat->avansuri->first()->suma . "\t";

                    $content .= $angajat->banca_angajat_nume . "\t";
                    $content .= 'AVANS' . "\t";
                    $content .= Carbon::parse($searchData)->isoformat('MMMM YYYY') . "\t";

                    $content .= "\n";
                }

                // file name that will be used in the download
                $fileName = "Avansuri ING.txt";

                // use headers in order to generate the download
                $headers = [
                'Content-type' => 'text/plain',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
                'Content-Length' => strlen($content)
                ];

                // make a response, with the content, a 200 response code and the headers
                return Response::make($content, 200, $headers);
                break;
            case 'exportExcelMana':
                $angajati = Angajat::
                    with(['avansuri'=> function($query) use ($searchData){
                        $query->whereDate('data', $searchData);
                    }])
                    ->where(function($query){
                        $query->where(function($query){
                            $query->where('banca_iban', 'not like', '%BTRL%')
                                    ->where('banca_iban', 'not like', '%ING%');
                            })
                            ->orWhereNull('banca_iban');
                    })
                    // ->where('banca_iban', 'not like', '%BTRL%')
                    // ->where('banca_iban', 'not like', '%ING%')
                    ->where('activ', 1)
                    ->orderBy('prod')
                    ->orderBy('nume')
                    ->get();

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                // $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


                $sheet->setCellValue('A1', 'Avansuri - ' . Carbon::parse($searchData)->isoformat('MMMM YYYY'));
                $sheet->getStyle('A1')->getFont()->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->setCellValue('A4', 'Nr.');
                // $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->setCellValue('B4', 'Nume Prenume');
                $sheet->getColumnDimension('B')->setAutoSize(true);

                $sheet->setCellValueByColumnAndRow((3), 4 , 'AVANS DE PLĂTIT ÎN MÂNĂ');
                $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . '4')->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension($sheet->getCellByColumnAndRow((3), 4)->getColumn())->setWidth(10);

                $rand = 5;

                $formulaTotalPlataInMana = "=";

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

                        $sheet->setCellValueByColumnAndRow((3), $rand ,  $angajat->avansuri->first()->suma);

                        $rand ++;
                        $nr_crt_angajat ++;
                    }

                    // CALCUL TOTALURI
                    // PLata in mana
                    $sheet->setCellValueByColumnAndRow((3), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . ($rand-1) . ')');
                    $formulaTotalPlataInMana .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . $rand . '+';

                    // Schimbare culoare la totaluri in rosu
                    $sheet->getStyle(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4) . $rand
                        )->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

                    $rand += 2;
                }

                $rand += 1;

                $sheet->setCellValue('B' . $rand, 'TOTAL GENERAL');
                $sheet->getStyle('B' . $rand)->getAlignment()->setHorizontal('right');

                $sheet->setCellValue('C' . $rand, substr_replace($formulaTotalPlataInMana ,"", -1));
                // Schimbare culoare la totaluri in rosu
                $sheet->getStyle(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand . ':' .
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . $rand
                    )->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                // Set bold totaluri generale
                $sheet->getStyle('A' . $rand . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand)->getFont()->setBold(true);

                $rand += 3;

                // Se parcug toate coloanele si se stabileste latimea AUTO
                foreach ($sheet->getColumnIterator() as $column) {
                    // $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                // S-au parcurs coloanele, avem indexul ultimei coloane, se pot aplica functii acum
                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A4:' . $column->getColumnIndex() . '4')->getFont()->setBold(true);

                // $sheet->getStyle('A4:' . $column->getColumnIndex() . $rand)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="Avansuri plata în mână.xlsx"');
                $writer->save('php://output');
                exit();

                break;
            default:
                $angajati = Angajat::
                    with(['avansuri'=> function($query) use ($searchData){
                        $query->whereDate('data', $searchData);
                    }])
                    ->where('activ', 1)
                    ->orderBy('prod')
                    ->orderBy('nume')
                    ->get();

                    return view('avansuri.index', compact('angajati', 'searchData', 'searchLuna', 'searchAn'));
                break;
            }



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
