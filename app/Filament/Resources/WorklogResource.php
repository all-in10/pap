<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorklogResource\Pages;
use App\Models\Worklog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class WorklogResource extends Resource
{
    protected static ?string $model = Worklog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Apontamentos';
    protected static ?string $pluralModelLabel = 'Apontamentos';
    protected static ?string $modelLabel = 'Apontamento';
    protected static ?string $navigationGroup = 'Funcionários';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('employee_id')->relationship('employee', 'first_name')->label('Funcionário')->searchable()->preload()->required(),
                DatePicker::make('date')->label('Data')->required(),
                TimePicker::make('start_time')->label('Hora Início'),
                TimePicker::make('end_time')->label('Hora Término'),
                TextInput::make('duration')->label('Duração')->numeric()->step(0.25),
                TextInput::make('type')->label('Tipo'),
                Toggle::make('approved')->label('Aprovado'),
                Textarea::make('description')->label('Descrição'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('employee.first_name')->label('Funcionário'),
                TextColumn::make('date')->date()->label('Data'),
                TextColumn::make('duration')->label('Duração'),
                TextColumn::make('type')->label('Tipo'),
                IconColumn::make('approved')->boolean()->label('Aprovado'),
                TextColumn::make('created_at')->dateTime()->label('Criado em'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorklogs::route('/'),
            'create' => Pages\CreateWorklog::route('/create'),
            'edit' => Pages\EditWorklog::route('/{record}/edit'),
        ];
    }
}
