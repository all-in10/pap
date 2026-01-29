<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BenefitResource\Pages;
use App\Models\Benefit;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class BenefitResource extends Resource
{
    protected static ?string $model = Benefit::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Benefícios';

    protected static ?string $modelLabel = 'Benefício';

    protected static ?string $pluralModelLabel = 'Benefícios';

    protected static ?string $navigationGroup = 'Recursos Humanos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Benefício')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome do Benefício')
                            ->placeholder('Ex: Vale Refeição')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->options(Benefit::getTypeOptions())
                            ->label('Tipo de Benefício')
                            ->required()
                            ->native(false),

                        TextInput::make('value')
                            ->numeric()
                            ->label('Valor Padrão (€)')
                            ->required()
                            ->step(0.01)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                    ]),

                Section::make('Descrição')
                    ->schema([
                        Textarea::make('description')
                            ->label('Descrição do Benefício')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Benefício')
                    ->searchable()
                    ->sortable(),

                SelectColumn::make('type')
                    ->options(Benefit::getTypeOptions())
                    ->label('Tipo'),

                TextColumn::make('value')
                    ->label('Valor')
                    ->formatStateUsing(fn($state) => '€ ' . number_format($state, 2, ',', '.'))
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Ativo'),
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
            'index' => Pages\ListBenefits::route('/'),
            'create' => Pages\CreateBenefit::route('/create'),
            'edit' => Pages\EditBenefit::route('/{record}/edit'),
        ];
    }
}
