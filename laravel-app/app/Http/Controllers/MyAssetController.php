<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use Illuminate\Support\Facades\Auth;

class MyAssetController extends Controller
{
    public function index()
    {
        $assignments = AssetAssignment::where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->with('asset.category')
            ->orderByDesc('assigned_at')
            ->get();

        $history = AssetAssignment::where('user_id', Auth::id())
            ->whereNotNull('returned_at')
            ->with('asset.category')
            ->orderByDesc('returned_at')
            ->limit(10)
            ->get();

        return view('my-assets.index', compact('assignments', 'history'));
    }
}
