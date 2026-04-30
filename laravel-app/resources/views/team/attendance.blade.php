@extends('layouts.absensi')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Laporan Absensi Tim</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau kehadiran anggota tim Anda</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-700">
            <form method="GET" action="{{ route('team.attendance') }}" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-slate-500">Dari</label>
                    <input type="date" name="start_date" value="{{ request('start_date', now()->format('Y-m-d')) }}"
                        class="px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-sage">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-slate-500">Sampai</label>
                    <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                        class="px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-sage">
                </div>
                <select name="status"
                    class="px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-sage">
                    <option value="Semua Status">Semua Status</option>
                    <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-sm font-bold hover:opacity-90 transition">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Karyawan</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Masuk</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pulang</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        <img src="{{ $att->user->profile_photo_url }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $att->user->name }}</p>
                                        <p class="text-[11px] text-slate-500 truncate">{{ $att->user->jabatan ?? $att->user->getRoleNames()->first() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300">
                                {{ $att->tanggal?->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                                {{ $att->jam_masuk ? \Carbon\Carbon::parse($att->jam_masuk)->format('H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                                {{ $att->jam_keluar ? \Carbon\Carbon::parse($att->jam_keluar)->format('H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($att->status === 'tepat_waktu')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full text-[11px] font-bold">Hadir</span>
                                @elseif($att->status === 'terlambat')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full text-[11px] font-bold">Terlambat</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-[11px] font-bold">{{ ucfirst($att->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mb-3">
                                        <span class="material-icons-round text-2xl text-slate-300 dark:text-slate-500">event_busy</span>
                                    </div>
                                    <p class="text-slate-500 font-medium text-sm">Tidak ada data absensi untuk filter ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-700">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection
