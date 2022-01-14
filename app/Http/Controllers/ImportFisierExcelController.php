<?php

namespace App\Http\Controllers;

use App\Models\ProdusOperatie;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportFisierExcelController extends Controller
{
    public function importProduseOperatii()
    {
        $import_produse_operatii = DB::table('import_produse_operatii')->get();

        foreach ($import_produse_operatii as $import_produs_operatie){
            $produs_operatie = new ProdusOperatie;

            $produs_operatie->produs_id = 2;
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

            $produs_operatie->save();
        }
    }

    public function importProduseOperatiiSetareNormeInfinit()
    {
        // dd('hi');
        $produse_operatii = ProdusOperatie::all();

        foreach ($produse_operatii as $produs_operatie) {
            $produs_operatie->norma = 999999;
            $produs_operatie->save();
        }
    }
}
