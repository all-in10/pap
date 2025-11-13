<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'email',
        'nss',
        'nif',
        'phone_number',
        'observations',
        'address',
        'zip_code',
        'date_of_birth',
        'date_hired',
        'is_active',
        'country_id',
        'state_id',
        'city_id',
        'department_id',
        'designation_id',
    ];

    protected static function booted()
    {
        static::created(function ($employee) {
            // Evita criação de contrato se não houver data de contratação
            if (!$employee->date_hired) {
                return;
            }

            // Salário base da designação ou zero
            $baseSalary = $employee->designation?->base_salary ?? 0;

            // Cria contrato
            $employee->contracts()->create([
                'designation_id' => $employee->designation_id,
                'contract_type'  => 'full_time',
                'salary'         => $baseSalary,
                'start_date'     => $employee->date_hired,
                'date_hired'     => $employee->date_hired,
                'status'         => 'active',
            ]);
        });
    }

    // RELACIONAMENTOS
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
