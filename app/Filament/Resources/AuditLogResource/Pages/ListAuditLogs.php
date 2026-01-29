<?php

namespace App\Filament\Resources\AuditLogResource\Pages;

use App\Filament\Resources\AuditLogResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Support\Enums\MaxWidth;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export to CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn() => $this->exportToCSV())
                ->color('success')
                ->requiresConfirmation(),

            Actions\Action::make('clear_old')
                ->label('Delete Old Logs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Audit Logs')
                ->modalDescription('Delete all audit logs older than 90 days?')
                ->modalSubmitActionLabel('Delete')
                ->action(fn() => $this->clearOldLogs()),
        ];
    }

    protected function exportToCSV(): void
    {
        $auditLogs = \App\Models\AuditLog::orderBy('created_at', 'desc')->get();

        $csv = "Timestamp,User,Action,Entity,Record ID,Changes\n";

        foreach ($auditLogs as $log) {
            $changes = $log->changes ? json_encode($log->changes) : 'N/A';
            $csv .= implode(',', [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user_id ?? 'System',
                $log->action,
                class_basename($log->model_type),
                $log->model_id,
                "\"$changes\"",
            ]) . "\n";
        }

        $filename = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';

        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo $csv;
        exit;
    }

    protected function clearOldLogs(): void
    {
        $deleted = \App\Models\AuditLog::where('created_at', '<', now()->subDays(90))->delete();

        $this->notify('success', "Deleted $deleted old audit logs");
    }
}
