<?php

namespace App\Exceptions;

use Exception;

class TimeoffOverlapException extends Exception
{
    public function __construct(
        $startDate,
        $endDate,
        $message = "Não é possível criar timeoff neste período. Existe uma solicitação de ausência aprovada sobreposta."
    ) {
        parent::__construct($message);
        
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Render the exception
     */
    public function render()
    {
        return response()->json([
            'message' => $this->message,
            'error_type' => 'timeoff_overlap',
            'details' => [
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ]
        ], 422);
    }
}
