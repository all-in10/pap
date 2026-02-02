<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeoffCategoryResource\Pages;
use App\Models\TimeoffCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TimeoffCategoryResource extends Resource
{
    protected static ?string $model = TimeoffCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Categorias de Licença';
    protected static ?string $pluralModelLabel = 'Categorias de Licença';
    protected static ?string $modelLabel = 'Categoria de Licença';
    protected static ?string $navigationGroup = 'RH';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->label('Chave')->required()->maxLength(50),
            Forms\Components\TextInput::make('label')->label('Nome')->required()->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('ID'),
            Tables\Columns\TextColumn::make('key')->label('Chave'),
            Tables\Columns\TextColumn::make('label')->label('Nome'),
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
            'index' => Pages\ListTimeoffCategories::route('/'),
            'create' => Pages\CreateTimeoffCategory::route('/create'),
            'edit' => Pages\EditTimeoffCategory::route('/{record}/edit'),
        ];
    }
}
