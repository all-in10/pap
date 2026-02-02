<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Employee;
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
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use App\Support\Access;
use Carbon\Carbon;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Registo de Horas';
    protected static ?string $pluralModelLabel = 'Registos de Horas';
    protected static ?string $modelLabel = 'Registo de Horas';
    protected static ?string $navigationGroup = 'Funcionários';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('employee_id')
                ->label('Funcionário')
                ->relationship('employee', 'first_name')
                ->preload()
                ->searchable()
                ->required()
                ->default(function () {
                    $u = Auth::user();
                    return $u?->employee?->id ?? null;
                })
                ->rule(function ($get, $record) {
                    return function ($attribute, $value, $fail) use ($get, $record) {
                        $exists = Attendance::where('employee_id', $value)
                            ->where('work_date', $get('work_date'))
                            ->when($record?->id, fn($query) => $query->where('id', '!=', $record->id))
                            ->exists();
                        if ($exists) {
                            $fail('Já existe um registo de horas para este funcionário nesta data.');
                        }
                    };
                }),

            DatePicker::make('work_date')
                ->label('Data')
                ->default(now())
                ->required(),

            TimePicker::make('start_time')
                ->label('Início')
                ->reactive()
                ->displayFormat('h:i A')
                ->required()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $set('hours_worked', Attendance::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    $set('extra_hours', Attendance::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                }),

            TimePicker::make('end_time')
                ->label('Fim')
                ->reactive()
                ->displayFormat('h:i A')
                ->required()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $set('hours_worked', Attendance::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    $set('extra_hours', Attendance::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                }),

            TimePicker::make('break_start')
                ->label('Início do Intervalo')
                ->reactive()
                ->displayFormat('h:i A')
                ->nullable()
                ->rule(function ($get, $record) {
                    return function ($attribute, $value, $fail) use ($get) {
                        if ($value) {
                            $start = $get('start_time');
                            $end = $get('end_time');
                            if (!$start || !$end) {
                                $fail('Por favor, defina os horários de Início e Fim antes de especificar os horários do intervalo.');
                                return;
                            }
                            $bStart = Attendance::parseTimeFlexible($value);
                            $s = Attendance::parseTimeFlexible($start);
                            $e = Attendance::parseTimeFlexible($end);
                            if (!$bStart || !$s || !$e) {
                                $fail('Formato de hora inválido para início do intervalo.');
                                return;
                            }
                            if ($bStart->lessThan($s) || $bStart->greaterThan($e)) {
                                $fail('O início do intervalo deve estar entre o início e o fim do turno.');
                            }
                        }
                    };
                })
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $set('hours_worked', Attendance::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    $set('extra_hours', Attendance::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                }),

            TimePicker::make('break_end')
                ->label('Fim do Intervalo')
                ->reactive()
                ->displayFormat('h:i A')
                ->nullable()
                ->rule(function ($get, $record) {
                    return function ($attribute, $value, $fail) use ($get) {
                        if ($value) {
                            $bStart = $get('break_start');
                            $end = $get('end_time');
                            if (!$bStart) {
                                $fail('Por favor, defina Início do Intervalo antes do Fim do Intervalo.');
                                return;
                            }
                            if (!$end) {
                                $fail('Por favor, defina o horário de Fim antes de especificar o fim do intervalo.');
                                return;
                            }
                            $bS = Attendance::parseTimeFlexible($bStart);
                            $bE = Attendance::parseTimeFlexible($value);
                            $e = Attendance::parseTimeFlexible($end);
                            if (!$bS || !$bE || !$e) {
                                $fail('Formato de hora inválido para fim do intervalo.');
                                return;
                            }
                            if ($bE->lessThan($bS) || $bE->greaterThan($e)) {
                                $fail('O fim do intervalo deve ser depois do início do intervalo e antes do horário de término.');
                                return;
                            }
                            $breakMinutes = $bS->diffInMinutes($bE);
                            if ($breakMinutes > 120) {
                                $fail('A duração do intervalo não pode exceder 2 horas.');
                            }
                        }
                    };
                })
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $set('hours_worked', Attendance::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    $set('extra_hours', Attendance::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                }),

            TextInput::make('hours_worked')
                ->label('Horas Trabalhadas')
                ->numeric()
                ->required()
                ->disabled(),

            TextInput::make('extra_hours')
                ->label('Horas Extras')
                ->numeric()
                ->required()
                ->disabled(),

            Textarea::make('notes')
                ->label('Observações')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('employee.first_name')
                ->label('Funcionário')
                ->sortable()
                ->searchable(),

            TextColumn::make('work_date')
                ->label('Data')
                ->date()
                ->sortable(),

            TextColumn::make('start_time')
                ->label('Início')
                ->formatStateUsing(fn($state) => Carbon::createFromFormat('H:i:s', $state)->format('h:i A')),

            TextColumn::make('end_time')
                ->label('Fim')
                ->formatStateUsing(fn($state) => Carbon::createFromFormat('H:i:s', $state)->format('h:i A')),

            TextColumn::make('break_start')
                ->label('Início do Intervalo')
                ->formatStateUsing(fn($state) => $state ? Carbon::createFromFormat('H:i:s', $state)->format('h:i A') : '-'),

            TextColumn::make('break_end')
                ->label('Fim do Intervalo')
                ->formatStateUsing(fn($state) => $state ? Carbon::createFromFormat('H:i:s', $state)->format('h:i A') : '-'),

            TextColumn::make('hours_worked')
                ->label('Horas Trabalhadas')
                ->sortable()
                ->formatStateUsing(fn($state) => (int)$state . 'h'),

            TextColumn::make('extra_hours')
                ->label('Horas Extras')
                ->sortable()
                ->formatStateUsing(fn($state) => (int)$state . 'h')
                ->color(fn($state) => (int)$state > 0 ? 'danger' : 'info'),

            TextColumn::make('notes')
                ->label('Observações')
                ->limit(30)
                ->tooltip(fn($record) => $record->notes)
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    /* === Funções auxiliares === */
    private static function parseTimeFlexible($time)
    {
        if (!$time) {
            return null;
        }
        if ($time instanceof \DateTimeInterface) {
            return Carbon::instance($time);
        }
        if (is_string($time)) {
            try {
                return Carbon::createFromFormat('H:i:s', $time);
            } catch (\Exception $e) {
                try {
                    return Carbon::createFromFormat('H:i', $time);
                } catch (\Exception $e) {
                    try {
                        return Carbon::createFromFormat('h:i A', $time);
                    } catch (\Exception $e) {
                        return null;
                    }
                }
            }
        }
        return null;
    }

    private static function calculateHoursWorked(?string $startTime, ?string $endTime, ?string $breakStart = null, ?string $breakEnd = null): float
    {
        $start = self::parseTimeFlexible($startTime);
        $end = self::parseTimeFlexible($endTime);
        if ($start && $end) {
            $total = $start->floatDiffInHours($end);
            $bStart = self::parseTimeFlexible($breakStart);
            $bEnd = self::parseTimeFlexible($breakEnd);
            $breakDuration = 0;
            if ($bStart && $bEnd) {
                $breakDuration = $bStart->floatDiffInHours($bEnd);
            }
            return max(0, $total - $breakDuration);
        }
        return 0;
    }

    private static function calculateExtraHours(?string $startTime, ?string $endTime, ?string $breakStart = null, ?string $breakEnd = null): float
    {
        $total = self::calculateHoursWorked($startTime, $endTime, $breakStart, $breakEnd);
        return max(0, $total - 8);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
