<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    protected $table = 'performance_reviews';

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_period',
        'rating',
        'comments',
        'goals_met',
        'strengths',
        'improvements',
        'recommended_raise',
    ];

    protected $casts = [
        'rating' => 'float',
        'goals_met' => 'integer',
        'strengths' => 'array',
        'improvements' => 'array',
        'recommended_raise' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: PerformanceReview belongs to Employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relationship: PerformanceReview belongs to Reviewer (User)
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get review period options
     */
    public static function getPeriodOptions(): array
    {
        return [
            'semestral' => 'Semestral',
            'anual' => 'Anual',
            'probation' => 'Período de Experiência',
        ];
    }

    /**
     * Get rating scale
     */
    public static function getRatingOptions(): array
    {
        return [
            '1' => '1 - Necessita Melhorias',
            '2' => '2 - Abaixo do Esperado',
            '3' => '3 - Atende Expectativas',
            '4' => '4 - Excepcional',
            '5' => '5 - Extraordinário',
        ];
    }

    /**
     * Get average rating for an employee
     */
    public static function getEmployeeAverageRating(int $employeeId): float
    {
        return static::where('employee_id', $employeeId)
            ->avg('rating') ?? 0;
    }

    /**
     * Get latest review for an employee
     */
    public static function getLatestReview(int $employeeId): ?self
    {
        return static::where('employee_id', $employeeId)
            ->latest('created_at')
            ->first();
    }
}
