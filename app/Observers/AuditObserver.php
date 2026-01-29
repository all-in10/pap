<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created(Model $model)
    {
        $this->log('created', $model);
    }

    public function updated(Model $model)
    {
        $this->log('updated', $model);
    }

    public function deleted(Model $model)
    {
        $this->log('deleted', $model);
    }

    protected function log(string $action, Model $model)
    {
        try {
            $userId = Auth::id() ?? null;

            AuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'changes' => $this->formatChanges($model, $action),
            ]);
        } catch (\Throwable $e) {
            // swallow to avoid breaking app flows
        }
    }

    protected function formatChanges(Model $model, string $action)
    {
        if ($action === 'deleted') {
            return $model->getAttributes();
        }

        $changes = $model->getChanges();
        $original = $model->getOriginal();

        $diff = [];
        foreach ($changes as $key => $value) {
            $diff[$key] = [
                'old' => Arr::get($original, $key),
                'new' => $value,
            ];
        }

        return $diff;
    }
}
