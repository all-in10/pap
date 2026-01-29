<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\NotificationService;

class Timeoff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'type',
        'status',
        'reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Validações automáticas ao salvar
     */
    protected static function booted()
    {
        static::saving(function ($timeoff) {
            // Validar que end_date >= start_date
            if ($timeoff->end_date->isBefore($timeoff->start_date)) {
                throw new \InvalidArgumentException('Timeoff end date must be after start date');
            }

            // Validar tipo válido
            $validTypes = ['vacation', 'sick_leave', 'personal_leave', 'other'];
            if (!in_array($timeoff->type, $validTypes)) {
                throw new \InvalidArgumentException('Invalid timeoff type');
            }

            // Validar status válido
            $validStatuses = ['pending', 'approved', 'rejected'];
            if (!in_array($timeoff->status, $validStatuses)) {
                throw new \InvalidArgumentException('Invalid timeoff status');
            }

            // Validar overlap com timeoffs aprovados do mesmo employee
            // Impede criar timeoffs que se sobrepõem com aprovações existentes
            $overlapping = Timeoff::where('employee_id', $timeoff->employee_id)
                ->where('status', 'approved')
                ->where('id', '!=', $timeoff->id ?? 0) // Exclude current timeoff if updating
                ->where(function ($query) use ($timeoff) {
                    $query->whereBetween('start_date', [$timeoff->start_date, $timeoff->end_date])
                          ->orWhereBetween('end_date', [$timeoff->start_date, $timeoff->end_date])
                          ->orWhere(function ($q) use ($timeoff) {
                              $q->where('start_date', '<=', $timeoff->start_date)
                                ->where('end_date', '>=', $timeoff->end_date);
                          });
                })
                ->exists();

            if ($overlapping) {
                throw new \InvalidArgumentException('Employee already has an approved timeoff during this period');
            }
        });

        // Disparar eventos quando o status muda
        static::updating(function ($timeoff) {
            // Verifica se o status foi alterado
            if ($timeoff->isDirty('status')) {
                $originalStatus = $timeoff->getOriginal('status');
                $newStatus = $timeoff->status;

                // Se foi aprovado
                if ($originalStatus !== 'approved' && $newStatus === 'approved') {
                    NotificationService::sendTimeoffApprovedNotification($timeoff);
                }

                // Se foi rejeitado
                if ($originalStatus !== 'rejected' && $newStatus === 'rejected') {
                    NotificationService::sendTimeoffRejectedNotification($timeoff);
                }
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Retorna o número de dias de folga
     */
    public function getDaysCount(): int
    {
        return abs($this->end_date->diffInDays($this->start_date)) + 1;
    }

    /**
     * Verifica se é uma solicitação pendente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verifica se foi aprovada
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Verifica se foi rejeitada
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Retorna o label do tipo em português
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'vacation' => 'Férias',
            'sick_leave' => 'Licença Médica',
            'personal_leave' => 'Licença Pessoal',
            'other' => 'Outro',
            default => 'Desconhecido',
        };
    }
}
