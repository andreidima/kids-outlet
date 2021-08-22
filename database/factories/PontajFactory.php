<?php

namespace Database\Factories;

use App\Models\Pontaj;
use Illuminate\Database\Eloquent\Factories\Factory;

class PontajFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pontaj::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nume' => $this->faker->name(),
            'angajat_id' => $this->faker->numberBetween(13, 45),
            'cod_de_acces' => $this->faker->numberBetween(000000, 999999),
        ];
    }
}
