<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeoffResource\Pages;
use App\Models\Timeoff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TimeoffResource extends Resource
{
    protected static ?string $model = Timeoff::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Pedidos de Folga';
    protected static ?string $pluralModelLabel = 'Pedidos de Folga';
    protected static ?string $modelLabel = 'Pedido de Folga';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->label('Funcionário')
                ->relationship('employee', 'first_name')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\DatePicker::make('start_date')
                ->label('Início')
                ->native(false)
                ->required(),

            Forms\Components\DatePicker::make('end_date')
                ->label('Fim')
                ->native(false)
                ->required(),

            Forms\Components\Select::make('type')
                ->label('Tipo de Folga')
                ->options([
                    'vacation'       => 'Férias',
                    'sick_leave'     => 'Licença Médica',
                    'personal_leave' => 'Licença Pessoal',
                    'other'          => 'Outro',
                ])
                ->native(false)
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'pending'  => 'Pendente',
                    'approved' => 'Aprovado',
                    'rejected' => 'Rejeitado',
                ])
                ->default('pending')
                ->required(),

            Forms\Components\Textarea::make('reason')
                ->label('Motivo')
                ->rows(3)
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('employee.first_name')->label('Funcionário')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('start_date')->label('Início')->date(),
            Tables\Columns\TextColumn::make('end_date')->label('Fim')->date(),
            Tables\Columns\TextColumn::make('type')->label('Tipo'),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge(fn(string $state) => match ($state) {
                    'pending'  => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default    => 'gray',
                }),

            Tables\Columns\TextColumn::make('reason')->label('Motivo')->limit(50),
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
