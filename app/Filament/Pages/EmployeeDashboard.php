<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Employee;
use App\Models\Worklog;
use App\Models\Hoursbank;
use App\Models\Timeoff;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class EmployeeDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $slug = 'dashboard';
    protected static string $view = 'filament.pages.employee-dashboard';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Solicitar Férias ou Justificativa de Ausência')
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Tipo de Solicitação')
                        ->options([
                            'vacation' => 'Férias',
                            'justification' => 'Justificativa de Ausência',
                        ])
                        ->required(),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Data Inicial')
                        ->required(),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Data Final')
                        ->required(),
                    Forms\Components\Textarea::make('reason')
                        ->label('Motivo/Observações')
                        ->rows(3),
                ])
                ->columns(2),
        ];
    }

    public function submit(): void
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            Notification::make()
                ->title('Erro')
                ->body('Funcionário não encontrado.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        Timeoff::create([
            'employee_id' => $employee->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'type' => $data['type'],
            'status' => 'pending',
            'reason' => $data['reason'] ?? null,
        ]);

        Notification::make()
            ->title('Sucesso')
            ->body('Solicitação enviada com sucesso.')
            ->success()
            ->send();

        $this->form->fill();
    }

    public function getWorklogData(): array
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return [];
        }

        return $employee
            ->worklogs()
            ->orderBy('work_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($log) => [
                'work_date' => $log->work_date->format('Y-m-d'),
                'start_time' => $log->start_time,
                'end_time' => $log->end_time,
                'hours_worked' => $log->hours_worked,
                'extra_hours' => $log->extra_hours,
            ])
            ->toArray();
    }

    public function getDepartmentData(): ?string
    {
        $user = Auth::user();
        $employee = $user->employee;

        return $employee?->department?->name;
    }

    public function getHoursbankData(): ?int
    {
        $user = Auth::user();
        $employee = $user->employee;

        return $employee?->hoursbank?->total_hours ?? 0;
    }

    public function getTimeoffData(): array
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return [];
        }

        return $employee
            ->timeoffs()
            ->orderBy('start_date', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($timeoff) => [
                'type' => $timeoff->type,
                'start_date' => $timeoff->start_date->format('Y-m-d'),
                'end_date' => $timeoff->end_date->format('Y-m-d'),
                'status' => $timeoff->status,
                'reason' => $timeoff->reason,
            ])
            ->toArray();
    }
}
