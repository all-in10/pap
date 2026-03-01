<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\State;

class CityFactory extends Factory
{
    protected $model = \App\Models\City::class;

    public function definition(): array
    {
        return [
            'name' => fake()->city(),
            'state_id' => State::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
