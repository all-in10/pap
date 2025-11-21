<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hoursbank extends Model
{
    protected $fillable = [
        'employee_id',
        'total_hours',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function worklogs()
    {
        return $this->hasMany(Worklog::class, 'employee_id', 'employee_id');
    }
}
