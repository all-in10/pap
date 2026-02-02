<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BenefitResource\Pages;
use App\Models\Benefit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class BenefitResource extends Resource
{
    protected static ?string $model = Benefit::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Benefícios';
    protected static ?string $pluralModelLabel = 'Benefícios';
    protected static ?string $modelLabel = 'Benefício';
    protected static ?string $navigationGroup = 'RH';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('employee_id')->relationship('employee', 'first_name')->label('Funcionário')->searchable()->preload()->required(),
            TextInput::make('type')->label('Tipo')->required(),
            TextInput::make('provider')->label('Fornecedor'),
            Textarea::make('details')->label('Detalhes'),
            DatePicker::make('start_date')->label('Data de Início'),
            DatePicker::make('end_date')->label('Data de Término'),
            Toggle::make('active')->label('Ativo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('ID'),
            TextColumn::make('employee.first_name')->label('Funcionário'),
            TextColumn::make('type')->label('Tipo'),
            TextColumn::make('provider')->label('Fornecedor'),
            IconColumn::make('active')->boolean()->label('Ativo'),
            TextColumn::make('created_at')->dateTime()->label('Criado em'),
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
            'index' => Pages\ListBenefits::route('/'),
            'create' => Pages\CreateBenefit::route('/create'),
            'edit' => Pages\EditBenefit::route('/{record}/edit'),
        ];
    }
}
