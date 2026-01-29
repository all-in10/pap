<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlexibleScheduleResource\Pages;
use App\Models\FlexibleSchedule;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FlexibleScheduleResource extends Resource
{
    protected static ?string $model = FlexibleSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Jornadas Flexíveis';

    protected static ?string $modelLabel = 'Jornada Flexível';

    protected static ?string $pluralModelLabel = 'Jornadas Flexíveis';

    protected static ?string $navigationGroup = 'Recursos Humanos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações Básicas')
                    ->columns(2)
                    ->schema([
                        Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Funcionário'),

                        Select::make('designation_id')
                            ->relationship('designation', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Designação'),

                        Select::make('type')
                            ->options(FlexibleSchedule::getTypeOptions())
                            ->required()
                            ->label('Tipo de Jornada')
                            ->native(false),
                    ]),

                Section::make('Configuração de Horas')
                    ->columns(2)
                    ->schema([
                        TextInput::make('min_daily_hours')
                            ->numeric()
                            ->step(0.5)
                            ->required()
                            ->label('Mínimo de Horas Diárias')
                            ->hint('Ex: 6.5 (6h 30min)')
                            ->minValue(4),

                        TextInput::make('max_daily_hours')
                            ->numeric()
                            ->step(0.5)
                            ->required()
                            ->label('Máximo de Horas Diárias')
                            ->hint('Ex: 9.5 (9h 30min)')
                            ->maxValue(12),

                        TextInput::make('flex_days_per_week')
                            ->numeric()
                            ->required()
                            ->label('Dias com Flexibilidade por Semana')
                            ->hint('De 1 a 5 dias')
                            ->minValue(1)
                            ->maxValue(5),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Jornada Ativa')
                            ->default(true),
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

                TextColumn::make('designation.name')
                    ->label('Designação')
                    ->searchable(),

                SelectColumn::make('type')
                    ->options(FlexibleSchedule::getTypeOptions())
                    ->label('Tipo de Jornada'),

                TextColumn::make('min_daily_hours')
                    ->label('Mín. Horas/Dia')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '')),

                TextColumn::make('max_daily_hours')
                    ->label('Máx. Horas/Dia')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '')),

                TextColumn::make('flex_days_per_week')
                    ->label('Dias Flexíveis/Semana'),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Ativa'),
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
            'index' => Pages\ListFlexibleSchedules::route('/'),
            'create' => Pages\CreateFlexibleSchedule::route('/create'),
            'edit' => Pages\EditFlexibleSchedule::route('/{record}/edit'),
        ];
    }
}
