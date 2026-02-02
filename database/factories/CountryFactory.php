<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Country;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->country(),
            'code' => $this->faker->countryCode(),
            'phonecode' => $this->faker->numberBetween(1, 999),
        ];
    }
}
