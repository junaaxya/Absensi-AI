<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesSettingsTabs;
use App\Models\SystemSetting;
use App\Models\Department;
use App\Models\User;
use App\Models\WorkShift;
use App\Models\Holiday;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\BackupService;
use GuzzleHttp\Promise\PromiseInterface;

class AdminSystemSettingController extends Controller
{
    use AuthorizesSettingsTabs;

    private function fetchFaceServiceHealth(): Response
    {
        $response = Http::timeout(5)->get(
            config('services.flask.url', env('FLASK_SERVICE_URL', 'http://face-service:5000')) . '/health'
        );

        if ($response instanceof PromiseInterface) {
            $response = $response->wait();
        }

        if (! $response instanceof Response) {
            throw new \RuntimeException('Unexpected face service response type');
        }

        return $response;
    }

    public function index(Request $request, ?string $category = null)
    {
        $settings = SystemSetting::firstOrCreate(
            ['id' => 1],
            [
                'office_name' => 'Head Office',
                'office_latitude' => -6.2088,
                'office_longitude' => 106.8456,
                'office_radius' => 0.1,
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

        $categoryTabs = $this->categoryTabs();

        $activeCategory = $category ?? 'umum';
        $userRole = (string) ($request->user()?->role ?? '');
        $roleAllowedTabs = $this->roleAllowedTabs($userRole);
        $categoryAllowedTabs = $categoryTabs[$activeCategory] ?? $categoryTabs['umum'];
        $allowedTabs = array_values(array_intersect($categoryAllowedTabs, $roleAllowedTabs));

        if (empty($allowedTabs)) {
            abort(403);
        }

        $defaultTab = $allowedTabs[0];

        $needsDepartments = in_array('departemen', $allowedTabs, true) || in_array('export', $allowedTabs, true);
        $needsUsers = in_array('departemen', $allowedTabs, true);
        $needsShifts = in_array('shift_kerja', $allowedTabs, true);
        $needsHolidays = in_array('hari_libur', $allowedTabs, true);
        $needsLeaveTypes = in_array('tipe_cuti', $allowedTabs, true);
        $needsBackups = in_array('backup', $allowedTabs, true);

        $departments = $needsDepartments
            ? Department::with(['head', 'parent'])->withCount('employees')->orderBy('sort_order')->get()
            : collect();

        $users = $needsUsers
            ? User::select('id', 'name', 'jabatan')->orderBy('name')->get()
            : collect();

        $shifts = $needsShifts ? WorkShift::all() : collect();
        $holidays = $needsHolidays ? Holiday::orderBy('date')->get() : collect();
        $leaveTypes = $needsLeaveTypes ? LeaveType::all() : collect();

        $backups = [];
        if ($needsBackups) {
            $backupService = app(BackupService::class);
            $backups = $backupService->listBackups();
        }

        return view('admin.settings.index', compact(
            'settings',
            'departments',
            'users',
            'shifts',
            'holidays',
            'leaveTypes',
            'backups',
            'activeCategory',
            'allowedTabs',
            'defaultTab'
        ));
    }

    public function updateWorkHours(Request $request)
    {
        $this->authorizeTabAccess($request, 'jam_kerja');

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

    public function resetWorkHours(Request $request)
    {
        $this->authorizeTabAccess($request, 'jam_kerja');

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
        $this->authorizeTabAccess($request, 'lokasi');

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

    public function resetLocation(Request $request)
    {
        $this->authorizeTabAccess($request, 'lokasi');

        $settings = SystemSetting::first();
        $settings->update([
            'office_latitude' => -6.2088,
            'office_longitude' => 106.8456,
            'office_radius' => 0.1
        ]);

        return back()->with('success', 'Lokasi presensi dikembalikan ke default.');
    }

    public function updateCompanyProfile(Request $request)
    {
        $this->authorizeTabAccess($request, 'profil_perusahaan');

        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:30',
            'company_email' => 'nullable|email|max:255',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'company_website' => 'nullable|url|max:255',
            'company_npwp' => 'nullable|string|max:30',
        ]);

        $settings = SystemSetting::first();

        if ($request->hasFile('company_logo')) {
            if ($settings->company_logo) {
                Storage::disk('public')->delete($settings->company_logo);
            }
            $logoPath = $request->file('company_logo')->store('logos', 'public');
            $settings->company_logo = $logoPath;
        }

        $settings->update($request->only([
            'company_name', 'company_address', 'company_phone',
            'company_email', 'company_website', 'company_npwp',
        ]));

        if ($request->hasFile('company_logo')) {
            $settings->save();
        }

        return back()->with('success', 'Profil perusahaan berhasil disimpan.');
    }

    public function resetCompanyProfile(Request $request)
    {
        $this->authorizeTabAccess($request, 'profil_perusahaan');

        $settings = SystemSetting::first();

        if ($settings->company_logo) {
            Storage::disk('public')->delete($settings->company_logo);
        }

        $settings->update([
            'company_name' => null,
            'company_address' => null,
            'company_phone' => null,
            'company_email' => null,
            'company_logo' => null,
            'company_website' => null,
            'company_npwp' => null,
        ]);

        return back()->with('success', 'Profil perusahaan dikembalikan ke default.');
    }

    public function updateAttendancePolicy(Request $request)
    {
        $this->authorizeTabAccess($request, 'kebijakan_absensi');

        $request->validate([
            'auto_checkout_time' => 'nullable|date_format:H:i',
            'minimum_work_hours' => 'required|integer|min:1|max:24',
            'half_day_threshold_hours' => 'required|integer|min:1|max:12',
            'require_checkout' => 'boolean',
            'allow_multiple_checkin' => 'boolean',
            'weekend_days' => 'nullable|array',
            'weekend_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        $settings = SystemSetting::first();
        $settings->update([
            'auto_checkout_time' => $request->auto_checkout_time,
            'minimum_work_hours' => $request->minimum_work_hours,
            'half_day_threshold_hours' => $request->half_day_threshold_hours,
            'require_checkout' => $request->boolean('require_checkout'),
            'allow_multiple_checkin' => $request->boolean('allow_multiple_checkin'),
            'weekend_days' => $request->weekend_days ?? [],
        ]);

        return back()->with('success', 'Kebijakan absensi berhasil disimpan.');
    }

    public function resetAttendancePolicy(Request $request)
    {
        $this->authorizeTabAccess($request, 'kebijakan_absensi');

        $settings = SystemSetting::first();
        $settings->update([
            'auto_checkout_time' => '23:59',
            'minimum_work_hours' => 8,
            'half_day_threshold_hours' => 4,
            'require_checkout' => true,
            'allow_multiple_checkin' => false,
            'weekend_days' => ['Saturday', 'Sunday'],
        ]);

        return back()->with('success', 'Kebijakan absensi dikembalikan ke default.');
    }

    public function updateFaceRecognition(Request $request)
    {
        $this->authorizeTabAccess($request, 'face_recognition');

        $request->validate([
            'face_similarity_threshold' => 'required|numeric|between:0.1,1.0',
            'face_max_registration_photos' => 'required|integer|min:1|max:20',
            'face_anti_spoofing_enabled' => 'boolean',
            'face_min_photo_quality' => 'required|integer|min:0|max:100',
            'face_require_liveness' => 'boolean',
        ]);

        $settings = SystemSetting::first();
        $settings->update([
            'face_similarity_threshold' => $request->face_similarity_threshold,
            'face_max_registration_photos' => $request->face_max_registration_photos,
            'face_anti_spoofing_enabled' => $request->boolean('face_anti_spoofing_enabled'),
            'face_min_photo_quality' => $request->face_min_photo_quality,
            'face_require_liveness' => $request->boolean('face_require_liveness'),
        ]);

        try {
            Http::timeout(5)->post(config('services.flask.url', env('FLASK_SERVICE_URL', 'http://face-service:5000')) . '/config', [
                'similarity_threshold' => $request->face_similarity_threshold,
            ]);
        } catch (\Exception $e) {
        }

        return back()->with('success', 'Pengaturan face recognition berhasil disimpan.');
    }

    public function resetFaceRecognition(Request $request)
    {
        $this->authorizeTabAccess($request, 'face_recognition');

        $settings = SystemSetting::first();
        $settings->update([
            'face_similarity_threshold' => 0.5,
            'face_max_registration_photos' => 6,
            'face_anti_spoofing_enabled' => false,
            'face_min_photo_quality' => 80,
            'face_require_liveness' => false,
        ]);

        try {
            Http::timeout(5)->post(config('services.flask.url', env('FLASK_SERVICE_URL', 'http://face-service:5000')) . '/config', [
                'similarity_threshold' => 0.5,
            ]);
        } catch (\Exception $e) {
        }

        return back()->with('success', 'Pengaturan face recognition dikembalikan ke default.');
    }

    public function testFaceService(Request $request)
    {
        $this->authorizeTabAccess($request, 'face_recognition');

        try {
            $response = $this->fetchFaceServiceHealth();

            if ($response->successful()) {
                return response()->json([
                    'status' => 'ok',
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Face service returned error: ' . $response->status(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot connect to face service: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateNotificationSettings(Request $request)
    {
        $this->authorizeTabAccess($request, 'notifikasi');

        $request->validate([
            'notify_late_checkin' => 'boolean',
            'notify_absence' => 'boolean',
            'notification_emails' => 'nullable|string',
        ]);

        $settings = SystemSetting::first();
        $settings->update([
            'notify_late_checkin' => $request->boolean('notify_late_checkin'),
            'notify_absence' => $request->boolean('notify_absence'),
            'notification_emails' => $request->notification_emails,
        ]);

        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }

    public function updateRetention(Request $request)
    {
        $this->authorizeTabAccess($request, 'backup');

        $request->validate([
            'backup_retention_days' => 'required|integer|min:1|max:365',
            'audit_log_retention_days' => 'required|integer|min:1|max:365',
        ]);

        $settings = SystemSetting::first();
        $settings->update([
            'backup_retention_days' => $request->backup_retention_days,
            'audit_log_retention_days' => $request->audit_log_retention_days,
        ]);

        return back()->with('success', 'Pengaturan retensi data berhasil disimpan.');
    }
}
