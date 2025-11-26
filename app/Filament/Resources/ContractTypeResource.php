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
                    ->formatStateUsing(function ($state) {
                        $map = [
                            'full_time' => ['label' => 'Tempo Completo', 'color' => '#a4ac86'],
                            'temporary' => ['label' => 'Temporário', 'color' => '#b6ad90'],
                            'internship' => ['label' => 'Estágio', 'color' => '#7f4f24'],
                            'non_defined' => ['label' => 'Não Definido', 'color' => '#414833'],
                        ];

                        $entry = $map[$state] ?? ['label' => (string) $state, 'color' => '#414833'];

                        return sprintf(
                            '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background: %s; color: #ffffff;">%s</span>',
                            $entry['color'],
                            e($entry['label'])
                        );
                    })
                    ->html(),
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
