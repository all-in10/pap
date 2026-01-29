<?php

namespace App\Exceptions;

use Exception;

class InsufficientPermissionException extends Exception
{
    public function __construct(
        $action = null,
        $resource = null,
        $message = null
    ) {
        if (!$message) {
            $message = "Você não tem permissão para executar esta ação";
            if ($action && $resource) {
                $message = "Você não tem permissão para {$action} {$resource}";
            }
        }

        parent::__construct($message);
        
        $this->action = $action;
        $this->resource = $resource;
    }

    /**
     * Render the exception
     */
    public function render()
    {
        return response()->json([
            'message' => $this->message,
            'error_type' => 'insufficient_permission',
            'action' => $this->action,
            'resource' => $this->resource
        ], 403);
    }
}
