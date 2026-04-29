<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory, HasAuditLog;

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
        'max_devices_per_user', 'anomaly_score_warning_threshold', 'anomaly_score_reject_threshold', 'enable_anti_cheat',
        'notify_late_checkin', 'notify_absence', 'notification_emails',
        'backup_retention_days', 'audit_log_retention_days',
        'violation_deduction_type', 'violation_deduction_per_point',
        'violation_deduction_percentage',
        'sp1_threshold', 'sp2_threshold', 'sp3_threshold',
        'bpjs_kes_ceiling', 'bpjs_jp_ceiling', 'jkk_risk_group', 'no_npwp_surcharge_enabled',
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
        'max_devices_per_user' => 'integer',
        'anomaly_score_warning_threshold' => 'integer',
        'anomaly_score_reject_threshold' => 'integer',
        'enable_anti_cheat' => 'boolean',
        'notify_late_checkin' => 'boolean',
        'notify_absence' => 'boolean',
        'backup_retention_days' => 'integer',
        'audit_log_retention_days' => 'integer',
        'violation_deduction_per_point' => 'float',
        'violation_deduction_percentage' => 'float',
        'sp1_threshold' => 'integer',
        'sp2_threshold' => 'integer',
        'sp3_threshold' => 'integer',
        'bpjs_kes_ceiling' => 'float',
        'bpjs_jp_ceiling' => 'float',
        'jkk_risk_group' => 'integer',
        'no_npwp_surcharge_enabled' => 'boolean',
    ];
}
