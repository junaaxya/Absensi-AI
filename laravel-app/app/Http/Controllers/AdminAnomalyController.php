<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EmployeeDevice;
use Illuminate\Http\Request;

class AdminAnomalyController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user')
            ->where('anomaly_score', '>', 0)
            ->orderByDesc('created_at');

        if ($request->filled('score_min')) {
            $query->where('anomaly_score', '>=', (int) $request->score_min);
        }

        if ($request->filled('score_max')) {
            $query->where('anomaly_score', '<=', (int) $request->score_max);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal', '<=', $request->date_to);
        }

        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $attendances = $query->paginate(25)->withQueryString();

        return view('admin.anomaly.index', compact('attendances'));
    }

    public function markDeviceUntrusted(EmployeeDevice $device)
    {
        $device->update(['is_trusted' => false]);

        return back()->with('success', 'Perangkat ditandai sebagai tidak dipercaya.');
    }
}
