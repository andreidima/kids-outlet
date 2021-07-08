<?php

namespace Database\Factories;

use App\Models\Angajat;
use Illuminate\Database\Eloquent\Factories\Factory;

class AngajatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Angajat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nume' => $this->faker->name(),
            'telefon' => '074' . $this->faker->numberBetween(1000000, 9999999),
            'cod_de_acces' => $this->faker->numberBetween(000000, 999999),
        ];
    }
}
