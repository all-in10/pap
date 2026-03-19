<?php

namespace App\Filament\Resources\VacationResource\Pages;

use App\Filament\Resources\VacationResource;
use App\Models\Vacation;
use App\Enums\RoleEnum;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class CreateVacation extends CreateRecord
{
    protected static string $resource = VacationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        // Se é EMPLOYEE, setá automaticamente seu employee_id
        if ($user?->role === RoleEnum::EMPLOYEE && $user->employee_id) {
            $data['employee_id'] = $user->employee_id;
        }

        // Calcular dias solicitados
        if ($data['start_date'] && $data['end_date']) {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $daysTaken = $endDate->diffInDays($startDate) + 1;
            $data['days_taken'] = $daysTaken;
        }

        // Armazenar saldo atual do employee
        $employee = \App\Models\Employee::find($data['employee_id']);
        if ($employee) {
            $data['balance_at_creation'] = $employee->vacation_balance;
            $data['vacation_year'] = $employee->vacation_year;

            // Calcular novo saldo
            $newBalance = $employee->vacation_balance - ($data['days_taken'] ?? 0);

            // Validação: EMPLOYEE não pode criar com saldo negativo
            if ($user?->role === RoleEnum::EMPLOYEE && $newBalance < 0) {
                throw ValidationException::withMessages([
                    'days_taken' => "Saldo insuficiente! Você tem {$employee->vacation_balance} dias, mas está solicitando {$data['days_taken']} dias.",
                ]);
            }

            // Deduzir dias do saldo do employee
            $employee->update(['vacation_balance' => $newBalance]);

            // Avisar se saldo ficou negativo (apenas para HR/ADMIN)
            if ($newBalance < 0 && in_array($user?->role, [RoleEnum::ADMIN, RoleEnum::HR])) {
                Notification::make()
                    ->warning()
                    ->title('Atenção')
                    ->body("Saldo de férias do employee será negativo: {$newBalance} dias. (Permitido apenas para Admin/HR)")
                    ->send();
            }
        }

        // Status padrão é pending
        if (!isset($data['status'])) {
            $data['status'] = Vacation::STATUS_PENDING;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->success()
            ->title('Férias solicitadas')
            ->body('Solicitação de férias criada com sucesso e aguardando aprovação.')
            ->send();
    }
}
