<?php

declare(strict_types=1);

namespace App\Filament\Resources\AuditLogResource\Pages;

use App\Filament\Resources\AuditLogResource;
use Filament\Resources\Pages\Page;
use App\Models\AuditLog;

class ViewAuditLog extends Page
{
    protected static string $resource = AuditLogResource::class;
    protected static string $view = 'filament.resources.audit-log-resource.pages.view-audit-log';

    public $record;

    public function mount($record): void
    {
        $this->record = AuditLog::findOrFail($record);
    }
}
