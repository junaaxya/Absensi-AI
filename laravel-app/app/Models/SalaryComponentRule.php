<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponentRule extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'company_id',
        'salary_component_id',
        'override_formula',
        'override_amount',
        'min_value',
        'max_value',
        'execution_order',
        'is_active',
        'effective_from',
        'effective_until',
    ];

    protected $casts = [
        'override_amount' => 'decimal:2',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'execution_order' => 'integer',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function salaryComponent()
    {
        return $this->belongsTo(SalaryComponent::class);
    }
}
