<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Validator;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'designation_id',
        'employee_id',
        'contract_type_id',
        'salary',
        'start_date',
        'end_date',
        'contract_file_path',
        'status',
        'date_hired',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'date_hired' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * Validações automáticas ao salvar
     */
    protected static function booted()
    {
        static::saving(function ($contract) {
            // Validar que salary é positivo
            if ($contract->salary <= 0) {
                throw new \InvalidArgumentException('Salary must be greater than zero');
            }

            // Validar que end_date >= start_date
            if ($contract->end_date && $contract->end_date->isBefore($contract->start_date)) {
                throw new \InvalidArgumentException('Contract end date must be after start date');
            }

            // Validar status válido
            $validStatuses = ['active', 'terminated', 'suspended'];
            if (!in_array($contract->status, $validStatuses)) {
                throw new \InvalidArgumentException('Invalid contract status');
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope para filtrar contratos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isTerminated(): bool
    {
        return $this->status === 'terminated';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Verifica se o contrato está dentro do período válido
     */
    public function isWithinPeriod(): bool
    {
        $now = now();
        $startOk = $now->greaterThanOrEqualTo($this->start_date);
        $endOk = !$this->end_date || $now->lessThanOrEqualTo($this->end_date);
        return $startOk && $endOk;
    }
}
