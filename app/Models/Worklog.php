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
        'end_time',
        'hours_worked',
        'extra_hours',
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
                $start = Carbon::createFromFormat('H:i:s', $worklog->start_time);
                $end = Carbon::createFromFormat('H:i:s', $worklog->end_time);

                // Calcula horas trabalhadas
                $worklog->hours_worked = $start->floatDiffInHours($end);

                // Calcula extras (acima de 8h/dia)
                $limite = 8;
                $worklog->extra_hours = max(0, $worklog->hours_worked - $limite);
            }
        });

        // Atualiza o banco de horas após salvar
        static::saved(function ($worklog) {
            $worklog->updateHoursbank();
        });

        // Atualiza o banco de horas se excluir o Worklog
        static::deleted(function ($worklog) {
            $worklog->updateHoursbank();
        });
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
