<?php

namespace App\Filament\Resources\TimeoffResource\Pages;

use App\Filament\Resources\TimeoffResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTimeoff extends EditRecord
{
    protected static string $resource = TimeoffResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();
        $record = $this->record;
        
        // Ensure employee_id is always set - preserve existing or use user's employee_id if employee
        if (!isset($data['employee_id']) || is_null($data['employee_id'])) {
            if ($record && $record->employee_id) {
                $data['employee_id'] = $record->employee_id;
            } elseif ($user && strtoupper($user->role) === 'EMPLOYEE') {
                $data['employee_id'] = $user->employee_id;
            }
        }
        
        return $data;
    }
}
