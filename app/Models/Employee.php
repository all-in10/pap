<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RecordsActivity;
use App\Observers\EmployeeObserver;

class Employee extends Model
{
    use HasFactory, RecordsActivity, SoftDeletes;

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
        // Registar o Observer para gerir criação de User, Contract e Hourbank
        static::observe(EmployeeObserver::class);
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
