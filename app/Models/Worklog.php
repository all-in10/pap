<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Worklog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'work_date',
        'start_time',
        'break_start',
        'break_end',
        'end_time',
        'hours_worked',
        'extra_hours',
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
        // Sempre que criar ou atualizar um Worklog
        static::saving(function ($worklog) {
            if ($worklog->start_time && $worklog->end_time) {
                try {
                    $start = Carbon::createFromFormat('H:i:s', $worklog->start_time);
                    $end = Carbon::createFromFormat('H:i:s', $worklog->end_time);
                } catch (\Exception $e) {
                    // If times cannot be parsed, avoid changing values
                    return;
                }

                // Total worked minutes between start and end
                $totalMinutes = $start->diffInMinutes($end);

                // Subtract break minutes if provided and valid
                $breakMinutes = 0;
                if (!empty($worklog->break_start) && !empty($worklog->break_end)) {
                    try {
                        $bStart = Carbon::createFromFormat('H:i:s', $worklog->break_start);
                        $bEnd = Carbon::createFromFormat('H:i:s', $worklog->break_end);
                        $breakMinutes = $bStart->diffInMinutes($bEnd);
                        if ($breakMinutes < 0) {
                            $breakMinutes = 0;
                        }
                    } catch (\Exception $e) {
                        $breakMinutes = 0;
                    }
                }

                $workedMinutes = max(0, $totalMinutes - $breakMinutes);

                // Store only whole hours (floor), as requested
                $worklog->hours_worked = intdiv($workedMinutes, 60);

                // Calculate extra hours (hours above daily limit)
                $limite = 8;
                $worklog->extra_hours = max(0, $worklog->hours_worked - $limite);
            }
        });

        // Audit log para criação, edição e remoção
        static::created(function ($worklog) {
            \App\Services\Audit::recordModelEvent('created', $worklog);
        });
        static::updated(function ($worklog) {
            \App\Services\Audit::recordModelEvent('updated', $worklog);
        });
        static::deleted(function ($worklog) {
            \App\Services\Audit::recordModelEvent('deleted', $worklog);
        });

        // Atualiza o banco de horas após salvar
        static::saved(function ($worklog) {
            $worklog->updateHoursbank();
        });

        // Atualiza o banco de horas se excluir o Worklog
        // (já coberto acima)
    }

    /**
     * Atualiza o total de horas no banco de horas do funcionário.
     */
    public function updateHoursbank()
    {
        $employee = $this->employee;

        if ($employee) {
            $totalExtras = $employee->worklogs()->sum('extra_hours');

            // Cria ou atualiza o Hoursbank
            $hoursbank = $employee->hoursbank()->firstOrCreate([]);
            $hoursbank->total_hours = $totalExtras;
            $hoursbank->save();
        }
    }
}
