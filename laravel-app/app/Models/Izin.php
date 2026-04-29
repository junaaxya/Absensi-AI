<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use App\Traits\PayrollFreezeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory, HasAuditLog, PayrollFreezeTrait;

    protected $fillable = [
        'user_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'dokumen',
        'status',
        'leave_type_id',
        'approval_status',
        'current_approver_id',
        'approved_by',
        'wfa_location',
        'wfa_daily_checkins',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_by' => 'array',
        'wfa_daily_checkins' => 'array',
        'approval_status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function currentApprover()
    {
        return $this->belongsTo(User::class, 'current_approver_id');
    }
}
