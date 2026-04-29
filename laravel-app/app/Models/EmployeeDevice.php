<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDevice extends Model
{
    use HasFactory, HasAuditLog;

    protected $table = 'employee_devices';

    protected $fillable = [
        'user_id',
        'device_fingerprint',
        'device_name',
        'platform',
        'browser',
        'screen_resolution',
        'is_trusted',
        'last_used_at',
    ];

    protected $casts = [
        'is_trusted' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
