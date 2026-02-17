<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::firstOrCreate(
            ['id' => 1],
            [
                'office_name' => 'Head Office',
                'office_latitude' => -6.175392,
                'office_longitude' => 106.827153,
                'office_radius' => 0.5,
                'work_start_time' => '08:00',
                'work_end_time' => '17:00',
                'overtime_start_time' => '17:30',
                'overtime_end_time' => '21:00',
                'late_tolerance_minutes' => 15,
                'company_name' => null,
                'auto_checkout_time' => '23:59',
                'minimum_work_hours' => 8,
                'half_day_threshold_hours' => 4,
                'require_checkout' => true,
                'allow_multiple_checkin' => false,
                'weekend_days' => ['Saturday', 'Sunday'],
                'face_similarity_threshold' => 0.5,
                'face_max_registration_photos' => 6,
                'face_anti_spoofing_enabled' => false,
                'face_min_photo_quality' => 80,
                'face_require_liveness' => false,
            ]
        );
    }
}
