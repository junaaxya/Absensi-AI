<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'payroll_period_id',
        'user_id',
        'working_days',
        'present_days',
        'late_count',
        'late_minutes_total',
        'overtime_hours',
        'gaji_pokok',
        'total_earnings',
        'total_deductions',
        'total_bpjs_company',
        'total_bpjs_employee',
        'pph21_amount',
        'net_salary',
        'prorate_factor',
        'prorate_reason',
        'status',
    ];

    protected $casts = [
        'working_days' => 'integer',
        'present_days' => 'integer',
        'late_count' => 'integer',
        'late_minutes_total' => 'integer',
        'overtime_hours' => 'decimal:2',
        'gaji_pokok' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_bpjs_company' => 'decimal:2',
        'total_bpjs_employee' => 'decimal:2',
        'pph21_amount' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'prorate_factor' => 'decimal:4',
    ];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PayrollDetailItem::class);
    }
}
