<?php

namespace App\Traits;

use App\Models\AuditLog;

trait HasAuditLog
{
    protected static bool $auditingDisabled = false;

    public static function withoutAuditLog(callable $callback): mixed
    {
        static::$auditingDisabled = true;

        try {
            return $callback();
        } finally {
            static::$auditingDisabled = false;
        }
    }

    public static function bootHasAuditLog(): void
    {
        static::created(function ($model) {
            if (static::$auditingDisabled) {
                return;
            }

            $model->recordAuditLog('created', null, $model->getAuditableAttributes());
        });

        static::updated(function ($model) {
            if (static::$auditingDisabled) {
                return;
            }

            $changed = $model->getChanges();
            unset($changed['updated_at']);

            if (empty($changed)) {
                return;
            }

            $oldValues = [];
            foreach (array_keys($changed) as $key) {
                $oldValues[$key] = $model->getOriginal($key);
            }

            $model->recordAuditLog(
                'updated',
                $model->filterSensitiveFields($oldValues),
                $model->filterSensitiveFields($changed),
            );
        });

        static::deleted(function ($model) {
            if (static::$auditingDisabled) {
                return;
            }

            $model->recordAuditLog('deleted', $model->getAuditableAttributes(), null);
        });
    }

    protected function recordAuditLog(string $event, ?array $oldValues, ?array $newValues): void
    {
        AuditLog::create([
            'user_id' => $this->resolveAuditUserId(),
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $this->resolveAuditIp(),
            'user_agent' => $this->resolveAuditUserAgent(),
            'url' => $this->resolveAuditUrl(),
            'created_at' => now(),
        ]);
    }

    protected function getAuditableAttributes(): array
    {
        return $this->filterSensitiveFields($this->getAttributes());
    }

    protected function filterSensitiveFields(array $values): array
    {
        $sensitive = ['password', 'remember_token', 'no_rekening', 'npwp'];

        return array_diff_key($values, array_flip($sensitive));
    }

    protected function resolveAuditUserId(): ?int
    {
        try {
            return auth()->id();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function resolveAuditIp(): ?string
    {
        try {
            return request()->ip();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function resolveAuditUserAgent(): ?string
    {
        try {
            return request()->userAgent();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function resolveAuditUrl(): ?string
    {
        try {
            return request()->fullUrl();
        } catch (\Throwable) {
            return null;
        }
    }
}
