<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?string $navigationGroup = 'System';

    protected static bool $shouldRegisterNavigation = true;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user_id')
                    ->label('User')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ? "User {$state}" : 'System'),

                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('model_type')
                    ->label('Model')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('model_id')
                    ->label('Record ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),

                Tables\Filters\SelectFilter::make('model_type')
                    ->label('Model')
                    ->options([
                        'App\Models\Employee' => 'Employee',
                        'App\Models\Contract' => 'Contract',
                        'App\Models\Timeoff' => 'Timeoff',
                        'App\Models\Worklog' => 'Worklog',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query
                            ->when(
                                $data['from'],
                                fn($q) => $q->whereDate('created_at', '>=', $data['from']),
                            )
                            ->when(
                                $data['until'],
                                fn($q) => $q->whereDate('created_at', '<=', $data['until']),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for audit logs
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
