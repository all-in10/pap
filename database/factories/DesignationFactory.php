<?php

namespace Database\Factories;

use App\Models\Designation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DesignationFactory extends Factory
{
    protected $model = Designation::class;

    public function definition(): array
    {
        $levels = ['junior', 'pleno', 'senior'];

        return [
            'name' => $this->faker->unique()->jobTitle(),
            'description' => $this->faker->optional()->paragraph(),
            'level' => $this->faker->optional()->randomElement($levels),
            'base_salary' => $this->faker->optional()->randomFloat(2, 2000, 15000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}