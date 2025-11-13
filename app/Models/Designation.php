<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'level',
        'base_salary',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
