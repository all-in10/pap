<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Worklog extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     * Relacionamento muitos-para-um com Employee
     * Um registo de trabalho pertence a um funcionário
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Método executado quando o modelo é inicializado
     * Define eventos para salvamento, criação e exclusão de registos de trabalho
     */
    protected static function booted()
    {
        // Sempre que salvar um Worklog (criar ou atualizar)
        static::saving(function ($worklog) {
            // Calcula horas trabalhadas apenas se start_time e end_time estiverem definidos
            if ($worklog->start_time && $worklog->end_time) {
                try {
                    // Converte strings de tempo para objetos Carbon
                    $start = Carbon::createFromFormat('H:i:s', $worklog->start_time);
                    $end = Carbon::createFromFormat('H:i:s', $worklog->end_time);
                } catch (\Exception $e) {
                    // Se não conseguir analisar os tempos, evita alterar valores
                    return;
                }

                // Calcula minutos totais trabalhados entre início e fim
                $totalMinutes = $start->diffInMinutes($end);

                // Subtrai minutos de pausa se fornecidos e válidos
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

                // Minutos trabalhados = total - pausa
                $workedMinutes = max(0, $totalMinutes - $breakMinutes);

                // Armazena apenas horas inteiras (arredonda para baixo)
                $worklog->hours_worked = intdiv($workedMinutes, 60);

                // Calcula horas extras (acima do limite diário de 8 horas)
                $limite = 8;
                $worklog->extra_hours = max(0, $worklog->hours_worked - $limite);
            }
        });

        // Atualiza o banco de horas após salvar o registo
        static::saved(function ($worklog) {
            $worklog->updateHoursbank();
        });

        // Atualiza o banco de horas se excluir o registo
        static::deleted(function ($worklog) {
            $worklog->updateHoursbank();
        });
    }

    /**
     * Atualiza o total de horas no banco de horas do funcionário.
     * Fluxo: soma todas as horas extras dos registos de trabalho do funcionário e atualiza o Hoursbank
     */
    public function updateHoursbank()
    {
        $employee = $this->employee;

        if ($employee) {
            // Soma todas as horas extras dos registos de trabalho do funcionário
            $totalExtras = $employee->worklogs()->sum('extra_hours');

            // Cria ou atualiza o Hoursbank com o total de extras
            $hoursbank = $employee->hoursbank()->firstOrCreate([]);
            $hoursbank->total_hours = $totalExtras;
            $hoursbank->save();
        }
    }
}
