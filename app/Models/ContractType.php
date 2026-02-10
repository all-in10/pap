<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'label', 'description'];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Verifica se este tipo de contrato requer data de fim
     */
    public function requiresEndDate(): bool
    {
        return in_array($this->name, ['temporario', 'tempo_parcial', 'estagio', 'termo_certo', 'termo_incerto']);
    }

    /**
     * Obtém IDs dos tipos que requerem data de fim
     */
    public static function requiresEndDateIds(): array
    {
        return static::whereIn('name', ['temporario', 'tempo_parcial', 'estagio', 'termo_certo', 'termo_incerto'])
            ->pluck('id')
            ->toArray();
    }
}
