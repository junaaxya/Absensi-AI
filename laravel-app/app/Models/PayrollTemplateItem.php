<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollTemplateItem extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'payroll_template_id',
        'name',
        'code',
        'type',
        'category',
        'value_type',
        'formula',
        'default_amount',
        'is_taxable',
        'is_fixed',
        'is_prorated',
        'prorate_basis',
        'execution_order',
        'depends_on',
        'min_value',
        'max_value',
        'description',
        'help_text',
        'is_required',
        'is_optional',
        'group_label',
        'sort_within_group',
    ];

    protected $casts = [
        'depends_on' => 'array',
        'is_taxable' => 'boolean',
        'is_fixed' => 'boolean',
        'is_prorated' => 'boolean',
        'is_required' => 'boolean',
        'is_optional' => 'boolean',
        'default_amount' => 'decimal:2',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'execution_order' => 'integer',
        'sort_within_group' => 'integer',
    ];

    public function template()
    {
        return $this->belongsTo(PayrollTemplate::class, 'payroll_template_id');
    }
}
