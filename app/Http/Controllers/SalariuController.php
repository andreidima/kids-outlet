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
        $searchLuna = $request->searchLuna ?? Carbon::now()->isoFormat('MM');
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
