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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payrollDetail()
    {
        return $this->belongsTo(PayrollDetail::class);
    }
}
