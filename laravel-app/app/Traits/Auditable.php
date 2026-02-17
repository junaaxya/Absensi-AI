<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::logAudit('created', $model);
        });

        static::updated(function ($model) {
            static::logAudit('updated', $model);
        });

        static::deleted(function ($model) {
            static::logAudit('deleted', $model);
        });
    }

    protected static function logAudit(string $action, $model): void
    {
        $oldValues = null;
        $newValues = null;

        if ($action === 'updated') {
            $changed = $model->getChanges();
            unset($changed['updated_at']);

            if (empty($changed)) {
                return;
            }

            $oldValues = array_intersect_key($model->getOriginal(), $changed);
            $newValues = $changed;
        } elseif ($action === 'created') {
            $newValues = $model->getAttributes();
            unset($newValues['updated_at'], $newValues['created_at']);
        } elseif ($action === 'deleted') {
            $oldValues = $model->getOriginal();
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
