<?php

namespace App\Http\Controllers;

use App\Models\ProdusOperatie;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportFisierExcelController extends Controller
{
    public function importProduseOperatii()
    {
        $import_produse_operatii = DB::table('Sheet1')->get();

        foreach ($import_produse_operatii as $import_produs_operatie){
            $produs_operatie = new ProdusOperatie;
            $produs_operatie->produs_id = 92;
            $produs_operatie->nume = $import_produs_operatie->{'DESCRIEREA OPERATIEI'};
            $produs_operatie->numar_de_faza = $import_produs_operatie->{'nr crt'};
            $produs_operatie->timp = str_replace(',', '.', $import_produs_operatie->{'TIMP'});
            $produs_operatie->pret = str_replace(',', '.', $import_produs_operatie->{'PRET'});
            $produs_operatie->pret_pe_minut = str_replace(',', '.', $import_produs_operatie->{'PRET PE MINUT'});
            $produs_operatie->timp_total = str_replace(',', '.', $import_produs_operatie->{'TIMP TOTAL'});
            $produs_operatie->norma = str_replace(',', '.', $import_produs_operatie->{'NORMA'});
            $produs_operatie->pret_100_pe_minut = str_replace(',', '.', $import_produs_operatie->{'PRET 100% pe minut'});
            $produs_operatie->pret_100_pe_faze = str_replace(',', '.', $import_produs_operatie->{'PRET 100% pe faze'});
            $produs_operatie->j = str_replace(',', '.', $import_produs_operatie->{'J'});
            // $produs_operatie->norma_totala = 1500;

            // $produs_operatie = ProdusOperatie::
            //     where('produs_id', 2)
            //     ->where('nume', $import_produs_operatie->{'DESCRIEREA OPERATIEI'})
            //     ->get();

            // foreach ($produs_operatie as $produs_operatie){
            //     $produs_operatie->norma = str_replace(',', '.', $import_produs_operatie->{'NORMA'});
            //     echo $loop . ' ' . $produs_operatie->numar_de_faza . ' ' . $produs_operatie->nume . '<br>';
            //     $loop++;
            // }
            // echo '<br>';

            $produs_operatie->save();
        }


        echo 'Au fost importate ' . $import_produse_operatii->count() . ' operatii (faze) ale produsului!';
    }

    public function importProduseOperatiiSetareNorme()
    {
        $produse_operatii = ProdusOperatie::where('produs_id', 9)->get();

        foreach ($produse_operatii as $produs_operatie) {
            $produs_operatie->norma_totala = 992;
            $produs_operatie->save();
        }

        echo 'Tuturor operatiilor, ' . $produse_operatii->count() . ', le-a fost setata norma de 992!';
    }
}
