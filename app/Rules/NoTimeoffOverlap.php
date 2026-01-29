<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Timeoff;

class NoTimeoffOverlap implements DataAwareRule, ValidationRule
{
    /**
     * Todos os dados sendo validados
     */
    protected array $data = [];

    /**
     * ID do timeoff sendo atualizado (null se criando)
     */
    private ?int $timeoffId = null;

    /**
     * Construtor
     */
    public function __construct(?int $timeoffId = null)
    {
        $this->timeoffId = $timeoffId;
    }

    /**
     * Set the data under validation.
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Valida que não há sobreposição de timeoffs
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Extrai informações do contexto
        $employeeId = $this->data['employee_id'] ?? null;
        $startDate = $this->data['start_date'] ?? null;
        $endDate = $this->data['end_date'] ?? null;

        if (!$employeeId || !$startDate || !$endDate) {
            return;
        }

        // Query para verificar overlaps
        $query = Timeoff::where('employee_id', $employeeId)
            ->where('status', 'approved') // apenas timeoffs aprovados contam
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });

        // Se atualizando, exclua o timeoff atual
        if ($this->timeoffId) {
            $query->where('id', '!=', $this->timeoffId);
        }

        if ($query->exists()) {
            $fail('validation.timeoff_overlap')
                ->translate(['attribute' => 'período de férias']);
        }
    }
}
