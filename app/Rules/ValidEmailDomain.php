<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidEmailDomain implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Regex: email@domain.extension
        // Rejeita: teste@teste (sem extensão), user@, @domain.com
        // Aceita: teste@empresa.com, user@empresa.pt, admin@sub.domain.co.uk
        return preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $value) === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O email deve conter um domínio com extensão válida (ex: usuario@empresa.com).';
    }
}
