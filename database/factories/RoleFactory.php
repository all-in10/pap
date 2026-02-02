<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(['admin', 'hr', 'employee']),
            'label' => ucfirst($this->faker->word()),
        ];
    }
}
