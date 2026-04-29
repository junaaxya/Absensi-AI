<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use App\Traits\PayrollFreezeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, HasAuditLog, PayrollFreezeTrait;

    protected $table = 'attendances';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'kegiatan',
        'status',
        'lat_in',
        'long_in',
        'lat_out',
        'long_out',
        'similarity_score_in',
        'similarity_score_out',
        'device_info',
        'device_fingerprint',
        'gps_readings',
        'ip_address',
        'timezone_client',
        'anomaly_score',
        'anomaly_flags',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'jam_masuk'     => 'datetime:H:i:s',
        'jam_keluar'    => 'datetime:H:i:s',
        'gps_readings'  => 'array',
        'anomaly_flags' => 'array',
        'anomaly_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function violations()
    {
        return $this->morphMany(Violation::class, 'reference');
    }
}
