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
        // Automação: cria contrato ao criar Employee
        static::created(function ($employee) {
            $baseSalary = $employee->designation ? $employee->designation->base_salary : 0;
            $employee->contracts()->create([
                'contract_type' => 'full_time',          // padrão
                'salary' => 0,                           // valor padrão ou recebido via form
                'start_date' => $employee->date_hired,   // pega do Employee
                'date_hired' => $employee->date_hired,   // mantém no contrato
                'status' => 'active',
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
