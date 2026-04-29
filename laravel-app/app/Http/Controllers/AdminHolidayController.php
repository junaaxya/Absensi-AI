<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesSettingsTabs;
use App\Models\Holiday;
use Database\Seeders\HolidaySeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminHolidayController extends Controller
{
    use AuthorizesSettingsTabs;

    private function resolveHolidayDate(Request $request): ?string
    {
        return $request->input('date') ?? $request->input('holiday_date');
    }

    public function import(Request $request): RedirectResponse
    {
        $this->authorizeTabAccess($request, 'hari_libur');

        $year = (int) date('Y');
        $result = HolidaySeeder::importHolidays($year);

        if ($result['imported'] === 0) {
            return back()->with('info', "Semua hari libur {$year} sudah ada. Tidak ada data baru yang ditambahkan.");
        }

        $message = "{$result['imported']} hari libur {$year} berhasil diimpor.";
        if ($result['skipped'] > 0) {
            $message .= " ({$result['skipped']} sudah ada, dilewati)";
        }

        return back()->with('success', $message);
    }

    public function store(Request $request)
    {
        $this->authorizeTabAccess($request, 'hari_libur');

        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'nullable|date',
            'holiday_date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_national' => 'boolean',
        ]);

        $date = $this->resolveHolidayDate($request);
        if ($date === null) {
            return back()->withErrors(['date' => 'Tanggal wajib diisi.'])->withInput();
        }

        Holiday::create([
            'name' => $request->name,
            'date' => $date,
            'description' => $request->description,
            'is_national' => $request->boolean('is_national', true),
        ]);

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $this->authorizeTabAccess($request, 'hari_libur');

        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'nullable|date',
            'holiday_date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_national' => 'boolean',
        ]);

        $date = $this->resolveHolidayDate($request);
        if ($date === null) {
            return back()->withErrors(['date' => 'Tanggal wajib diisi.'])->withInput();
        }

        $holiday->update([
            'name' => $request->name,
            'date' => $date,
            'description' => $request->description,
            'is_national' => $request->boolean('is_national', true),
        ]);

        return back()->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(Request $request, Holiday $holiday)
    {
        $this->authorizeTabAccess($request, 'hari_libur');

        $holiday->delete();

        return back()->with('success', 'Hari libur berhasil dihapus.');
    }
}
