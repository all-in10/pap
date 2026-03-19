<?php

namespace App\Filament\Resources\VacationResource\Pages;

use App\Filament\Resources\VacationResource;
use Filament\Resources\Pages\EditRecord;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class EditVacation extends EditRecord
{
    protected static string $resource = VacationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalcular dias se as datas mudaram
        if ($data['start_date'] && $data['end_date']) {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $newDaysTaken = $endDate->diffInDays($startDate) + 1;

            // Se mudou número de dias, ajustar saldo do employee
            $oldDaysTaken = $this->record->days_taken;
            if ($newDaysTaken != $oldDaysTaken) {
                $difference = $newDaysTaken - $oldDaysTaken;
                $employee = $this->record->employee;

                if ($employee) {
                    $newBalance = $employee->vacation_balance - $difference;
                    $employee->update(['vacation_balance' => $newBalance]);

                    if ($newBalance < 0) {
                        Notification::make()
                            ->warning()
                            ->title('Atenção')
                            ->body("Saldo de férias ficará negativo: {$newBalance} dias")
                            ->send();
                    }
                }
            }

            $data['days_taken'] = $newDaysTaken;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->success()
            ->title('Férias atualizada')
            ->body('Solicitação de férias atualizada com sucesso.')
            ->send();
    }
}
