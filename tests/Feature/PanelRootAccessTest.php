<?php

use App\Enums\UserRole;
use App\Models\User;

it('prevents root from accessing hr panel (403) and provides link to admin panel', function () {
    /** @var User $root */
    $root = User::factory()->create(['role' => UserRole::ROOT->value]);

    $this->actingAs($root)
        ->get('/hr')
        ->assertStatus(403)
        ->assertSee('erro 403')
        ->assertSee('Voltar ao meu painel')
        ->assertSee('/admin');
});

it('allows root to access admin panel', function () {
    /** @var User $root */
    $root = User::factory()->create(['role' => UserRole::ROOT->value]);

    $this->actingAs($root)
        ->get('/admin')
        ->assertStatus(200);
});