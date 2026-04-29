<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'period_month',
        'start_date',
        'end_date',
        'status',
        'total_employees',
        'total_gross',
        'total_deductions',
        'total_net',
        'calculated_at',
        'approved_by',
        'approved_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_employees' => 'integer',
        'total_gross' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net' => 'decimal:2',
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function payrollDetails()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
