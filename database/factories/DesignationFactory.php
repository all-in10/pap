<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DesignationFactory extends Factory
{
    protected $model = \App\Models\Designation::class;

    public function definition(): array
    {
        $levels = ['junior', 'pleno', 'senior'];

        return [
            'name' => fake()->unique()->jobTitle(),
            'description' => fake()->optional()->paragraph(),
            'level' => fake()->optional()->randomElement($levels),
            'base_salary' => fake()->optional()->randomFloat(2, 2000, 15000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
