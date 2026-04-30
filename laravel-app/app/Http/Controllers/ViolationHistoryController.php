<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\WarningLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViolationHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $year = $request->get('year', now()->year);
        $month = $request->get('month');

        $query = Violation::where('user_id', $user->id)
            ->with('violationType')
            ->whereYear('tanggal', $year);

        if ($month) {
            $query->whereMonth('tanggal', $month);
        }

        $violations = $query->orderByDesc('tanggal')->paginate(20)->withQueryString();

        $warningLetters = WarningLetter::where('user_id', $user->id)
            ->orderByDesc('issued_at')
            ->get();

        $yearlyTotal = Violation::where('user_id', $user->id)
            ->whereYear('tanggal', $year)
            ->sum('points');

        return view('violations.index', compact('violations', 'warningLetters', 'yearlyTotal', 'year', 'month'));
    }
}
