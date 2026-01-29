<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerformanceReviewResource\Pages;
use App\Models\PerformanceReview;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PerformanceReviewResource extends Resource
{
    protected static ?string $model = PerformanceReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Avaliações de Desempenho';

    protected static ?string $modelLabel = 'Avaliação de Desempenho';

    protected static ?string $pluralModelLabel = 'Avaliações de Desempenho';

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

                        Select::make('reviewer_id')
                            ->relationship('reviewer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Avaliador'),

                        Select::make('review_period')
                            ->options(PerformanceReview::getPeriodOptions())
                            ->required()
                            ->label('Período da Avaliação')
                            ->native(false),

                        Select::make('rating')
                            ->options(PerformanceReview::getRatingOptions())
                            ->required()
                            ->label('Classificação')
                            ->native(false),
                    ]),

                Section::make('Avaliação Detalhada')
                    ->columns(1)
                    ->schema([
                        TextInput::make('goals_met')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->label('Metas Alcançadas (%)')
                            ->hint('Porcentagem de 0 a 100'),

                        TextInput::make('recommended_raise')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->label('Aumento Recomendado (%)')
                            ->hint('Porcentagem de aumento salarial'),

                        Textarea::make('comments')
                            ->label('Comentários Gerais')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pontos Fortes e Melhorias')
                    ->columns(2)
                    ->schema([
                        Textarea::make('strengths')
                            ->label('Pontos Fortes')
                            ->rows(4)
                            ->hint('Separados por vírgula ou quebra de linha'),

                        Textarea::make('improvements')
                            ->label('Áreas para Melhoria')
                            ->rows(4)
                            ->hint('Separados por vírgula ou quebra de linha'),
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

                TextColumn::make('reviewer.name')
                    ->label('Avaliador')
                    ->searchable(),

                SelectColumn::make('review_period')
                    ->options(PerformanceReview::getPeriodOptions())
                    ->label('Período'),

                TextColumn::make('rating')
                    ->label('Classificação')
                    ->formatStateUsing(fn($state) => match($state) {
                        '1' => '⭐ Necessita Melhorias',
                        '2' => '⭐⭐ Abaixo do Esperado',
                        '3' => '⭐⭐⭐ Atende Expectativas',
                        '4' => '⭐⭐⭐⭐ Excepcional',
                        '5' => '⭐⭐⭐⭐⭐ Extraordinário',
                        default => $state,
                    }),

                TextColumn::make('goals_met')
                    ->label('Metas Alcançadas')
                    ->formatStateUsing(fn($state) => "{$state}%"),

                TextColumn::make('recommended_raise')
                    ->label('Aumento Recomendado')
                    ->formatStateUsing(fn($state) => "{$state}%"),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
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
            'index' => Pages\ListPerformanceReviews::route('/'),
            'create' => Pages\CreatePerformanceReview::route('/create'),
            'edit' => Pages\EditPerformanceReview::route('/{record}/edit'),
        ];
    }
}
