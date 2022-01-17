<?php

namespace App\Http\Controllers;

use App\Models\Pontaj;
use App\Models\NormaLucrata;

use Illuminate\Http\Request;

class InserareDateDeTestController extends Controller
{
    public function inserarePontaje()
    {
        // Inserare pontaje pe 15 zile
        for ($i=0; $i<15; $i++){
            $data = \Carbon\Carbon::now()->startOfMonth()->addDays($i);

            if ($data->isWeekday()){
                // echo $pontaj->data->isoFormat('DD.MM.YYYY');
                // echo '<br>';

                // ID-urile angajatilor pentru care se introduc pontajele
                for ($j=3; $j<=12; $j++){
                    $pontaj = new Pontaj;

                    $pontaj->angajat_id = $j;
                    $pontaj->data = $data;
                    $pontaj->ora_sosire = rand(6, 7) . ':' . rand(00, 59);
                    $pontaj->ora_plecare = rand(14, 15) . ':' . rand(00, 59);

                    $pontaj->save();
                }
            }
            echo 'Done';
        }
    }

    public function inserareComenzi()
    {
        // Inserare comenzi pe 15 zile
        for ($i=0; $i<15; $i++){
            $data = \Carbon\Carbon::now()->startOfMonth()->addDays($i);

            if ($data->isWeekday()){
                // ID-urile angajatilor pentru care se introduc normele lucrate
                for ($j=3; $j<=12; $j++){
                    $norma_lucrata = new NormaLucrata;

                    $norma_lucrata->angajat_id = $j;
                    $norma_lucrata->data = $data;
                    $norma_lucrata->produs_operatie_id = rand(1, 84);
                    $norma_lucrata->cantitate = rand(250, 300);

                    $norma_lucrata->save();
                }
            }
        }
        echo 'Done';
    }
}
