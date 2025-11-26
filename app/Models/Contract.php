<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

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
}
