<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Department;
use App\Models\Designation;

class EmployeeFactory extends Factory
{
    protected $model = \App\Models\Employee::class;

    public function definition(): array
    {
        $genders = ['male', 'female', 'n/a'];

        return [
            'country_id' => Country::factory(),
            'state_id' => State::factory(),
            'city_id' => City::factory(),
            'department_id' => Department::factory(),
            'designation_id' => fake()->optional()->randomElement(Designation::pluck('id')->toArray()),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement($genders),
            'email' => fake()->unique()->safeEmail(),
            'nss' => fake()->unique()->numerify('##########'),
            'nif' => fake()->unique()->numerify('##########'),
            'phone_number' => fake()->phoneNumber(),
            'observations' => fake()->optional()->sentence(),
            'address' => fake()->address(),
            'zip_code' => fake()->postcode(),
            'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
            'date_hired' => fake()->date('Y-m-d', 'now'),
            'is_active' => fake()->boolean(90),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
