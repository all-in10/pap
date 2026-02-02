<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeoffResource\Pages;
use App\Models\Timeoff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class TimeoffResource extends Resource
{
    protected static ?string $model = Timeoff::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Licenças';
    protected static ?string $pluralModelLabel = 'Licenças';
    protected static ?string $modelLabel = 'Licença';
    protected static ?string $navigationGroup = 'RH';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('employee_id')
                ->relationship('employee', 'first_name')
                ->label('Funcionário')
                ->searchable()
                ->preload()
                ->required(),
            DatePicker::make('start_date')->label('Data de Início')->required(),
            DatePicker::make('end_date')->label('Data de Término')->required(),
            Select::make('category_id')
                ->label('Categoria')
                ->relationship('category', 'label')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('type')
                ->label('Tipo de Licença')
                ->options(collect(\App\Models\Timeoff::TYPES)->mapWithKeys(fn($v, $k) => [$k => $v['label']])->toArray())
                ->required(),
            Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pendente',
                    'approved' => 'Aprovado',
                    'rejected' => 'Rejeitado',
                ])
                ->required(),
            Textarea::make('reason')->label('Motivo'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('ID'),
            TextColumn::make('employee.first_name')->label('Funcionário'),
            TextColumn::make('category.label')->label('Categoria')->sortable(),
            TextColumn::make('type')->label('Tipo de Licença')->sortable()
            ->formatStateUsing(fn($state) => \App\Models\Timeoff::TYPES[$state]['label'] ?? $state),
            TextColumn::make('start_date')->date()->label('Data de Início')->sortable(),
            TextColumn::make('end_date')->date()->label('Data de Término')->sortable(),
            TextColumn::make('status')->label('Status')->sortable(),
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
            'index' => Pages\ListTimeoffs::route('/'),
            'create' => Pages\CreateTimeoff::route('/create'),
            'edit' => Pages\EditTimeoff::route('/{record}/edit'),
        ];
    }
}
