<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($employee) {
            if (empty($employee->user_id)) {
                $defaultPassword = 'changeme123';
                $user = \App\Models\User::create([
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'email' => $employee->email,
                    'password' => bcrypt($defaultPassword),
                    'must_change_password' => true,
                ]);
                $employee->user_id = $user->id;
                $employee->save();
            }

            // Cria um banco de horas associado caso não exista
            if (!$employee->hoursbank) {
                $employee->hoursbank()->create([
                    'total_hours' => 0,
                ]);
            }

            // Evita criação de contrato se não houver data de contratação
            if (!$employee->date_hired) {
                return;
            }

            // Salário base da designação ou zero
            $baseSalary = $employee->designation?->base_salary ?? 0;

            // Get the full_time contract type
            $contractType = \App\Models\ContractType::where('name', 'Tempo completo')->first();

            // Cria contrato
            $employee->contracts()->create([
                'designation_id' => $employee->designation_id,
                'contract_type_id' => $contractType?->id,
                'salary'         => $baseSalary,
                'start_date'     => $employee->date_hired,
                'date_hired'     => $employee->date_hired,
                'status'         => 'active',
            ]);
        });
    }

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

    public function hoursbank()
    {
        return $this->hasOne(Hoursbank::class);
    }

    public function worklogs()
    {
        return $this->hasMany(Worklog::class);
    }

    public function timeoffs()
    {
        return $this->hasMany(Timeoff::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
