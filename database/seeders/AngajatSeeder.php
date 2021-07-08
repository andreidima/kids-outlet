<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Angajat;

class AngajatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Angajat::factory()
            ->count(2)
            ->create();
    }
}
