<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Inserare pontaje pe o luna
        // for ($i=0; $i<31; $i++){
        //     for ($j=96; $j<=195; $j++){
        //         $pontaj = new \App\Models\Pontaj;

        //         $pontaj->angajat_id = $j;
        //         $pontaj->data = \Carbon\Carbon::now()->startOfMonth()->addDays($i);
        //         $pontaj->ora_sosire = rand(8, 9) . ':' . rand(00, 59);
        //         $pontaj->ora_plecare = rand(16, 17) . ':' . rand(00, 59);

        //         $pontaj->save();
        //     }
        // }

        //Inserare cateva pontaje suplimentare de test
        for ($i=0; $i<31; $i++){
            for ($j=0; $j<=10; $j++){
                $pontaj = new \App\Models\Pontaj;

                $pontaj->angajat_id = rand(96, 195);
                $pontaj->data = \Carbon\Carbon::now()->startOfMonth()->addDays($i);
                $pontaj->ora_sosire = rand(8, 9) . ':' . rand(00, 59);
                $pontaj->ora_plecare = rand(16, 17) . ':' . rand(00, 59);

                $pontaj->save();
            }
        }

        // \App\Models\User::factory(10)->create();
        // \App\Models\Angajat::factory(100)->create();

    }
}
