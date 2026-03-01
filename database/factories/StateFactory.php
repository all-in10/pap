<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Country;

class StateFactory extends Factory
{
    protected $model = \App\Models\State::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'country_id' => Country::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
