<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForModel(Builder $query, string $type, int|string $id): Builder
    {
        return $query->where('auditable_type', $type)->where('auditable_id', $id);
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /** @return array<string, array{old: mixed, new: mixed}>|null */
    public function getChangesAttribute(): ?array
    {
        return match ($this->event) {
            'updated' => $this->buildUpdatedChanges(),
            'created' => $this->buildCreatedChanges(),
            'deleted' => $this->buildDeletedChanges(),
            default => null,
        };
    }

    private function buildUpdatedChanges(): ?array
    {
        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];
        $keys = array_unique(array_merge(array_keys($old), array_keys($new)));

        if (empty($keys)) {
            return null;
        }

        $changes = [];
        foreach ($keys as $key) {
            $changes[$key] = [
                'old' => $old[$key] ?? null,
                'new' => $new[$key] ?? null,
            ];
        }

        return $changes;
    }

    private function buildCreatedChanges(): ?array
    {
        $new = $this->new_values ?? [];

        if (empty($new)) {
            return null;
        }

        $changes = [];
        foreach ($new as $key => $value) {
            $changes[$key] = [
                'old' => null,
                'new' => $value,
            ];
        }

        return $changes;
    }

    private function buildDeletedChanges(): ?array
    {
        $old = $this->old_values ?? [];

        if (empty($old)) {
            return null;
        }

        $changes = [];
        foreach ($old as $key => $value) {
            $changes[$key] = [
                'old' => $value,
                'new' => null,
            ];
        }

        return $changes;
    }
}
