<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Spatie\Activitylog\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $slug = 'activity-logs';

    protected static ?string $recordTitleAttribute = 'description';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 100;

    protected static ?string $navigationLabel = 'Auditoria';

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->role === 'admin';
    }

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

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Registo')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('ID')
                            ->disabled(),

                        Forms\Components\TextInput::make('log_name')
                            ->label('Tipo de Log')
                            ->disabled(),

                        Forms\Components\TextInput::make('event')
                            ->label('Evento')
                            ->disabled(),

                        Forms\Components\TextInput::make('description')
                            ->label('Descrição')
                            ->disabled(),

                        Forms\Components\TextInput::make('causer.name')
                            ->label('Usuário')
                            ->disabled(),

                        Forms\Components\TextInput::make('subject_type')
                            ->label('Modelo')
                            ->formatStateUsing(fn ($state) => $state ? class_basename($state) : 'N/A')
                            ->disabled(),

                        Forms\Components\TextInput::make('subject_id')
                            ->label('ID Registro')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Data/Hora')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detalhes')
                    ->schema([
                        Forms\Components\Textarea::make('properties')
                            ->label('Propriedades (JSON)')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '')
                            ->disabled()
                            ->rows(10),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('log_name')
                    ->label('Tipo de Log')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'default' => 'gray',
                        'admin' => 'danger',
                        'system' => 'info',
                        default => 'warning',
                    }),

                TextColumn::make('event')
                    ->label('Evento')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'Login realizado' => 'info',
                        'Logout realizado' => 'secondary',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('causer.name')
                    ->label('Usuário')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('subject_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : 'N/A'),

                TextColumn::make('subject_id')
                    ->label('ID Registro')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('properties')
                    ->label('Detalhes')
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Tipo de Evento')
                    ->options([
                        'created' => 'Criado',
                        'updated' => 'Atualizado',
                        'deleted' => 'Deletado',
                        'Login realizado' => 'Login',
                        'Logout realizado' => 'Logout',
                    ]),

                SelectFilter::make('causer_id')
                    ->label('Usuário')
                    ->relationship('causer', 'name')
                    ->searchable(),

                SelectFilter::make('subject_type')
                    ->label('Modelo')
                    ->options([
                        'App\\Models\\Employee' => 'Funcionário',
                        'App\\Models\\Contract' => 'Contrato',
                        'App\\Models\\Attendance' => 'Presença',
                        'App\\Models\\Timeoff' => 'Licença',
                        'App\\Models\\Benefit' => 'Benefício',
                        'App\\Models\\Worklog' => 'Registro de Trabalho',
                        'App\\Models\\Hourbank' => 'Banco de Horas',
                        'App\\Models\\User' => 'Utilizador',
                    ]),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Data Inicial'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Data Final'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(function () {
                            /** @var \App\Models\User|null $user */
                            $user = auth()->user();
                            return !($user?->role === 'admin');
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}
