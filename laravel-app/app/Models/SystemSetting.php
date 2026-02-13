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
    ];

    protected $casts = [
        'office_latitude' => 'float',
        'office_longitude' => 'float',
        'office_radius' => 'float',
    ];
}
