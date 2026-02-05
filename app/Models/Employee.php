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

    protected $casts = [
        'date_of_birth' => 'date',
        'date_hired' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        // Automação: cria User, Hourbank e Contrato ao criar Employee
        static::created(function ($employee) {
            // Cria contrato
            $baseSalary = $employee->designation ? $employee->designation->base_salary : 0;
            $employee->contracts()->create([
                'contract_type_id' => \App\Models\ContractType::firstWhere('name', 'sem_termo')->id ?? null, // padrão
                'salary' => $baseSalary,
                'start_date' => $employee->date_hired,
                'date_hired' => $employee->date_hired,
                'status' => 'active',
            ]);

            // Cria hourbank
            $employee->hourbanks()->create([
                'balance_hours' => 0,
                'last_accrual_date' => $employee->date_hired ?? now(),
            ]);

            // Cria user
            if ($employee->email) {
                \App\Models\User::create([
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'email' => $employee->email,
                    'password' => bcrypt('12345678'), // senha padrão, altere conforme necessário
                    'employee_id' => $employee->id,
                ]);
            }
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

    // NOVOS RELACIONAMENTOS
    public function worklogs()
    {
        return $this->hasMany(Worklog::class);
    }

    public function hourbanks()
    {
        return $this->hasMany(Hourbank::class);
    }

    public function timeoffs()
    {
        return $this->hasMany(Timeoff::class);
    }

    public function benefits()
    {
        return $this->hasMany(Benefit::class);
    }

    // RELAÇÃO COM USUÁRIO
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
