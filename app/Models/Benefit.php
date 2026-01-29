<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Benefit extends Model
{
    protected $table = 'benefits';

    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Benefit has many EmployeeBenefit
     */
    public function employeeBenefits(): HasMany
    {
        return $this->hasMany(EmployeeBenefit::class);
    }

    /**
     * Get benefit type options
     */
    public static function getTypeOptions(): array
    {
        return [
            'monthly' => 'Mensal',
            'annual' => 'Anual',
            'one_time' => 'Único',
        ];
    }

    /**
     * Get total value for a benefit type
     */
    public function getTotalValue(): string
    {
        return $this->type === 'monthly'
            ? number_format($this->value, 2, ',', '.')
            : number_format($this->value, 2, ',', '.');
    }
}
