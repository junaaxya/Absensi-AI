<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesSettingsTabs;
use App\Services\ExportService;
use Illuminate\Http\Request;

class AdminExportController extends Controller
{
    use AuthorizesSettingsTabs;

    public function exportAttendance(Request $request, ExportService $exportService)
    {
        $this->authorizeTabAccess($request, 'export');

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'department_id' => 'nullable|integer|exists:departments,id',
        ]);

        return $exportService->exportAttendance(
            $request->start_date,
            $request->end_date,
            $request->department_id
        );
    }

    public function exportEmployees(Request $request, ExportService $exportService)
    {
        $this->authorizeTabAccess($request, 'export');

        $request->validate([
            'department_id' => 'nullable|integer|exists:departments,id',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        return $exportService->exportEmployees(
            $request->department_id,
            $request->status
        );
    }
}
