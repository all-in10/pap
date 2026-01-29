<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class ValidateContractDates implements DataAwareRule, ValidationRule
{
    /**
     * Todos os dados sendo validados
     */
    protected array $data = [];

    /**
     * Set the data under validation.
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Valida datas de contrato
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Se validando end_date
        if ($attribute === 'end_date') {
            $startDate = $this->data['start_date'] ?? null;
            $endDate = $value;

            if (!$startDate || !$endDate) {
                return;
            }

            try {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);

                // end_date deve ser >= start_date
                if ($end->isBefore($start)) {
                    $fail('validation.contract_end_before_start')
                        ->translate();
                }

                // Aviso: contrato muito longo (mais de 5 anos)
                if ($end->diffInYears($start) > 5) {
                    $fail('validation.contract_too_long')
                        ->translate(['years' => 5]);
                }
            } catch (\Exception $e) {
                $fail('validation.invalid_date_format')
                    ->translate(['attribute' => $attribute]);
            }
        }

        // Se validando start_date
        if ($attribute === 'start_date') {
            $startDate = $value;

            try {
                $start = Carbon::parse($startDate);

                // start_date não deve ser muito no futuro (mais de 6 meses)
                if ($start->isFuture() && $start->diffInMonths(now()) > 6) {
                    $fail('validation.contract_start_too_far')
                        ->translate(['months' => 6]);
                }
            } catch (\Exception $e) {
                $fail('validation.invalid_date_format')
                    ->translate(['attribute' => $attribute]);
            }
        }
    }
}
