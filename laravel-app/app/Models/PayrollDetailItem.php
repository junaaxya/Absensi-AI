<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDetailItem extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'payroll_detail_id',
        'component_name',
        'component_type',
        'amount',
        'notes',
        'salary_component_id',
        'component_code',
        'component_category',
        'value_type',
        'formula_used',
        'formula_variables',
        'execution_order',
        'is_prorated',
        'prorate_factor',
        'pre_prorate_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'pre_prorate_amount' => 'decimal:2',
        'prorate_factor' => 'decimal:6',
        'execution_order' => 'integer',
        'is_prorated' => 'boolean',
        'formula_variables' => 'array',
    ];

    public function payrollDetail()
    {
        return $this->belongsTo(PayrollDetail::class);
    }
}
