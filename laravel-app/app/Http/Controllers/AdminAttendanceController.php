<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Izin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user');
        \App\Services\RoleBasedScope::scopeAttendance($query, auth()->user());

        // Filter: Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } else {
            // Default hari ini
            $query->whereDate('tanggal', Carbon::today());
        }

        // Filter: Status
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $status = strtolower($request->status);
            if (in_array($status, ['hadir', 'terlambat'])) {
                $query->where('status', $status);
            } elseif (in_array($status, ['izin', 'sakit', 'cuti', 'dinas'])) {
                // Izin logic handled separately below, but for now filtering attendance status not sufficient
                // We'll focus on attendance records primarily here.
                // For a mixed list (Attendance + Izin), we'd need a union or different approach.
                // Let's stick to Attendance model for now and handle Izin as separate collection if needed,
                // or just show Attendance records which include "status" if we designed it that way.
                // However, based on the UI, it shows "Izin" rows too.
            }
        }

        // Filter: Search User
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('tanggal', 'desc')->orderBy('jam_masuk', 'desc')->paginate(10)->withQueryString();
        
        // Load Izin data (simplified for now to mix in view or separate list if requested, 
        // but based on design, it's one table. Union is cleaner but complex with Eloquent.
        // Let's fetch Izins separately and merge manually for display if needed, 
        // OR simpler: view only Attendances for now, as Izin usually creates an Attendance record 
        // with status='izin' in many systems. 
        // Checking migration... 'attendances' has 'status'. 'izins' has separate table.
        // If we want both in one table, we should fetch both and merge.
        
        // STRATEGY: 
        // 1. Fetch Attendance records (Hadir/Terlambat/Alpha if generated)
        // 2. Fetch Izin records (Active within date range)
        // 3. Merge and Sort (Pagination will be tricky with merge, so typically we just show Attendance 
        //    and assume Izin creates a record in Attendance table OR we list them separately.
        //    Given the constraint of "Admin Portal" layout, I will implement a robust 
        //    "Attendance Only" view first, adding Izin rows if they exist in Attendance table 
        //    or purely from Izin table if we treat them as separate data sources.)
        
        // DECISION: 
        // To make pagination work seamlessly, we will stick to `Attendance` model.
        // *Assumption*: The system creates `Attendance` records with status='izin'/'sakit' 
        // when an Izin is approved. (Common pattern).
        // If not, we will just show actual check-ins (Hadir/Terlambat).
        
        // Let's check if Izin creates Attendance record. 
        // Based on previous file reads, `IzinController@store` only creates `Izin` record.
        // `AttendanceController@autoAttendance` creates `Attendance`.
        // So they are separate. 
        
        // For this specific UI which shows mixed types (Hadir, Terlambat, Izin), 
        // the best approach without complex Union pagination is to fetch everything 
        // if pagination isn't strict database-side, OR use a View/Union.
        
        // SIMPLIFIED APPROACH for V1:
        // We will display `Attendance` records. 
        // Only `Hadir` and `Terlambat` will appear here.
        // `Izin` records will be fetched separately and stacked (or separate tab if we had one).
        // BUT, user wants the table to look like the design.
        // So I will fetch `izins` valid for the date range and merge them into the collection
        // for the current page, purely for display. 
        
        // Re-fetching Izins for display
        $izins = collect([]);
        if ((!$request->filled('status') || in_array(strtolower($request->status), ['izin', 'sakit', 'cuti', 'dinas']))) {
             $izinQuery = Izin::with('user')
                ->where('status', 'approved');
             \App\Services\RoleBasedScope::scopeIzin($izinQuery, auth()->user());
                
             if ($request->filled('start_date') && $request->filled('end_date')) {
                $izinQuery->whereDate('tanggal_mulai', '<=', $request->end_date)
                          ->whereDate('tanggal_selesai', '>=', $request->start_date);
             } else {
                $izinQuery->whereDate('tanggal_mulai', '<=', Carbon::today())
                          ->whereDate('tanggal_selesai', '>=', Carbon::today());
             }
             
             if ($request->filled('q')) {
                $search = $request->q;
                $izinQuery->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
             }
             
             $izins = $izinQuery->get();
        }

        return view('admin.attendance', compact('attendances', 'izins'));
    }
}
