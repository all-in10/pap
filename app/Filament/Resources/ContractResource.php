<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
use App\Models\Designation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Contratos';
    protected static ?string $pluralModelLabel = 'Contratos';
    protected static ?string $modelLabel = 'Contrato';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Seleção do funcionário
            Forms\Components\Select::make('employee_id')
                ->label('Funcionário')
                ->relationship('employee', 'first_name')
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('Será criado automaticamente se vazio.'),

            // Seleção da designação
            Forms\Components\Select::make('designation_id')
                ->label('Cargo / Designação')
                ->relationship('designation', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $designation = Designation::find($state);
                        $set('salary', $designation?->base_salary ?? 0);
                    }
                }),

            // Tipo de contrato
            Forms\Components\Select::make('contract_type')
                ->label('Tipo de Contrato')
                ->options([
                    'full_time'   => 'Full Time',
                    'temporary'   => 'Temporary',
                    'internship'  => 'Internship',
                    'non_defined' => 'Não Definido',
                ])
                ->default('non_defined')
                ->required()
                ->reactive(),

            // Salário
            Forms\Components\TextInput::make('salary')
                ->label('Salário')
                ->numeric()
                ->required(),

            // Data de início
            Forms\Components\DatePicker::make('start_date')
                ->label('Data de Início')
                ->required()
                ->default(fn($get) => $get('employee.date_hired') ?? now()),

            // Data de fim
            Forms\Components\DatePicker::make('end_date')
                ->label('Data de Fim')
                ->visible(fn($get) => in_array($get('contract_type'), ['temporary', 'internship']))
                ->required(fn($get) => $get('contract_type') === 'temporary')
                ->nullable()
                ->helperText('Obrigatório apenas para contratos temporários.'),

            // Status
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'active'     => 'Ativo',
                    'terminated' => 'Encerrado',
                    'suspended'  => 'Suspenso',
                ])
                ->default('active')
                ->required(),

            // Data de contratação
            Forms\Components\DatePicker::make('date_hired')
                ->label('Data de Contratação')
                ->required()
                ->default(fn($get) => $get('employee.date_hired') ?? now()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Funcionário')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('designation.name')
                    ->label('Cargo / Designação')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('contract_type')
                    ->label('Tipo de Contrato')
                    ->sortable(),

                Tables\Columns\TextColumn::make('salary')
                    ->label('Salário')
                    ->money('EUR', true),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Início')
                    ->date(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fim')
                    ->date()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'active'     => 'success',
                        'terminated' => 'danger',
                        'suspended'  => 'warning',
                        default      => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit'   => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
