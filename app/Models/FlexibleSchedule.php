<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlexibleSchedule extends Model
{
    protected $table = 'flexible_schedules';

    protected $fillable = [
        'employee_id',
        'designation_id',
        'type',
        'min_daily_hours',
        'max_daily_hours',
        'flex_days_per_week',
        'is_active',
    ];

    protected $casts = [
        'min_daily_hours' => 'float',
        'max_daily_hours' => 'float',
        'flex_days_per_week' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: FlexibleSchedule belongs to Employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relationship: FlexibleSchedule belongs to Designation
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Validate if total weekly hours are within limits (40h)
     */
    public function getTotalFlexibleWeeklyHours(): float
    {
        return $this->max_daily_hours * $this->flex_days_per_week;
    }

    /**
     * Check if flexible schedule is valid
     */
    public function isValid(): bool
    {
        // Max daily hours must be > min daily hours
        if ($this->max_daily_hours <= $this->min_daily_hours) {
            return false;
        }

        // Total flexible weekly hours should not exceed 40h
        if ($this->getTotalFlexibleWeeklyHours() > 40) {
            return false;
        }

        // flex_days_per_week must be between 1-5
        if ($this->flex_days_per_week < 1 || $this->flex_days_per_week > 5) {
            return false;
        }

        return true;
    }

    /**
     * Get schedule type options
     */
    public static function getTypeOptions(): array
    {
        return [
            'fixed' => 'Jornada Fixa (8h)',
            'flexible' => 'Jornada Flexível',
            '4x3' => '4 dias trabalho / 3 de folga',
        ];
    }
}
