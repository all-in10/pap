<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Designation extends Model
{
    use HasFactory;
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
