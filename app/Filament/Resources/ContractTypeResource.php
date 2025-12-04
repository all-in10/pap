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
use App\Services\Access;

class ContractTypeResource extends Resource
{
    protected static ?string $model = ContractType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->unique(ContractType::class, 'name', ignoreRecord: true),
                Forms\Components\Select::make('category')
                    ->label('Category')
                    ->options([
                        'full_time' => 'Full Time',
                        'temporary' => 'Temporary',
                        'internship' => 'Internship',
                        'non_defined' => 'Undefined',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->formatStateUsing(function ($state) {
                        $map = [
                            'full_time' => ['label' => 'Full Time', 'color' => '#a4ac86'],
                            'temporary' => ['label' => 'Temporary', 'color' => '#b6ad90'],
                            'internship' => ['label' => 'Internship', 'color' => '#7f4f24'],
                            'non_defined' => ['label' => 'Non defined', 'color' => '#414833'],
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
                    ->label('Description')
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

    public static function canAccess(): bool
    {
        return Access::isRoot();
    }
}
