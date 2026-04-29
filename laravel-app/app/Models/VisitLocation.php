<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLocation extends Model
{
    use HasFactory, HasAuditLog;

    protected $table = 'visit_locations';

    protected $fillable = [
        'visit_attendance_id',
        'latitude',
        'longitude',
        'accuracy',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'float',
    ];

    public function visitAttendance()
    {
        return $this->belongsTo(VisitAttendance::class);
    }
}
