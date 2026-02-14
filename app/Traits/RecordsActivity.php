<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Trait para rastrear atividades de modelos
 * Registra automaticamente CRUD e mudanças de campos sensíveis
 */
trait RecordsActivity
{
    use LogsActivity;

    /**
     * Configuração de logs do modelo
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getLoggableAttributes())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Atributos que devem ser rastreados
     * Prioriza campos sensíveis definidos no config
     */
    protected function getLoggableAttributes(): array
    {
        $sensitiveFields = config('activitylog.sensitive_fields', []);
        
        // Sempre rastrear campos sensíveis
        $attributes = $sensitiveFields;

        // Rastrear campos importantes por modelo
        $modelClass = class_basename($this);
        
        $modelSpecificAttrs = match($modelClass) {
            'Employee' => ['first_name', 'middle_name', 'last_name', 'is_active', 'designation_id', 'department_id'],
            'Contract' => ['salary', 'start_date', 'end_date', 'status', 'contract_type_id'],
            'Attendance' => ['work_date', 'start_time', 'end_time', 'hours_worked', 'approved'],
            'Timeoff' => ['start_date', 'end_date', 'type', 'status', 'reason'],
            'Benefit' => ['type', 'provider', 'active'],
            'Worklog' => ['date', 'duration', 'type', 'approved'],
            'Hourbank' => ['balance_hours'],
            'User' => ['name', 'email', 'role'],
            default => [],
        };

        return array_unique(array_merge($attributes, $modelSpecificAttrs));
    }
}
