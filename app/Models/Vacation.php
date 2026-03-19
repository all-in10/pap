<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RecordsActivity;

class Vacation extends Model
{
    use HasFactory, RecordsActivity, SoftDeletes;

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pendente',
        self::STATUS_APPROVED => 'Aprovado',
        self::STATUS_REJECTED => 'Rejeitado',
    ];

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'days_taken',
        'vacation_year',
        'balance_at_creation',
        'status',
        'reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_taken' => 'integer',
        'vacation_year' => 'integer',
        'balance_at_creation' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // Relacionamentos
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper methods
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED && $this->approved_by !== null && $this->approved_at !== null;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Calcula os dias de férias entre as datas
     */
    public function calculateDaysTaken(): int
    {
        if ($this->start_date && $this->end_date) {
            return $this->end_date->diffInDays($this->start_date) + 1;
        }
        return 0;
    }

    /**
     * Retorna o saldo no momento em que as férias foram criadas
     */
    public function getBalance(): int
    {
        return $this->balance_at_creation;
    }
}
