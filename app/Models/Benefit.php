<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordsActivity;

class Benefit extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'employee_id',
        'type',
        'provider',
        'details',
        'start_date',
        'end_date',
        'active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
