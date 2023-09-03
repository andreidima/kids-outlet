<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Salariu;
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

class SalariuController extends Controller
{
    public function index(Request $request){
        // Pana pe 20 ale lunii se incarca luna precedenta (la inceputul lunii se mai umbla la avansuri, iar pe 15 la salarii). Dupa 20 ale lunii se intra de obicei sa se calculeze avansurile pe luna in curs.
        $searchLuna = $request->searchLuna ?? Carbon::now()->day < 20 ? Carbon::now()->subMonthNoOverflow()->isoFormat('MM') : Carbon::now()->isoFormat('MM');
        $searchAn = $request->searchAn ?? Carbon::now()->isoFormat('YYYY');

        $request->validate(['searchLuna' => 'numeric|between:1,12', 'searchAn' => 'numeric|between:2023,2040']);

        $searchData = Carbon::today();
        $searchData->day = 1;
        $searchData->month = $searchLuna;
        $searchData->year = $searchAn;

        // Se verifica daca sunt generate salariile pe luna cautata, daca nu se creaza acum si se salveaza in DB
        $angajati = Angajat::select('id')->where('activ', 1)
                ->with(['salarii' => function($query) use ($searchData){
                    $query->whereDate('data', $searchData);
                }])
                ->get();
        foreach ($angajati as $angajat){
            if ($angajat->salarii->count() === 0){
                $salariu = Salariu::create(['angajat_id' => $angajat->id, 'avans' => 0, 'lichidare' => 0, 'data' => $searchData]);
            }
        }

        // Daca se apasa pe butonull „calculeazaAutomatAvansurile”, se genereaza avansurile si se salveaza in baza de date
        if ($request->input('action') === 'calculeazaAutomatAvansurile'){
            $angajati = Angajat::where('activ', 1)
                ->with(['salarii' => function($query) use ($searchData){
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
                    $angajat->salarii->first()->update(['avans' => $angajat->avans]);
                } else if ($zilePontate >= 7){
                    $angajat->salarii->first()->update(['avans' => 300]);
                } else{
                    $angajat->salarii->first()->update(['avans' => 0]);
                }
            }
        }

        // Daca se apasa pe butonull „calculeazaAutomatLichidarile”, se genereaza lichidarile si se salveaza in baza de date
        if ($request->input('action') === 'calculeazaAutomatLichidarile'){
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
                ->with(['salarii'=> function($query) use ($searchData){
                    $query->whereDate('data', $searchData);
                }])
                ->where('activ', 1) // Contul este activ
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

            foreach ($angajati as $angajat){
                // Calcularea sumelor realizate pe fiecare produs in parte si total REALIZAT
                $realizatTotal = 0;
                foreach ($produse as $produs){
                    $realizat = 0;
                    foreach ($produs->produse_operatii as $produs_operatie){
                        foreach ($angajat->norme_lucrate->where('produs_operatie_id', $produs_operatie->id) as $norma_lucrata){
                            $realizat += $norma_lucrata->cantitate * $produs_operatie->pret;
                        }
                    }
                    $realizatProduse[$produs->id] = $realizat;
                    $realizatTotal += $realizat;
                }

                // Coloanele „CO” si „MEDICALE”
                $zile_concediu_medical = 0;
                $zile_concediu_de_odihna = 0;
                foreach($angajat->pontaj as $pontaj){
                    if ($pontaj->concediu === 1){
                        $zile_concediu_medical ++;
                    }else if ($pontaj->concediu === 2){
                        $zile_concediu_de_odihna ++;
                    }
                }
                $sumaConcediuOdihna = $salariul_minim_pe_economie / $numar_de_zile_lucratoare * $zile_concediu_de_odihna;
                $sumaConcediuMedical = $salariul_minim_pe_economie / $numar_de_zile_lucratoare * $zile_concediu_medical * 0.75;

                $angajat->salarii->first()->update(['lichidare' => $realizatTotal + $sumaConcediuOdihna + $sumaConcediuMedical - ($angajat->salarii->first()->avans ?? 0)]);
            }
        }



        switch ($request->input('action')) {
            case 'exportAvansuriExcelToate':
                $angajati = Angajat::
                    with(['salarii'=> function($query) use ($searchData){
                        $query->whereDate('data', $searchData);
                    }])
                    ->with(['pontaj' => function($query) use ($searchData){
                        $query->whereMonth('data', $searchData)
                            ->whereYear('data', $searchData);
                    }])
                    ->where('activ', 1)
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

                // $sheet->setCellValueByColumnAndRow((3), 4 , 'AVANS ÎN BAZA DE DATE');
                // $sheet->setCellValueByColumnAndRow((3), 4 , 'ZILE PONTATE (inclusiv medical sau CO)');
                // $sheet->setCellValueByColumnAndRow((5), 4 , 'AVANS DE PLĂTIT');
                $sheet->setCellValueByColumnAndRow((3), 4 , 'AVANS');
                $sheet->setCellValueByColumnAndRow((4), 4 , 'BANCĂ');
                $sheet->setCellValueByColumnAndRow((5), 4 , 'MÂNĂ');
                $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . '4')->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4) . '4')->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . '4')->getAlignment()->setWrapText(true);
                // $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6) . '4')->getAlignment()->setWrapText(true);
                // $spreadsheet->getActiveSheet()->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7) . '4')->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension($sheet->getCellByColumnAndRow((3), 4)->getColumn())->setWidth(10);
                $sheet->getColumnDimension($sheet->getCellByColumnAndRow((4), 4)->getColumn())->setWidth(10);
                $sheet->getColumnDimension($sheet->getCellByColumnAndRow((5), 4)->getColumn())->setWidth(10);
                // $sheet->getColumnDimension($sheet->getCellByColumnAndRow((6), 4)->getColumn())->setWidth(10);
                // $sheet->getColumnDimension($sheet->getCellByColumnAndRow((7), 4)->getColumn())->setWidth(10);

                $rand = 5;

                $formulaTotalAvans = "=";
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

                        // Zile pontate
                        // $sheet->setCellValueByColumnAndRow((3), $rand , $zilePontate = $angajat->pontaj->whereIn('concediu', [0,1,2,3])->count());

                        // AVANS
                        if (isset($angajat->salarii->first()->avans)){
                            $sheet->setCellValueByColumnAndRow((3), $rand , $angajat->salarii->first()->avans);
                        }

                        // Mod de plata
                        if ($angajat->banca_iban || ($angajat->firma === "Petit Atelier S.R.L.") || ($angajat->firma === "Mate Andy Style") || ($angajat->firma === "Bensar S.R.L.")){
                            $sheet->setCellValueByColumnAndRow((4), $rand , $angajat->salarii->first()->avans);
                        } else{ // plata in mana
                            $sheet->setCellValueByColumnAndRow((5), $rand , $angajat->salarii->first()->avans);
                        }


                        $rand ++;
                        $nr_crt_angajat ++;
                    }

                    // CALCUL TOTALURI
                    // AVANS
                    $sheet->setCellValueByColumnAndRow((3), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . ($rand-1) . ')');
                    $formulaTotalAvans .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand . '+';
                    // Plata prin banca
                    $sheet->setCellValueByColumnAndRow((4), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4) . ($rand-1) . ')');
                    $formulaTotalPlataPrinBanca .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4) . $rand . '+';
                    // Plata in mana
                    $sheet->setCellValueByColumnAndRow((5), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . ($rand-1) . ')');
                    $formulaTotalPlataInMana .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . $rand . '+';

                    // Schimbare culoare la totaluri in rosu
                    $sheet->getStyle(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . $rand
                        )->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

                    $rand += 2;
                }

                $rand += 1;

                $sheet->setCellValue('B' . $rand, 'TOTAL GENERAL');
                $sheet->getStyle('B' . $rand)->getAlignment()->setHorizontal('right');

                $sheet->setCellValue('C' . $rand, substr_replace($formulaTotalAvans ,"", -1));
                $sheet->setCellValue('D' . $rand, substr_replace($formulaTotalPlataPrinBanca ,"", -1));
                $sheet->setCellValue('E' . $rand, substr_replace($formulaTotalPlataInMana ,"", -1));
                // Schimbare culoare la totaluri in rosu
                $sheet->getStyle(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand . ':' .
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . $rand
                    )->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                // Set bold totaluri generale
                $sheet->getStyle('A' . $rand . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5) . $rand)->getFont()->setBold(true);

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
            case 'exportAvansuriExcelBancaBt':
                $angajati = Angajat::
                    with(['salarii'=> function($query) use ($searchData){
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
                    $sheet->setCellValueByColumnAndRow((4), $rand , $angajat->salarii->first()->avans);

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
            case 'exportAvansuriTxtBancaIng':
                $angajati = Angajat::
                    with(['salarii'=> function($query) use ($searchData){
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

                    $content .= $angajat->salarii->first()->avans . "\t";

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
            case 'exportAvansuriExcelMana':
                $angajati = Angajat::
                    with(['salarii'=> function($query) use ($searchData){
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

                        $sheet->setCellValueByColumnAndRow((3), $rand ,  $angajat->salarii->first()->avans);

                        $rand ++;
                        $nr_crt_angajat ++;
                    }

                    // CALCUL TOTALURI
                    // PLata in mana
                    $sheet->setCellValueByColumnAndRow((3), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . ($rand-1) . ')');
                    $formulaTotalPlataInMana .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand . '+';

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
            case 'exportLichidariExcelToate':
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
                    ->with(['salarii'=> function($query) use ($searchData){
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


                $sheet->setCellValue('A1', 'Lichidare - ' . Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') . ' - ' . Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY'));
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
                $sheet->setCellValueByColumnAndRow(($index+11), 4 , 'LICHIDARE CALCULATĂ DE APLICAȚIE');
                $sheet->setCellValueByColumnAndRow(($index+12), 4 , 'LICHIDARE SETATĂ DE OPERATOR');

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
                        $sheet->setCellValueByColumnAndRow((($index+5)), $rand , $avansPlatit = $angajat->salarii->first()->avans ?? 0);
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

                        // LICHIDARE CALCULATA DE APLICATIE
                        $sheet->setCellValueByColumnAndRow(($index+11), $rand , '=' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+8) . $rand . '-' .
                            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+5) . $rand);
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+11), $rand)->getColumn())->setAutoSize(true);

                        // LICHIDARE SETATA DE OPERATOR
                        $sheet->setCellValueByColumnAndRow((($index+12)), $rand , $angajat->salarii->first()->lichidare ?? 0);
                        $sheet->getColumnDimension($sheet->getCellByColumnAndRow(($index+12), $rand)->getColumn())->setAutoSize(true);


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
                    // LICHIDARE CALCULATA DE APLICATIE
                    $sheet->setCellValueByColumnAndRow(($index+11), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+11) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+11) . ($rand-1) . ')');
                    // LICHIDARE SETATA DE OPERATOR
                    $sheet->setCellValueByColumnAndRow(($index+12), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+12) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+12) . ($rand-1) . ')');
                    // Schimbare culoare la totaluri in rosu
                    $sheet->getStyle(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+4) . $rand . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index+12) . $rand
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
                header('Content-Disposition: attachment; filename="Lichidari toate.xlsx"');
                $writer->save('php://output');
                exit();

                break;
            case 'exportLichidariExcelBancaBt':
                $angajati = Angajat::
                    with(['salarii'=> function($query) use ($searchData){
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

                    // Lichidare de platit - se seteaza coloana ca string pentru a putea delimita zecimalele cu punct
                    $sheet->getCellByColumnAndRow((4), $rand)->setValueExplicit(number_format((float)$angajat->salarii->first()->lichidare, 2, '.', ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);

                    $sheet->setCellValue('E' . $rand, $angajat->banca_iban);
                    // $sheet->setCellValue('F' . $rand, $angajat->banca_detalii_1 . " " . $angajat->banca_detalii_2);
                    $sheet->setCellValue('F' . $rand, 'LICHIDARE ' . Carbon::parse($searchData)->isoformat('MMMM YYYY'));

                    $rand ++;
                }
                // Se parcug toate coloanele si se stabileste latimea AUTO
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                // $sheet->getColumnDimension('A')->setWidth(90);

                // Coloana pret a fost setata ca string, asa ca este nevoie de aliniat textul la dreapta
                $sheet->getStyle('D')->getAlignment()->setHorizontal('right');

                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="Lichidari BT.xlsx"');
                $writer->save('php://output');
                exit();

                break;
            case 'exportLichidariTxtBancaIng':
                $angajati = Angajat::
                    with(['salarii'=> function($query) use ($searchData){
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

                    $content .= number_format((float)$angajat->salarii->first()->lichidare, 2, '.', '') . "\t";

                    $content .= $angajat->banca_angajat_nume . "\t";
                    $content .= 'LICHIDARE' . "\t";
                    $content .= Carbon::parse($searchData)->isoformat('MMMM YYYY') . "\t";

                    $content .= "\n";
                }

                // file name that will be used in the download
                $fileName = "Lichidari ING.txt";

                // use headers in order to generate the download
                $headers = [
                'Content-type' => 'text/plain',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
                'Content-Length' => strlen($content)
                ];

                // make a response, with the content, a 200 response code and the headers
                return Response::make($content, 200, $headers);
                break;
            case 'exportLichidariExcelMana':
                $angajati = Angajat::
                    with(['salarii'=> function($query) use ($searchData){
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


                $sheet->setCellValue('A1', 'Lichidari - ' . Carbon::parse($searchData)->isoformat('MMMM YYYY'));
                $sheet->getStyle('A1')->getFont()->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->setCellValue('A4', 'Nr.');
                // $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->setCellValue('B4', 'Nume Prenume');
                $sheet->getColumnDimension('B')->setAutoSize(true);

                $sheet->setCellValueByColumnAndRow((3), 4 , 'LICHIDARE DE PLĂTIT ÎN MÂNĂ');
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

                        $sheet->setCellValueByColumnAndRow((3), $rand ,  number_format((float)$angajat->salarii->first()->lichidare, 2, '.', ''));

                        $rand ++;
                        $nr_crt_angajat ++;
                    }

                    // CALCUL TOTALURI
                    // PLata in mana
                    $sheet->setCellValueByColumnAndRow((3), $rand , '=SUM(' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand_initial . ':' .
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . ($rand-1) . ')');
                    $formulaTotalPlataInMana .= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3) . $rand . '+';

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
                header('Content-Disposition: attachment; filename="Lichidari plata în mână.xlsx"');
                $writer->save('php://output');
                exit();

                break;
            default:
                // $angajati = Angajat::
                //     with(['salarii'=> function($query) use ($searchData){
                //         $query->whereDate('data', $searchData);
                //     }])
                //     ->where('activ', 1)
                //     ->orderBy('prod')
                //     ->orderBy('nume')
                //     ->get();


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
                    ->with(['salarii'=> function($query) use ($searchData){
                        $query->whereDate('data', $searchData);
                    }])
                    ->where('activ', 1) // Contul este activ
                    ->where('prod', 1) // doar de test
                    ->orderBy('prod')
                    ->orderBy('nume')
                    ->get();


                // Extragerea tuturor produselor ce au fost lucrate in luna curenta
                $produseIds = [];
                foreach ($angajati as $angajat){
                    foreach ($angajat->norme_lucrate as $norma_lucrata) {
                        if (!in_array( $norma_lucrata->produs_operatie->produs->id, $produseIds,)){
                            array_push($produseIds, $norma_lucrata->produs_operatie->produs->id);
                        }
                    }
                }
                $produse = Produs::with('produse_operatii')->whereIn('id', $produseIds)->get();

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


                foreach ($angajati as $angajat){
                    // Calcularea sumelor realizate pe fiecare produs in parte si total REALIZAT
                    $realizatProduse = [];
                    $realizatTotal = 0;
                    foreach ($produse as $produs){
                        $realizat = 0;
                        foreach ($produs->produse_operatii as $produs_operatie){
                            foreach ($angajat->norme_lucrate->where('produs_operatie_id', $produs_operatie->id) as $norma_lucrata){
                                $realizat += $norma_lucrata->cantitate * $produs_operatie->pret;
                            }
                        }
                        $realizatProduse[$produs->id] = $realizat;
                        $realizatTotal += $realizat;
                    }
                    $angajat->realizatProduse = $realizatProduse; // Se adauga la angajat arrayul cu realizatul per produs
                    $angajat->realizatTotal = $realizatTotal; // Se adauga la angajat realizatTotal

                    // Coloanele „CO” si „MEDICALE”
                    $zile_concediu_medical = 0;
                    $zile_concediu_de_odihna = 0;
                    foreach($angajat->pontaj as $pontaj){
                        if ($pontaj->concediu === 1){
                            $zile_concediu_medical ++;
                        }else if ($pontaj->concediu === 2){
                            $zile_concediu_de_odihna ++;
                        }
                    }
                    $angajat->sumaConcediuOdihna = $salariul_minim_pe_economie / $numar_de_zile_lucratoare * $zile_concediu_de_odihna;
                    $angajat->sumaConcediuMedical = $salariul_minim_pe_economie / $numar_de_zile_lucratoare * $zile_concediu_medical * 0.75;
                }

                return view('salarii.index', compact('angajati', 'produse', 'searchData', 'searchLuna', 'searchAn'));
                break;
            }



    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function axiosActualizareValoare(Request $request)
    {
        switch ($_GET['request']) {
            case 'actualizareValoare':
                $salariu = Salariu::find($request->salariuId);
                $salariu->update([$request->numeCamp => $request->valoare]);
                // $salariu->suma = $request->avansSuma;
                // $salariu->save();

                return response()->json([
                    'raspuns' => "Actualizat",
                    'salariuId' => $salariu->id,
                    'numeCamp' => $request->numeCamp,
                ]);
            break;
            default:
                break;
        }
    }
}
