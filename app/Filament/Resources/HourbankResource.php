<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HourbankResource\Pages;
use App\Models\Hourbank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;

class HourbankResource extends Resource
{
    protected static ?string $model = Hourbank::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Banco de Horas';
    protected static ?string $pluralModelLabel = 'Bancos de Horas';
    protected static ?string $modelLabel = 'Banco de Horas';
    protected static ?string $navigationGroup = 'Funcionários';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('employee_id')->relationship('employee', 'first_name')->label('Funcionário')->searchable()->preload()->required(),
            TextInput::make('balance_hours')->label('Saldo de Horas')->numeric()->step(0.25),
            DatePicker::make('last_accrual_date')->label('Último Acúmulo'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('ID')->hidden(true),
            TextColumn::make('employee.first_name')->label('Funcionário')->searchable()->sortable(),
            TextColumn::make('balance_hours')->label('Saldo de Horas')->searchable()->sortable(),
            TextColumn::make('last_accrual_date')->date()->label('Último Acúmulo'),
            TextColumn::make('created_at')->dateTime()->label('Criado em'),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListHourbanks::route('/'),
            'create' => Pages\CreateHourbank::route('/create'),
            'edit' => Pages\EditHourbank::route('/{record}/edit'),
        ];
    }
}
