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
            'designation_id' => $this->faker->optional()->randomElement(Designation::pluck('id')->toArray()),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement($genders),
            'email' => $this->faker->unique()->safeEmail(),
            'nss' => $this->faker->unique()->numerify('##########'),
            'nif' => $this->faker->unique()->numerify('##########'),
            'phone_number' => $this->faker->phoneNumber(),
            'observations' => $this->faker->optional()->sentence(),
            'address' => $this->faker->address(),
            'zip_code' => $this->faker->postcode(),
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'date_hired' => $this->faker->date('Y-m-d', 'now'),
            'is_active' => $this->faker->boolean(90),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
