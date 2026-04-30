<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryComponent extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'user_id',
        'salary_component_id',
        'amount',
        'effective_date',
        'value_type',
        'formula',
        'effective_until',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_date' => 'date',
        'effective_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaryComponent()
    {
        return $this->belongsTo(SalaryComponent::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeEffectiveOn(Builder $query, $date): Builder
    {
        return $query->where(function ($q) use ($date) {
            $q->where('effective_date', '<=', $date)
                ->where(function ($inner) use ($date) {
                    $inner->whereNull('effective_until')
                        ->orWhere('effective_until', '>=', $date);
                });
        });
    }
}
