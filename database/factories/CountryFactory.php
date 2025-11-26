<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition()
    {
        return [
            'name' => $this->faker->country(),
            'code' => strtoupper($this->faker->unique()->lexify('??')), // exemplo: US, BR
            'phonecode' => $this->faker->numberBetween(1, 999),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
