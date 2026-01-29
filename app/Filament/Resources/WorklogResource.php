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
    protected static ?string $navigationLabel = 'Registros de Ponto';
    protected static ?string $pluralModelLabel = 'Registros de Ponto';
    protected static ?string $navigationGroup = 'Gestão de Funcionários';
    protected static ?string $modelLabel = 'Registro de Ponto';

    /**
     * Define o formulário para criação/edição de registos de trabalho
     * Inclui seleção de funcionário, datas/horários, pausa, cálculos automáticos
     * Fluxo: campos reativos atualizam horas trabalhadas/extras; validações de intervalo
     */
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
                                $fail('Já existe um registo de horas para este funcionário nesta data.');
                            }
                        };
                    }),

                Forms\Components\DatePicker::make('work_date')
                    ->label('Data')
                    ->default(now())
                    ->required(),

                Forms\Components\TimePicker::make('start_time')
                    ->label('Início')
                    ->reactive()
                    ->displayFormat('h:i A')
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    }),

                Forms\Components\TimePicker::make('end_time')
                    ->label('Fim')
                    ->reactive()
                    ->displayFormat('h:i A')
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    }),

                Forms\Components\TimePicker::make('break_start')
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

                                $bStart = self::parseTimeFlexible($value);
                                $s = self::parseTimeFlexible($start);
                                $e = self::parseTimeFlexible($end);

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
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                    }),

                Forms\Components\TimePicker::make('break_end')
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

                                $bS = self::parseTimeFlexible($bStart);
                                $bE = self::parseTimeFlexible($value);
                                $e = self::parseTimeFlexible($end);

                                if (!$bS || !$bE || !$e) {
                                    $fail('Formato de hora inválido para fim do intervalo.');
                                    return;
                                }

                                if ($bE->lessThan($bS) || $bE->greaterThan($e)) {
                                    $fail('O fim do intervalo deve ser depois do início do intervalo e antes do horário de término.');
                                    return;
                                }

                                // limit break duration to maximum (2 hours)
                                $breakMinutes = $bS->diffInMinutes($bE);
                                if ($breakMinutes > 120) {
                                    $fail('A duração do intervalo não pode exceder 2 horas.');
                                }
                            }
                        };
                    })
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $set('hours_worked', self::calculateHoursWorked($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
                        $set('extra_hours', self::calculateExtraHours($get('start_time'), $get('end_time'), $get('break_start'), $get('break_end')));
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

    /**
     * Define a tabela de listagem de registos de trabalho
     * Modifica query para filtrar apenas registos do próprio funcionário se usuário for EMPLOYEE
     * Fluxo: query modificada -> colunas com formatação de horas -> ações baseadas em permissões
     */
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
                        ->label('Funcionário')
                    ->sortable()
                    ->searchable(['employees.first_name', 'employees.last_name']),

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

                Tables\Columns\TextColumn::make('break_start')
                    ->label('Início do Intervalo')
                    ->formatStateUsing(fn($state) => $state ? Carbon::createFromFormat('H:i:s', $state)->format('h:i A') : '-'),

                Tables\Columns\TextColumn::make('break_end')
                    ->label('Fim do Intervalo')
                    ->formatStateUsing(fn($state) => $state ? Carbon::createFromFormat('H:i:s', $state)->format('h:i A') : '-'),

                Tables\Columns\TextColumn::make('break_duration')
                    ->label('Duração do Intervalo')
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
                    ->label('Horas Trabalhadas')
                    ->sortable()
                    ->formatStateUsing(fn($state) => (int)$state . 'h'),

                Tables\Columns\TextColumn::make('extra_hours')
                    ->label('Horas Extras')
                    ->sortable()
                    ->formatStateUsing(fn($state) => (int)$state . 'h')
                    ->color(fn($state) => (int)$state > 0 ? 'danger' : 'info'),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Observações')
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

    /**
     * Converte string de tempo para objeto Carbon, tentando múltiplos formatos
     * Suporta H:i:s, H:i, h:i A
     * @param mixed $time
     * @return \Carbon\Carbon|null
     */
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

    /**
     * Calcula horas trabalhadas subtraindo tempo de pausa
     * Fluxo: parse dos tempos -> diferença total -> subtrai pausa -> retorna horas
     * @param string|null $startTime
     * @param string|null $endTime
     * @param string|null $breakStart
     * @param string|null $breakEnd
     * @return float
     */
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

    /**
     * Calcula horas extras (acima de 8 horas diárias)
     * Fluxo: calcula horas trabalhadas -> subtrai limite de 8h -> retorna máximo 0
     * @param string|null $startTime
     * @param string|null $endTime
     * @param string|null $breakStart
     * @param string|null $breakEnd
     * @return float
     */
    private static function calculateExtraHours(?string $startTime, ?string $endTime, ?string $breakStart = null, ?string $breakEnd = null): float
    {
        $total = self::calculateHoursWorked($startTime, $endTime, $breakStart, $breakEnd);
        return max(0, $total - 8); // Normal daily limit = 8h
    }
}
