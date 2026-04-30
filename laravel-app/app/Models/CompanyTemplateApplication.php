<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTemplateApplication extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'company_id',
        'payroll_template_id',
        'applied_by',
        'applied_at',
        'mode',
        'components_created',
        'components_skipped',
        'components_updated',
        'notes',
        'rollback_snapshot',
    ];

    protected $casts = [
        'rollback_snapshot' => 'array',
        'applied_at' => 'datetime',
        'components_created' => 'integer',
        'components_skipped' => 'integer',
        'components_updated' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function template()
    {
        return $this->belongsTo(PayrollTemplate::class, 'payroll_template_id');
    }

    public function appliedBy()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }
}
