<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class AdminSystemSettingController extends Controller
{
    public function index()
    {
        // Fetch or create default settings
        $settings = SystemSetting::firstOrCreate(
            ['id' => 1],
            [
                'office_name' => 'Head Office',
                'office_latitude' => -6.2088,
                'office_longitude' => 106.8456,
                'office_radius' => 0.1, // 100 meters (0.1 km)
                'work_start_time' => '08:00',
                'work_end_time' => '17:00',
                'overtime_start_time' => '17:30',
                'overtime_end_time' => '21:00',
                'late_tolerance_minutes' => 15
            ]
        );

        return view('admin.settings.index', compact('settings'));
    }

    public function updateWorkHours(Request $request)
    {
        $request->validate([
            'work_start_time' => 'required',
            'work_end_time' => 'required',
            'overtime_start_time' => 'required',
            'overtime_end_time' => 'required',
            'late_tolerance_minutes' => 'required|integer|min:0',
        ]);

        $settings = SystemSetting::first();
        $settings->update($request->only([
            'work_start_time',
            'work_end_time',
            'overtime_start_time',
            'overtime_end_time',
            'late_tolerance_minutes'
        ]));

        return back()->with('success', 'Pengaturan jam kerja berhasil disimpan.');
    }

    public function resetWorkHours()
    {
        $settings = SystemSetting::first();
        $settings->update([
            'work_start_time' => '08:00:00',
            'work_end_time' => '17:00:00',
            'overtime_start_time' => '17:30:00',
            'overtime_end_time' => '21:00:00',
            'late_tolerance_minutes' => 15
        ]);

        return back()->with('success', 'Pengaturan jam kerja dikembalikan ke default.');
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'office_latitude' => 'required|numeric|between:-90,90',
            'office_longitude' => 'required|numeric|between:-180,180',
            'office_radius' => 'required|numeric|min:0.01', // Minimum 10 meters (0.01 km)
        ]);

        $settings = SystemSetting::first();
        $settings->update([
            'office_latitude' => $request->office_latitude,
            'office_longitude' => $request->office_longitude,
            'office_radius' => $request->office_radius,
        ]);

        return back()->with('success', 'Lokasi presensi berhasil diperbarui.');
    }

    public function resetLocation()
    {
        $settings = SystemSetting::first();
        $settings->update([
            'office_latitude' => -6.2088,
            'office_longitude' => 106.8456,
            'office_radius' => 0.1
        ]);

        return back()->with('success', 'Lokasi presensi dikembalikan ke default.');
    }
}
