<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Se password estiver vazio em CREATE, usar a senha padrão
        if (empty($data['password'])) {
            $default = env('DEFAULT_USER_PASSWORD', 'ChangeMe123!');
            $data['password'] = \Illuminate\Support\Facades\Hash::make($default);
            $data['must_change_password'] = true;
        } else {
            // Se o utilizador digitou uma senha, hash-la
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }

        return $data;
    }
}