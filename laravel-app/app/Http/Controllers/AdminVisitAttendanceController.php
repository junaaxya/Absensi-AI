<?php

namespace App\Http\Controllers;

use App\Models\VisitAttendance;
use Illuminate\Http\Request;

class AdminVisitAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitAttendance::with('user');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('location_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->where('tanggal', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->where('tanggal', '<=', $dateTo);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $visits = $query->orderByDesc('tanggal')
            ->orderByDesc('check_in_time')
            ->paginate(25)
            ->withQueryString();

        return view('admin.visits.index', compact('visits'));
    }

    public function show(VisitAttendance $visitAttendance)
    {
        $visitAttendance->load(['user', 'visitLocations' => function ($q) {
            $q->orderBy('recorded_at');
        }]);

        return view('admin.visits.show', compact('visitAttendance'));
    }
}
