<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->unique()->randomElement(['admin', 'hr', 'employee']),
            'label' => ucfirst(fake()->word()),
        ];
    }
}
