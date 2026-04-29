<?php

namespace App\Services;

use App\DTOs\AntiCheatResult;
use App\Models\Attendance;
use App\Models\EmployeeDevice;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;

class AntiCheatService
{
    protected array $data;
    protected User $user;
    protected SystemSetting $settings;

    public function __construct(array $data, User $user, SystemSetting $settings)
    {
        $this->data = $data;
        $this->user = $user;
        $this->settings = $settings;
    }

    public function validate(): AntiCheatResult
    {
        $totalScore = 0;
        $flags = [];

        $checks = [
            $this->checkGpsVariance(),
            $this->checkSpeedAnomaly(),
            $this->checkAccuracyParadox(),
            $this->checkDeviceConsistency(),
            $this->checkTimezoneMismatch(),
            $this->checkMockLocation(),
        ];

        foreach ($checks as $check) {
            $totalScore += $check['score'];
            if ($check['flag']) {
                $flags[] = $check['flag'];
            }
        }

        $rejectThreshold = $this->settings->anomaly_score_reject_threshold ?? 60;
        $warningThreshold = $this->settings->anomaly_score_warning_threshold ?? 30;

        return new AntiCheatResult(
            score: $totalScore,
            flags: $flags,
            passed: $totalScore < $rejectThreshold,
            warning: $totalScore >= $warningThreshold,
        );
    }

    protected function checkGpsVariance(): array
    {
        $readings = $this->data['gps_readings'] ?? [];

        if (!is_array($readings) || count($readings) < 3) {
            return ['score' => 0, 'flag' => null];
        }

        $lats = array_column($readings, 'latitude');
        $longs = array_column($readings, 'longitude');

        if (empty($lats) || empty($longs)) {
            return ['score' => 0, 'flag' => null];
        }

        $latVariance = $this->variance($lats);
        $longVariance = $this->variance($longs);

        if ($latVariance < 0.000001 && $longVariance < 0.000001) {
            return ['score' => 15, 'flag' => 'gps_too_stable'];
        }

        return ['score' => 0, 'flag' => null];
    }

    protected function checkSpeedAnomaly(): array
    {
        $lastAttendance = Attendance::where('user_id', $this->user->id)
            ->whereNotNull('lat_in')
            ->whereNotNull('long_in')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastAttendance) {
            return ['score' => 0, 'flag' => null];
        }

        $currentLat = $this->data['latitude'] ?? null;
        $currentLong = $this->data['longitude'] ?? null;

        if ($currentLat === null || $currentLong === null) {
            return ['score' => 0, 'flag' => null];
        }

        $lastLat = $lastAttendance->lat_out ?? $lastAttendance->lat_in;
        $lastLong = $lastAttendance->long_out ?? $lastAttendance->long_in;

        $distance = $this->haversineDistance($lastLat, $lastLong, $currentLat, $currentLong);

        $lastTime = $lastAttendance->jam_keluar
            ? Carbon::parse($lastAttendance->tanggal->format('Y-m-d') . ' ' . Carbon::parse($lastAttendance->jam_keluar)->format('H:i:s'))
            : Carbon::parse($lastAttendance->tanggal->format('Y-m-d') . ' ' . Carbon::parse($lastAttendance->jam_masuk)->format('H:i:s'));

        $minutesElapsed = Carbon::now()->diffInMinutes($lastTime);

        if ($minutesElapsed <= 0) {
            return ['score' => 0, 'flag' => null];
        }

        $speedKmh = ($distance / $minutesElapsed) * 60;

        $maxSpeed = match (true) {
            $minutesElapsed < 2   => 5,
            $minutesElapsed < 15  => 30,
            $minutesElapsed < 60  => 120,
            default               => 200,
        };

        if ($speedKmh > $maxSpeed) {
            return ['score' => 20, 'flag' => 'speed_anomaly'];
        }

        return ['score' => 0, 'flag' => null];
    }

    protected function checkAccuracyParadox(): array
    {
        $accuracy = $this->data['accuracy'] ?? null;

        if ($accuracy === null) {
            return ['score' => 0, 'flag' => null];
        }

        if ((float) $accuracy === 1.0 || (float) $accuracy < 3.0) {
            return ['score' => 10, 'flag' => 'accuracy_paradox'];
        }

        return ['score' => 0, 'flag' => null];
    }

    protected function checkDeviceConsistency(): array
    {
        $fingerprint = $this->data['device_fingerprint'] ?? null;

        if (!$fingerprint) {
            return ['score' => 0, 'flag' => null];
        }

        $existingDevice = EmployeeDevice::where('device_fingerprint', $fingerprint)
            ->where('user_id', $this->user->id)
            ->first();

        if ($existingDevice) {
            return ['score' => 0, 'flag' => null];
        }

        $maxDevices = $this->settings->max_devices_per_user ?? 2;
        $deviceCount = EmployeeDevice::where('user_id', $this->user->id)->count();

        if ($deviceCount >= $maxDevices) {
            return ['score' => 15, 'flag' => 'unknown_device'];
        }

        return ['score' => 0, 'flag' => null];
    }

    protected function checkTimezoneMismatch(): array
    {
        $clientTimezone = $this->data['timezone_client'] ?? null;
        $longitude = $this->data['longitude'] ?? null;

        if (!$clientTimezone || $longitude === null) {
            return ['score' => 0, 'flag' => null];
        }

        try {
            $clientTz = new \DateTimeZone($clientTimezone);
            $clientOffset = $clientTz->getOffset(new \DateTime('now', new \DateTimeZone('UTC'))) / 3600;
        } catch (\Exception $e) {
            return ['score' => 0, 'flag' => null];
        }

        $gpsEstimatedOffset = (float) $longitude / 15.0;

        if (abs($clientOffset - $gpsEstimatedOffset) > 2) {
            return ['score' => 10, 'flag' => 'timezone_mismatch'];
        }

        return ['score' => 0, 'flag' => null];
    }

    protected function checkMockLocation(): array
    {
        $mockDetected = $this->data['mock_location_detected'] ?? false;

        if ($mockDetected) {
            return ['score' => 20, 'flag' => 'mock_location'];
        }

        return ['score' => 0, 'flag' => null];
    }

    protected function variance(array $values): float
    {
        $count = count($values);
        if ($count < 2) {
            return 0.0;
        }

        $mean = array_sum($values) / $count;
        $sumSquaredDiffs = 0.0;

        foreach ($values as $value) {
            $sumSquaredDiffs += ($value - $mean) ** 2;
        }

        return $sumSquaredDiffs / $count;
    }

    protected function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
