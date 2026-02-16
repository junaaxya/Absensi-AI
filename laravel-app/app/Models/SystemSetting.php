<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'office_name',
        'office_latitude',
        'office_longitude',
        'office_radius',
        'work_start_time',
        'work_end_time',
        'overtime_start_time',
        'overtime_end_time',
        'late_tolerance_minutes',
    ];

    protected $casts = [
        'office_latitude' => 'float',
        'office_longitude' => 'float',
        'office_radius' => 'float',
        'late_tolerance_minutes' => 'integer',
    ];
}
