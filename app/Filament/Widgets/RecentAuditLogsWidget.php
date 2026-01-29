<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use App\Models\AuditLog;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAuditLogsWidget extends BaseWidget
{
    use WidgetVisibility;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static function allowedRoles(): array
    {
        return [
            UserRole::ROOT,
            UserRole::ADMIN,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(AuditLog::query()->latest('created_at')->limit(10))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M d, H:i:s')
                    ->sortable(),

                TextColumn::make('user_id')
                    ->label('User')
                    ->formatStateUsing(fn($state) => $state ? "User #{$state}" : '🔒 System')
                    ->color('info'),

                TextColumn::make('action')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('model_type')
                    ->label('Entity')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->badge()
                    ->color('blue'),

                TextColumn::make('model_id')
                    ->label('Record'),

                TextColumn::make('changes')
                    ->formatStateUsing(fn($state) => $state ? count($state) . ' fields' : '-')
                    ->color('info'),
            ])
            ->striped()
            ->paginated(false)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn(AuditLog $record) => route('filament.admin.resources.audit-logs.view', $record)),
            ]);
    }
}
