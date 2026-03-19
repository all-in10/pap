<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VacationResource\Pages;
use App\Models\Vacation;
use App\Enums\RoleEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class VacationResource extends Resource
{
    protected static ?string $model = Vacation::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';
    protected static ?string $navigationLabel = 'Férias';
    protected static ?string $pluralModelLabel = 'Férias';
    protected static ?string $modelLabel = 'Férias';
    protected static ?string $navigationGroup = 'RH';

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $isEmployee = $user?->role === RoleEnum::EMPLOYEE;

        return $form->schema([
            Tabs::make('Dados de Férias')
                ->tabs([
                    Tab::make('Informações Básicas')
                        ->schema([
                            Forms\Components\Select::make('employee_id')
                                ->relationship('employee', 'first_name')
                                ->label('Funcionário')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->hidden($isEmployee)
                                ->default($isEmployee ? $user->employee_id : null)
                                ->disabled($isEmployee),

                            Forms\Components\DatePicker::make('start_date')
                                ->label('Data de Início')
                                ->native(false)
                                ->required()
                                ->reactive(),

                            Forms\Components\DatePicker::make('end_date')
                                ->label('Data de Término')
                                ->native(false)
                                ->required()
                                ->reactive()
                                ->rules([
                                    function (callable $get) {
                                        return function ($attribute, $value, $fail) use ($get) {
                                            $startDate = $get('start_date');
                                            if ($startDate && $value && $value <= $startDate) {
                                                $fail('A data de fim deve ser posterior à data de início.');
                                            }
                                        };
                                    },
                                ]),

                            Forms\Components\TextInput::make('days_taken')
                                ->label('Dias Solicitados')
                                ->numeric()
                                ->disabled()
                                ->helperText('Preenchido automaticamente com base nas datas')
                                ->dehydrated(),

                            Forms\Components\TextInput::make('balance_at_creation')
                                ->label('Saldo Disponível (no momento da criação)')
                                ->numeric()
                                ->disabled()
                                ->helperText('Saldo de férias no momento da solicitação')
                                ->dehydrated(),

                            Forms\Components\Textarea::make('reason')
                                ->label('Motivo/Observações')
                                ->rows(3)
                                ->columnSpan(2),
                        ])
                        ->columns(2),

                    Tab::make('Aprovação')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options(Vacation::STATUSES)
                                ->required()
                                ->hidden($isEmployee)
                                ->default($isEmployee ? Vacation::STATUS_PENDING : null),

                            Forms\Components\Select::make('approved_by')
                                ->relationship('approvedBy', 'name')
                                ->label('Aprovado por')
                                ->searchable()
                                ->preload()
                                ->hidden($isEmployee),

                            Forms\Components\DateTimePicker::make('approved_at')
                                ->label('Data de Aprovação')
                                ->hidden($isEmployee),
                        ])
                        ->columns(2),
                ])
                ->columnSpan('full'),
        ]);
    }

    public static function table(Table $table): Table
    {
        $userRole = Auth::user()?->role;
        $isNotEmployee = $userRole !== RoleEnum::EMPLOYEE;

        return $table->columns([
            TextColumn::make('id')->label('ID')->sortable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('employee.first_name')->label('Funcionário')->sortable()->searchable(),
            TextColumn::make('start_date')->date()->label('Data Início')->sortable(),
            TextColumn::make('end_date')->date()->label('Data Fim')->sortable(),
            TextColumn::make('days_taken')->label('Dias')->sortable(),
            TextColumn::make('balance_at_creation')->label('Saldo (na criação)')->sortable(),
            TextColumn::make('status')
                ->label('Status')
                ->sortable()
                ->colors([
                    'warning' => Vacation::STATUS_PENDING,
                    'success' => Vacation::STATUS_APPROVED,
                    'danger' => Vacation::STATUS_REJECTED,
                ])
                ->formatStateUsing(fn ($state) => Vacation::STATUSES[$state] ?? $state),
            TextColumn::make('approvedBy.name')
                ->label('Aprovado por')
                ->sortable()
                ->visible($isNotEmployee)
                ->default('—'),
            TextColumn::make('approved_at')
                ->label('Data de Aprovação')
                ->dateTime()
                ->sortable()
                ->visible($isNotEmployee)
                ->default('—'),
            TextColumn::make('created_at')->dateTime()->label('Criado em')->toggleable(isToggledHiddenByDefault: true),
        ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => in_array($userRole, [RoleEnum::ADMIN, RoleEnum::HR])),
                Tables\Actions\Action::make('Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Vacation $record) {
                        $user = Auth::user();

                        // Re-check status para evitar race condition
                        $record->refresh();
                        if (!$record->isPending()) {
                            throw new \Exception('Este pedido já foi processado e não pode ser aprovado.');
                        }

                        // Verificar autorização
                        if (!$user->can('approve', $record)) {
                            throw new \Exception('Você não pode aprovar este pedido. Usuários não podem aprovar seus próprios pedidos.');
                        }

                        // Aprovar
                        $record->update([
                            'status' => Vacation::STATUS_APPROVED,
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                        ]);

                        \Illuminate\Support\Facades\Log::info("Férias #{$record->id} aprovadas por {$user->name}");
                    })
                    ->requiresConfirmation()
                    ->visible(fn(Vacation $record) => $record->isPending() && in_array($userRole, [RoleEnum::ADMIN, RoleEnum::HR])),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => in_array($userRole, [RoleEnum::ADMIN, RoleEnum::HR])),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => in_array($userRole, [RoleEnum::ADMIN, RoleEnum::HR])),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['employee', 'approvedBy']);  // Eager load to prevent N+1 queries

        $user = Auth::user();

        // Se é Employee, mostrar apenas seus próprios pedidos
        if ($user?->role === RoleEnum::EMPLOYEE && $user->employee_id) {
            $query->where('employee_id', $user->employee_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVacations::route('/'),
            'create' => Pages\CreateVacation::route('/create'),
            'edit' => Pages\EditVacation::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR, RoleEnum::EMPLOYEE]);
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR]);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR]);
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR, RoleEnum::EMPLOYEE]);
    }
}
