<?php

namespace App\Filament\Resources\TimeoffResource\Pages;

use App\Filament\Resources\TimeoffResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTimeoff extends CreateRecord
{
    protected static string $resource = TimeoffResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        
        // If the user is an employee and employee_id is not set, use the authenticated user's employee_id
        if ($user && strtoupper($user->role) === 'EMPLOYEE' && (!isset($data['employee_id']) || is_null($data['employee_id']))) {
            $data['employee_id'] = $user->employee_id;
        }
        
        return $data;
    }
}
