<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RecordsActivity;

class Contract extends Model
{
    use HasFactory, RecordsActivity, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'contract_type_id',
        'salary',
        'start_date',
        'end_date',
        'status',
        'date_hired',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'date_hired' => 'date',
        'salary' => 'decimal:2',
    ];

    protected static function booted()
    {
        // Defaults caso criado manualmente
        static::creating(function ($contract) {
            $contract->status ??= 'active';
            $contract->contract_type_id ??= \App\Models\ContractType::firstWhere('name', 'sem_termo')->id ?? null;
            $contract->salary ??= 0;
        });
    }

    // RELACIONAMENTO
    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }
    // RELACIONAMENTO
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verifica se este contrato requer data de fim
     */
    public function requiresEndDate(): bool
    {
        return $this->contractType?->requiresEndDate() ?? false;
    }

    /**
     * Obtém o tipo de contrato em formato legível
     */
    public function getContractTypeLabel(): string
    {
        return $this->contractType?->label ?? 'Tipo não definido';
    }

    /**
     * Verifica se a data de fim é válida (se necessária)
     */
    public function hasValidEndDate(): bool
    {
        if (!$this->requiresEndDate()) {
            return true;
        }

        return $this->end_date !== null && $this->end_date > $this->start_date;
    }
}