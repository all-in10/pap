<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HoursbankResource\Pages;
use App\Filament\Resources\HoursbankResource\RelationManagers\WorklogsRelationManager;
use App\Models\Hoursbank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Services\Access;

class HoursbankResource extends Resource
{
    protected static ?string $model = Hoursbank::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Banco de Horas';
    protected static ?string $pluralModelLabel = 'Banco de Horas';
    protected static ?string $navigationGroup = 'Gestão de Funcionários';
    protected static ?string $modelLabel = 'Banco de Horas';

    /**
     * Define o formulário para criação/edição de banco de horas
     * Inclui seleção de funcionário e total de horas (somente leitura)
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Funcionário')
                    ->relationship('employee', 'first_name')
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('total_hours')
                    ->label('Total de Horas')
                    ->numeric()
                    ->disabled()
                    ->dehydrateStateUsing(fn($get) => $get('total_hours')), // apenas leitura
            ]);
    }

    /**
     * Define a tabela para listagem de bancos de horas
     * Mostra funcionário, total de horas e datas de criação/atualização
     */
    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(function ($query) {
            /** @var \App\Models\User|null $u */
            $u = Auth::user();
            if ($u && Access::isEmployeeRole($u)) {
                $employeeId = $u->employee?->id ?? null;
                if ($employeeId) {
                    $query->where('employee_id', $employeeId);
                }
            }

            return $query;
        })
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Funcionário')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Total de Horas')
                    ->formatStateUsing(fn($record) => round($record->employee->worklogs()->sum('extra_hours'), 2) . 'h')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Define as relações para o resource
     * Inclui o gerenciador de worklogs relacionados ao funcionário
     */
    public static function getRelations(): array
    {
        return [
            WorklogsRelationManager::class, // relaciona os Worklogs do funcionário
        ];
    }

    /**
     * Define as páginas disponíveis para o resource
     * Inclui listagem, criação e edição de bancos de horas
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHoursbanks::route('/'),
            'create' => Pages\CreateHoursbank::route('/create'),
            'edit' => Pages\EditHoursbank::route('/{record}/edit'),
        ];
    }
}
