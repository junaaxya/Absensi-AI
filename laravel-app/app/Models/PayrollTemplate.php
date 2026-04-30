<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollTemplate extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'industry_type',
        'company_size',
        'includes_bpjs',
        'includes_pph21',
        'includes_overtime',
        'component_count',
        'popularity_score',
        'is_active',
        'is_system',
        'icon',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'includes_bpjs' => 'boolean',
        'includes_pph21' => 'boolean',
        'includes_overtime' => 'boolean',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'component_count' => 'integer',
        'popularity_score' => 'integer',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(PayrollTemplateItem::class);
    }

    public function applications()
    {
        return $this->hasMany(CompanyTemplateApplication::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForIndustry(Builder $query, string $industryType): Builder
    {
        return $query->where('industry_type', $industryType);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
