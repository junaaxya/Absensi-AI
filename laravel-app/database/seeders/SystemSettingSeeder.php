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
                'office_radius' => 0.5, // 0.5 km radius
            ]
        );
    }
}
