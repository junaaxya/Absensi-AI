<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $table = 'system_settings';

    protected $fillable = [
        'office_name',
        'office_latitude',
        'office_longitude',
        'office_radius',
        'office_gps_tolerance',
        'work_start_time',
        'work_end_time',
        'overtime_start_time',
        'overtime_end_time',
        'late_tolerance_minutes',
        'company_name', 'company_address', 'company_phone', 'company_email',
        'company_logo', 'company_website', 'company_npwp',
        'auto_checkout_time', 'minimum_work_hours', 'half_day_threshold_hours',
        'require_checkout', 'allow_multiple_checkin', 'weekend_days',
        'face_similarity_threshold', 'face_max_registration_photos',
        'face_anti_spoofing_enabled', 'face_min_photo_quality', 'face_require_liveness',
        'notify_late_checkin', 'notify_absence', 'notification_emails',
        'backup_retention_days', 'audit_log_retention_days',
    ];

    protected $casts = [
        'office_latitude' => 'float',
        'office_longitude' => 'float',
        'office_radius' => 'float',
        'office_gps_tolerance' => 'integer',
        'late_tolerance_minutes' => 'integer',
        'minimum_work_hours' => 'integer',
        'half_day_threshold_hours' => 'integer',
        'require_checkout' => 'boolean',
        'allow_multiple_checkin' => 'boolean',
        'weekend_days' => 'array',
        'face_similarity_threshold' => 'float',
        'face_max_registration_photos' => 'integer',
        'face_anti_spoofing_enabled' => 'boolean',
        'face_min_photo_quality' => 'integer',
        'face_require_liveness' => 'boolean',
        'notify_late_checkin' => 'boolean',
        'notify_absence' => 'boolean',
        'backup_retention_days' => 'integer',
        'audit_log_retention_days' => 'integer',
    ];
}
