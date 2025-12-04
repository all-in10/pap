<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DesignationResource\Pages;
use App\Models\Designation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\Access;

class DesignationResource extends Resource
{
    protected static ?string $model = Designation::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Designations';
    protected static ?string $pluralModelLabel = 'Designations';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $modelLabel = 'Designation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3),

                Forms\Components\Select::make('level')
                    ->label('Level')
                    ->options([
                        'junior' => 'Junior',
                        'pleno' => 'Mid',
                        'senior' => 'Senior',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('base_salary')
                    ->label('Base Salary')
                    ->numeric()
                    ->prefix('€')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('ID')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->searchable(),
                Tables\Columns\TextColumn::make('level')
                ->label('Level')
                ->sortable(),
                Tables\Columns\TextColumn::make('base_salary')
                    ->label('Base Salary')
                    ->money('EUR', true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDesignations::route('/'),
            'create' => Pages\CreateDesignation::route('/create'),
            'edit' => Pages\EditDesignation::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Access::isRoot();
    }
}
