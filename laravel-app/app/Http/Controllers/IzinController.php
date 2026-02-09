<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Izin;

class IzinController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jenis'           => 'required|string',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string',
            'dokumen'         => 'nullable|file|mimes:pdf,docx,doc,jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('dokumen')) {
            $path = $request->file('dokumen')->store('izin', 'public');
        }

        Izin::create([
            'user_id'         => Auth::id(),
            'jenis'           => $request->jenis,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan'          => $request->alasan,
            'dokumen'         => $path,
            'status'          => 'pending',
        ]);

        return back()->with('success', 'Pengajuan ketidakhadiran berhasil dikirim.');
    }
}
