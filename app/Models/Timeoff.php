<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeoff extends Model
{
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'type',
        'status',
        'reason',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
