<?php
declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\WorklogResource\Pages;
use App\Models\Worklog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\Access;

class WorklogResource extends Resource
{
    protected static ?string $model = Worklog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Worklogs';
    protected static ?string $pluralModelLabel = 'Worklogs';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $modelLabel = 'Worklog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Funcionário')
                        ->label('Employee')
                    ->relationship('employee', 'first_name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->default(function () {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        return $u?->employee?->id ?? null;
                    })
                    ->disabled(function () {
                        // Employees should not be able to pick other employees; use Access helper
                        return Access::isEmployeeRole();
                    })
                    ->rule(function ($get, $record) {
                        return function ($attribute, $value, $fail) use ($get, $record) {
                            $exists = Worklog::where('employee_id', $value)
                                ->where('work_date', $get('work_date'))
                                    ->when($record?->id, fn($query) => $query->where('id', '!=', $record->id))
                                ->exists();
                            if ($exists) {
                                $fail('There is already a record of hours worked for this employee on this date.');
                            }
                        };
                    }),

                Forms\Components\DatePicker::make('work_date')
                    ->label('Work Date')
                    ->default(now())
                    ->required(),

                Forms\Components\TimePicker::make('start_time')
                    ->label('Start Time')
                    ->reactive()
                    ->displayFormat('h:i A')
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    }),

                Forms\Components\TimePicker::make('end_time')
                    ->label('End Time')
                    ->reactive()
                    ->displayFormat('h:i A')
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    }),

                Forms\Components\TimePicker::make('break_start')
                    ->label('Break Start')
                    ->reactive()
                    ->displayFormat('h:i A')
                    ->nullable()
                    ->rule(function ($get, $record) {
                        return function ($attribute, $value, $fail) use ($get) {
                            if ($value) {
                                $start = $get('start_time');
                                $end = $get('end_time');
                                if (!$start || !$end) {
                                    $fail('Please set Start and End times before specifying break times.');
                                    return;
                                }

                                $bStart = self::parseTimeFlexible($value);
                                $s = self::parseTimeFlexible($start);
                                $e = self::parseTimeFlexible($end);

                                if (!$bStart || !$s || !$e) {
                                    $fail('Invalid time format for break start.');
                                    return;
                                }

                                if ($bStart->lessThan($s) || $bStart->greaterThan($e)) {
                                    $fail('Break start must be between start time and end time.');
                                }
                            }
                        };
                    })
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    }),

                Forms\Components\TimePicker::make('break_end')
                    ->label('Break End')
                    ->reactive()
                    ->displayFormat('h:i A')
                    ->nullable()
                    ->rule(function ($get, $record) {
                        return function ($attribute, $value, $fail) use ($get) {
                            if ($value) {
                                $bStart = $get('break_start');
                                $end = $get('end_time');
                                if (!$bStart) {
                                    $fail('Please set Break Start before Break End.');
                                    return;
                                }
                                if (!$end) {
                                    $fail('Please set End Time before specifying break end.');
                                    return;
                                }

                                $bS = self::parseTimeFlexible($bStart);
                                $bE = self::parseTimeFlexible($value);
                                $e = self::parseTimeFlexible($end);

                                if (!$bS || !$bE || !$e) {
                                    $fail('Invalid time format for break end.');
                                    return;
                                }

                                if ($bE->lessThan($bS) || $bE->greaterThan($e)) {
                                    $fail('Break end must be after break start and before end time.');
                                    return;
                                }

                                // limit break duration to maximum (2 hours)
                                $breakMinutes = $bS->diffInMinutes($bE);
                                if ($breakMinutes > 120) {
                                    $fail('Break duration cannot exceed 2 hours.');
                                }
                            }
                        };
                    })
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    }),

                Forms\Components\TextInput::make('hours_worked')
                    ->label('Hours Worked')
                    ->numeric()
                    ->required()
                    ->disabled(),

                Forms\Components\TextInput::make('extra_hours')
                    ->label('Extra Hours')
                    ->numeric()
                    ->required()
                    ->disabled(),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(function ($query) {
            /** @var \App\Models\User|null $u */
            $u = Auth::user();
            if ($u && Access::isEmployeeRole($u)) {
                $employeeId = $u->employee?->id ?? null;
                if ($employeeId) {
                    $query->where('employee_id', $employeeId);
                }
            }

            return $query;
        })
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                        ->label('Employee')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('work_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start')
                    ->formatStateUsing(fn($state) => Carbon::createFromFormat('H:i:s', $state)->format('h:i A')),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('End')
                    ->formatStateUsing(fn($state) => Carbon::createFromFormat('H:i:s', $state)->format('h:i A')),

                Tables\Columns\TextColumn::make('break_start')
                    ->label('Break Start')
                    ->formatStateUsing(fn($state) => $state ? Carbon::createFromFormat('H:i:s', $state)->format('h:i A') : '-'),

                Tables\Columns\TextColumn::make('break_end')
                    ->label('Break End')
                    ->formatStateUsing(fn($state) => $state ? Carbon::createFromFormat('H:i:s', $state)->format('h:i A') : '-'),

                Tables\Columns\TextColumn::make('break_duration')
                    ->label('Break Duration')
                    ->formatStateUsing(function ($state, $record) {
                        $bStart = $record->break_start ?? null;
                        $bEnd = $record->break_end ?? null;

                        if (!$bStart || !$bEnd) {
                            return '-';
                        }

                        try {
                            $start = Carbon::createFromFormat('H:i:s', $bStart);
                            $end = Carbon::createFromFormat('H:i:s', $bEnd);
                        } catch (\Exception $e) {
                            return '-';
                        }

                        $minutes = (int) round($start->diffInMinutes($end));
                        if ($minutes <= 0) {
                            return '-';
                        }

                        $hours = intdiv($minutes, 60);
                        $mins = $minutes % 60;

                        if ($hours > 0) {
                            return sprintf('%dh %02dm', $hours, $mins);
                        }

                        return sprintf('%dm', $mins);
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Hours Worked')
                    ->sortable()
                    ->formatStateUsing(fn($state) => (int)$state . 'h'),

                Tables\Columns\TextColumn::make('extra_hours')
                    ->label('Extra Hours')
                    ->sortable()
                    ->formatStateUsing(fn($state) => (int)$state . 'h')
                    ->color(fn($state) => (int)$state > 0 ? 'danger' : 'info'),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->notes)
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(function ($record): bool {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        return $u !== null && $u->can('update', $record);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function ($record): bool {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        return $u !== null && $u->can('delete', $record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->filters([
                Tables\Filters\Filter::make('mine')
                    ->label('Meus Registos')
                    ->query(function ($query) {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        if ($u && Access::isEmployeeRole($u)) {
                            $employeeId = $u->employee?->id ?? null;
                            if ($employeeId) {
                                return $query->where('employee_id', $employeeId);
                            }
                        }
                        return $query;
                    }),
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

    private static function parseTimeFlexible($time)
    {
        if (!$time) {
            return null;
        }

        if ($time instanceof \DateTimeInterface) {
            return Carbon::instance($time);
        }

        if (is_string($time)) {
            // Try 24-hour format with seconds first
            try {
                return Carbon::createFromFormat('H:i:s', $time);
            } catch (\Exception $e) {
                // try without seconds
                try {
                    return Carbon::createFromFormat('H:i', $time);
                } catch (\Exception $e) {
                    // try 12-hour format with AM/PM
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
        return max(0, $total - 8); // Normal daily limit = 8h
    }
}
