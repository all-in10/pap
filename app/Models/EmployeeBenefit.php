<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBenefit extends Model
{
    protected $table = 'employee_benefits';

    protected $fillable = [
        'employee_id',
        'benefit_id',
        'start_date',
        'end_date',
        'value_override',
        'approved_by',
    ];

    protected $casts = [
        'value_override' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relationship: EmployeeBenefit belongs to Employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relationship: EmployeeBenefit belongs to Benefit
     */
    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
    }

    /**
     * Relationship: EmployeeBenefit belongs to Approver (User)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the active benefit value (override or default)
     */
    public function getActiveValue()
    {
        return $this->value_override ?? $this->benefit->value;
    }

    /**
     * Check if benefit is currently active (between dates)
     */
    public function isActive(): bool
    {
        $now = now()->toDateString();
        
        if ($this->start_date && $this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted benefit value
     */
    public function getFormattedValue(): string
    {
        return 'R$ ' . number_format($this->getActiveValue(), 2, ',', '.');
    }
}
