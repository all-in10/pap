<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * Converte string de tempo para objeto Carbon, tentando múltiplos formatos
     */
    public static function parseTimeFlexible($time)
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

    /**
     * Calcula horas trabalhadas subtraindo tempo de pausa
     */
    public static function calculateHoursWorked($startTime, $endTime, $breakStart = null, $breakEnd = null): float
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
     */
    public static function calculateExtraHours($startTime, $endTime, $breakStart = null, $breakEnd = null): float
    {
        $total = self::calculateHoursWorked($startTime, $endTime, $breakStart, $breakEnd);
        return max(0, $total - 8);
    }

    protected $fillable = [
        'employee_id',
        'work_date',
        'start_time',
        'break_start',
        'break_end',
        'end_time',
        'hours_worked',
        'extra_hours',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'hours_worked' => 'integer',
        'extra_hours' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function booted()
    {
        static::saving(function ($attendance) {
            if ($attendance->start_time && $attendance->end_time) {
                try {
                    $start = Carbon::createFromFormat('H:i:s', $attendance->start_time);
                    $end = Carbon::createFromFormat('H:i:s', $attendance->end_time);
                } catch (\Exception $e) {
                    return;
                }

                $totalMinutes = $start->diffInMinutes($end);

                $breakMinutes = 0;
                if (!empty($attendance->break_start) && !empty($attendance->break_end)) {
                    try {
                        $bStart = Carbon::createFromFormat('H:i:s', $attendance->break_start);
                        $bEnd = Carbon::createFromFormat('H:i:s', $attendance->break_end);
                        $breakMinutes = $bStart->diffInMinutes($bEnd);
                        if ($breakMinutes < 0) {
                            $breakMinutes = 0;
                        }
                    } catch (\Exception $e) {
                        $breakMinutes = 0;
                    }
                }

                $workedMinutes = max(0, $totalMinutes - $breakMinutes);
                $attendance->hours_worked = intdiv($workedMinutes, 60);
                $limite = 8;
                $attendance->extra_hours = max(0, $attendance->hours_worked - $limite);
            }
        });

        static::saved(function ($attendance) {
            $attendance->updateHoursbank();
        });

        static::deleted(function ($attendance) {
            $attendance->updateHoursbank();
        });
    }

    public function updateHoursbank()
    {
        $employee = $this->employee;
        if ($employee) {
            $totalExtras = $employee->attendances()->sum('extra_hours');
            $hoursbank = $employee->hourbanks()->firstOrCreate([]);
            $hoursbank->balance_hours = $totalExtras;
            $hoursbank->save();
        }
    }
}
