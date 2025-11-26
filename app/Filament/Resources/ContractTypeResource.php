<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractTypeResource\Pages;
use App\Filament\Resources\ContractTypeResource\RelationManagers;
use App\Models\ContractType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractTypeResource extends Resource
{
    protected static ?string $model = ContractType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome do Tipo de Contrato')
                    ->required()
                    ->unique(ContractType::class, 'name', ignoreRecord: true),
                Forms\Components\Select::make('category')
                    ->label('Categoria')
                    ->options([
                        'full_time' => 'Tempo Completo',
                        'temporary' => 'Temporário',
                        'internship' => 'Estágio',
                        'non_defined' => 'Não Definido',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'full_time' => 'Tempo Completo',
                        'temporary' => 'Temporário',
                        'internship' => 'Estágio',
                        'non_defined' => 'Não Definido',
                        default => $state,
                    })
                    ->color(fn(string $state) => match ($state) {
                        'full_time' => 'success',
                        'temporary' => 'warning',
                        'internship' => 'info',
                        'non_defined' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractTypes::route('/'),
            'create' => Pages\CreateContractType::route('/create'),
            'edit' => Pages\EditContractType::route('/{record}/edit'),
        ];
    }
}
