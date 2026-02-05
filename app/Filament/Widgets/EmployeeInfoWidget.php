<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class EmployeeInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.employee-info-widget';

    public ?Employee $employee = null;
    public ?string $hourBankBalance = null;
    public $timeoffs = [];

    public function mount(): void
    {
        $user = Auth::user();
        
        if ($user && $user->employee_id) {
            $this->employee = Employee::find($user->employee_id);
            
            if ($this->employee) {
                // Obter saldo de horas do banco de horas mais recente
                $hourbank = $this->employee->hourbanks()
                    ->latest()
                    ->first();
                $this->hourBankBalance = $hourbank ? $hourbank->balance_hours . ' horas' : 'N/A';

                // Carregar todos os pedidos de férias/licenças
                $this->timeoffs = $this->employee->timeoffs()
                    ->with(['category'])
                    ->orderByDesc('created_at')
                    ->get();
            }
        }
    }
}
