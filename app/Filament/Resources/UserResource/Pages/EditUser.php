<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Em EDIT: se password estiver vazio, remover do update para manter a Password atual
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            // Se foi digitada uma nova password, hash-la
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }

        return $data;
    }
}