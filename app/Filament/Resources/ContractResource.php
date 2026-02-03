<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
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
    protected static ?string $navigationGroup = 'RH';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Relação com Funcionário
            Forms\Components\Select::make('employee_id')
                ->label('Funcionário')
                ->relationship('employee', 'first_name')
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('Será criado automaticamente se vazio.'),

            // Tipo de Contrato (relacionado ao ContractType)
            Forms\Components\Select::make('contract_type_id')
                ->label('Tipo de Contrato')
                ->options(fn () => \App\Models\ContractType::pluck('label', 'id')->toArray())
                ->default(fn () => \App\Models\ContractType::firstWhere('name', 'sem_termo')->id ?? null)
                ->required()
                ->reactive()
                ->searchable()
                ->preload(),

            // Salário
            Forms\Components\TextInput::make('salary')
                ->label('Salário')
                ->numeric()
                ->required(),

            // Datas
            Forms\Components\DatePicker::make('start_date')
                ->label('Data de Início')
                ->required()
                ->default(fn($get) => $get('employee.date_hired') ?? now()),

            Forms\Components\DatePicker::make('end_date')
                ->label('Data de Fim')
                ->visible(
                    fn(callable $get) =>
                    in_array($get('contract_type_id'), [
                        \App\Models\ContractType::firstWhere('name', 'temporary')->id ?? -1,
                        \App\Models\ContractType::firstWhere('name', 'internship')->id ?? -1,
                    ])
                )
                ->required(
                    fn(callable $get) =>
                    $get('contract_type_id') === (\App\Models\ContractType::firstWhere('name', 'temporary')->id ?? -1)
                )
                ->helperText('Obrigatório apenas para contratos temporários.')
                ->nullable(),

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

            // Data de Contratação
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

                Tables\Columns\TextColumn::make('contractType.label')
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
                    ->color(fn(string $state): string => match ($state) {
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
