<?php

namespace App\Exceptions;

use Exception;

class InvalidContractException extends Exception
{
    public function __construct(
        $message = "Dados do contrato são inválidos ou incompletos.",
        $errors = []
    ) {
        parent::__construct($message);
        
        $this->errors = $errors;
    }

    /**
     * Render the exception
     */
    public function render()
    {
        return response()->json([
            'message' => $this->message,
            'error_type' => 'invalid_contract',
            'errors' => $this->errors
        ], 422);
    }
}
