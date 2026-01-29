<?php

namespace App\Filament\Resources\AuditLogResource\Pages;

use App\Filament\Resources\AuditLogResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Audit Information')
                    ->schema([
                        Components\TextEntry::make('id')
                            ->label('Log ID')
                            ->copyable(),

                        Components\TextEntry::make('created_at')
                            ->label('Timestamp')
                            ->dateTime('F d, Y H:i:s'),

                        Components\TextEntry::make('user_id')
                            ->label('User')
                            ->formatStateUsing(fn($state) => $state ? "User #{$state}" : '🔒 System'),

                        Components\TextEntry::make('action')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(2),

                Components\Section::make('Entity Information')
                    ->schema([
                        Components\TextEntry::make('model_type')
                            ->label('Entity Type')
                            ->formatStateUsing(fn($state) => class_basename($state))
                            ->badge()
                            ->color('blue'),

                        Components\TextEntry::make('model_id')
                            ->label('Record ID')
                            ->copyable()
                            ->color('info'),
                    ])->columns(2),

                Components\Section::make('Changes Made')
                    ->schema([
                        Components\ViewEntry::make('changes')
                            ->view('filament.infolist.audit-changes')
                            ->hidden(fn($record) => !$record->changes || count($record->changes) === 0),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // No edit or delete actions for audit logs
        ];
    }
}

