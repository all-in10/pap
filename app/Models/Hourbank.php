<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hourbank extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'balance_hours',
        'last_accrual_date',
    ];

    protected $casts = [
        'balance_hours' => 'float',
        'last_accrual_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
