<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    /**
     * Update system settings (office location).
     */
    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
        ]);

        $settings = SystemSetting::firstOrCreate(
            ['id' => 1],
            [
                'office_name' => 'Head Office',
                'office_latitude' => $request->latitude,
                'office_longitude' => $request->longitude,
                'office_radius' => $request->radius,
            ]
        );

        if ($settings->wasRecentlyCreated === false) {
            $settings->update([
                'office_latitude' => $request->latitude,
                'office_longitude' => $request->longitude,
                'office_radius' => $request->radius,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $settings->fresh(),
        ]);
    }
}
