<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeBenefitResource\Pages;
use App\Models\EmployeeBenefit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeBenefitResource extends Resource
{
    protected static ?string $model = EmployeeBenefit::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Benefícios de Funcionários';

    protected static ?string $modelLabel = 'Benefício de Funcionário';

    protected static ?string $pluralModelLabel = 'Benefícios de Funcionários';

    protected static ?string $navigationGroup = 'Recursos Humanos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Atribuição de Benefício')
                    ->columns(2)
                    ->schema([
                        Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Funcionário'),

                        Select::make('benefit_id')
                            ->relationship('benefit', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Benefício'),

                        DatePicker::make('start_date')
                            ->label('Data de Início')
                            ->required(),

                        DatePicker::make('end_date')
                            ->label('Data de Fim (opcional)')
                            ->hint('Deixe vazio para benefício indefinido'),

                        TextInput::make('value_override')
                            ->numeric()
                            ->label('Valor Específico (opcional)')
                            ->hint('Deixe vazio para usar o valor padrão')
                            ->step(0.01)
                            ->minValue(0),

                        Select::make('approved_by')
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Aprovado por (opcional)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Funcionário')
                    ->searchable(['employees.first_name', 'employees.last_name'])
                    ->sortable(),

                TextColumn::make('benefit.name')
                    ->label('Benefício')
                    ->searchable(),

                TextColumn::make('start_date')
                    ->label('Início')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Fim')
                    ->date('d/m/Y')
                    ->placeholder('-'),

                TextColumn::make('value_override')
                    ->label('Valor')
                    ->formatStateUsing(fn($state, $record) => $state ? '€ ' . number_format($state, 2, ',', '.') : 'Padrão'),

                TextColumn::make('approver.name')
                    ->label('Aprovado por')
                    ->placeholder('-'),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeBenefits::route('/'),
            'create' => Pages\CreateEmployeeBenefit::route('/create'),
            'edit' => Pages\EditEmployeeBenefit::route('/{record}/edit'),
        ];
    }
}
