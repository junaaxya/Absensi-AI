<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSnapshot extends Model
{
    use HasFactory, HasAuditLog;

    public $timestamps = false;

    protected $fillable = [
        'payroll_period_id',
        'snapshot_type',
        'snapshot_data',
        'snapshot_hash',
        'created_at',
    ];

    protected $casts = [
        'snapshot_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }
}
