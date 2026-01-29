<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'action' => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'model_type' => 'App\Models\Employee',
            'model_id' => $this->faker->randomNumber(3),
            'changes' => [
                'name' => [
                    'old' => $this->faker->name(),
                    'new' => $this->faker->name(),
                ],
            ],
        ];
    }

    public function withUser($userId): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    public function created(): static
    {
        return $this->state(fn(array $attributes) => [
            'action' => 'created',
        ]);
    }

    public function updated(): static
    {
        return $this->state(fn(array $attributes) => [
            'action' => 'updated',
        ]);
    }

    public function deleted(): static
    {
        return $this->state(fn(array $attributes) => [
            'action' => 'deleted',
        ]);
    }
}
