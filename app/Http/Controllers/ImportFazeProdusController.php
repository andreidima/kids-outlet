<?php

namespace App\Http\Controllers;

use App\Models\ProdusOperatie;
use Illuminate\Http\Request;

class ImportFazeProdusController extends Controller
{
    public function importFaze()
    {
        $produse_operatii_import = DB::table('sheet1')->get();

        foreach ($produse_operatii_import as $operatie_import){
            $operatie = new ProdusOperatie;
            $operatie->produs_id =
            $operatie->nume = $operatie_import->{'DESCRIEREA OPERATIEI'};
            $operatie->numar_de_faza = $operatie_import->{'nr crt'};
            $operatie->timp = $operatie_import->{'TIMP'};
            $operatie->pret = $operatie_import->{'PRET'};
            $operatie->pret_pe_minut = $operatie_import->{'PRET PE MINUT'};
            $operatie->timp_total = $operatie_import->{'TIMP TOTAL '};
            $operatie->norma = $operatie_import->{'NORMA'};
            $operatie->pret_100_pe_minut = $operatie_import->{'PRET 100% pe minut'};
            $operatie->pret_100_pe_faze = $operatie_import->{'PRET 100% pe faze'};
            $operatie->j = $operatie_import->{'J'};
            $operatie->norma_totala = 999999999;

            $salariat->save();
        }
        echo 'done final';
    }
}
