<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * Método executado quando o modelo é inicializado
     * Define eventos para criação do funcionário
     */
    protected static function booted()
    {
        static::created(function ($employee) {
            // Cria usuário associado se não existir
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

            // Obtém o tipo de contrato "Tempo completo"
            $contractType = \App\Models\ContractType::where('name', 'Tempo completo')->first();

            // Cria contrato inicial
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

    // RELACIONAMENTOS - Define as relações com outras entidades
    /**
     * Relacionamento muitos-para-um com Country
     * Um funcionário pertence a um país
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Relacionamento muitos-para-um com State
     * Um funcionário pertence a um estado
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Relacionamento muitos-para-um com City
     * Um funcionário pertence a uma cidade
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relacionamento muitos-para-um com Department
     * Um funcionário pertence a um departamento
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relacionamento muitos-para-um com Designation
     * Um funcionário tem uma designação (cargo)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Relacionamento um-para-muitos com Contract
     * Um funcionário pode ter vários contratos
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Relacionamento um-para-um com Hoursbank
     * Um funcionário tem um banco de horas
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hoursbank()
    {
        return $this->hasOne(Hoursbank::class);
    }

    /**
     * Relacionamento um-para-muitos com Worklog
     * Um funcionário pode ter vários registos de trabalho
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function worklogs()
    {
        return $this->hasMany(Worklog::class);
    }

    /**
     * Relacionamento um-para-muitos com Timeoff
     * Um funcionário pode ter vários pedidos de férias
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeoffs()
    {
        return $this->hasMany(Timeoff::class);
    }

    /**
     * Relacionamento muitos-para-um com User
     * Um funcionário está associado a um usuário
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
