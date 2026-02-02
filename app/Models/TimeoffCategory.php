<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeoffCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', // ex: parentalidade
        'label', // ex: 👶 Parentalidade
    ];

    public function timeoffs()
    {
        return $this->hasMany(Timeoff::class, 'category_id');
    }
}
