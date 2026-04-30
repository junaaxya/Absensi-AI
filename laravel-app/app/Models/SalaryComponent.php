<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'name',
        'code',
        'type',
        'category',
        'is_taxable',
        'is_fixed',
        'default_amount',
        'description',
        'help_text',
        'group_label',
        'is_active',
        'company_id',
        'value_type',
        'formula',
        'is_prorated',
        'prorate_basis',
        'execution_order',
        'depends_on',
        'min_value',
        'max_value',
        'effective_from',
        'effective_until',
        'version',
        'parent_id',
        'source_template_id',
        'source_template_item_id',
    ];

    protected $casts = [
        'is_taxable' => 'boolean',
        'is_fixed' => 'boolean',
        'is_active' => 'boolean',
        'is_prorated' => 'boolean',
        'default_amount' => 'decimal:2',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'execution_order' => 'integer',
        'version' => 'integer',
        'depends_on' => 'array',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function parent()
    {
        return $this->belongsTo(SalaryComponent::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SalaryComponent::class, 'parent_id');
    }

    public function rules()
    {
        return $this->hasMany(SalaryComponentRule::class);
    }

    public function employeeSalaryComponents()
    {
        return $this->hasMany(EmployeeSalaryComponent::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany(Builder $query, ?int $companyId): Builder
    {
        return $query->where(function ($q) use ($companyId) {
            $q->whereNull('company_id');
            if ($companyId) {
                $q->orWhere('company_id', $companyId);
            }
        });
    }

    public function scopeEffectiveOn(Builder $query, $date): Builder
    {
        return $query->where(function ($q) use ($date) {
            $q->where(function ($inner) use ($date) {
                $inner->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', $date);
            })->where(function ($inner) use ($date) {
                $inner->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $date);
            });
        });
    }
}
