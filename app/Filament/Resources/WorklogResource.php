<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorklogResource\Pages;
use App\Models\Worklog;
use App\Models\Hoursbank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class WorklogResource extends Resource
{
    protected static ?string $model = Worklog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Registros de Horas';
    protected static ?string $pluralModelLabel = 'Registros de Horas';
    protected static ?string $modelLabel = 'Registro de Hora';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Funcionário')
                    ->relationship('employee', 'first_name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->rule(function ($get, $record) {
                        return function ($attribute, $value, $fail) use ($get, $record) {
                            $exists = Worklog::where('employee_id', $value)
                                ->where('work_date', $get('work_date'))
                                ->when($record?->id, fn($query) => $query->where('id', '!=', $record->id))
                                ->exists();
                            if ($exists) {
                                $fail('Já existe um registro de horas para este funcionário nesta data.');
                            }
                        };
                    }),

                Forms\Components\DatePicker::make('work_date')
                    ->label('Data do Trabalho')
                    ->default(now())
                    ->required(),

                Forms\Components\TimePicker::make('start_time')
                    ->label('Hora Início')
                    ->reactive()
                    ->displayFormat('h:i A')
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time')));
                    }),

                Forms\Components\TimePicker::make('end_time')
                    ->label('Hora Fim')
                    ->reactive()
                    ->displayFormat('h:i A')
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time')));
                    }),

                Forms\Components\TextInput::make('hours_worked')
                    ->label('Horas Trabalhadas')
                    ->numeric()
                    ->required()
                    ->disabled(),

                Forms\Components\TextInput::make('extra_hours')
                    ->label('Horas Extras')
                    ->numeric()
                    ->required()
                    ->disabled(),

                Forms\Components\Textarea::make('notes')
                    ->label('Observações')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Funcionário')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('work_date')
                    ->label('Data')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Início')
                    ->formatStateUsing(fn($state) => Carbon::createFromFormat('H:i:s', $state)->format('h:i A')),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Fim')
                    ->formatStateUsing(fn($state) => Carbon::createFromFormat('H:i:s', $state)->format('h:i A')),

                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Horas Trabalhadas')
                    ->sortable()
                    ->formatStateUsing(fn($state) => round($state, 2) . 'h'),

                Tables\Columns\TextColumn::make('extra_hours')
                    ->label('Extras')
                    ->sortable()
                    ->formatStateUsing(fn($state) => round($state, 2) . 'h')
                    ->color(fn($state) => $state > 0 ? 'success' : 'secondary'),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Observações')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->notes),
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
            'index' => Pages\ListWorklogs::route('/'),
            'create' => Pages\CreateWorklog::route('/create'),
            'edit' => Pages\EditWorklog::route('/{record}/edit'),
        ];
    }

    /* === Funções auxiliares === */

    private static function calculateHoursWorked(?string $startTime, ?string $endTime): float
    {
        if ($startTime && $endTime) {
            $start = Carbon::createFromFormat('h:i A', $startTime);
            $end = Carbon::createFromFormat('h:i A', $endTime);

            return $start->floatDiffInHours($end);
        }

        return 0;
    }

    private static function calculateExtraHours(?string $startTime, ?string $endTime): float
    {
        $total = self::calculateHoursWorked($startTime, $endTime);
        return max(0, $total - 8); // Limite diário normal = 8h
    }

    /* === Hook para atualizar banco de horas automaticamente === */
    protected static function booted()
    {
        static::saved(function ($worklog) {
            $hoursbank = Hoursbank::firstOrCreate([
                'employee_id' => $worklog->employee_id,
            ]);

            $totalExtra = $worklog->employee->worklogs()->sum('extra_hours');
            $hoursbank->total_hours = $totalExtra;
            $hoursbank->save();
        });
    }
}
