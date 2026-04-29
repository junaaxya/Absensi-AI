<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitAttendance extends Model
{
    use HasFactory, HasAuditLog;

    protected $table = 'visit_attendances';

    protected $fillable = [
        'user_id',
        'tanggal',
        'client_name',
        'location_name',
        'purpose',
        'check_in_time',
        'check_out_time',
        'check_in_lat',
        'check_in_long',
        'check_out_lat',
        'check_out_long',
        'check_in_photo',
        'check_out_photo',
        'similarity_score_in',
        'similarity_score_out',
        'notes',
        'status',
        'device_fingerprint',
        'anomaly_score',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'anomaly_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visitLocations()
    {
        return $this->hasMany(VisitLocation::class);
    }
}
