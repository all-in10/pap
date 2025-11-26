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

class HoursbankResource extends Resource
{
    protected static ?string $model = Hoursbank::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Banco de Horas';
    protected static ?string $pluralModelLabel = 'Bancos de Horas';
    protected static ?string $navigationGroup = 'Gestão de Funcionários';
    protected static ?string $modelLabel = 'Banco de Horas';

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
                    ->label('Horas Totais')
                    ->numeric()
                    ->disabled()
                    ->dehydrateStateUsing(fn($get) => $get('total_hours')), // apenas leitura
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

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Horas Totais')
                    ->formatStateUsing(fn($record) => round($record->worklogs()->sum('extra_hours'), 2) . 'h')
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

    public static function getRelations(): array
    {
        return [
            WorklogsRelationManager::class, // relaciona os Worklogs do funcionário
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHoursbanks::route('/'),
            'create' => Pages\CreateHoursbank::route('/create'),
            'edit' => Pages\EditHoursbank::route('/{record}/edit'),
        ];
    }
}
