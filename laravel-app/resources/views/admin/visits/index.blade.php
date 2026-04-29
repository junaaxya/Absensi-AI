@extends('layouts.admin')

@section('header-title', 'Kunjungan')
@section('header-subtitle', 'Kelola data kunjungan karyawan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Absensi Kunjungan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau seluruh kunjungan karyawan ke client.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark shadow-sm">
        <form method="GET" action="{{ route('admin.visits.index') }}"
            class="p-4 md:p-6 border-b border-slate-100 dark:border-slate-700">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, client, lokasi..."
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white text-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white text-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white text-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white text-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                        <option value="">Semua</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary hover:brightness-95 text-slate-900 font-bold rounded-xl transition text-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.visits.index') }}"
                        class="px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition text-sm">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Check In</th>
                        <th class="px-6 py-4">Check Out</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($visits as $visit)
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/30 flex items-center justify-center text-xs font-bold text-slate-900">
                                    {{ substr($visit->user->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-white">{{ $visit->user->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $visit->user->jabatan ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $visit->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">{{ $visit->client_name }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $visit->location_name }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $visit->check_in_time->format('H:i') }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $visit->check_out_time ? $visit->check_out_time->format('H:i') : '-' }}</td>
                        <td class="px-6 py-4">
                            @if($visit->status === 'active')
                                <span class="inline-flex items-center rounded-full bg-sky-100 dark:bg-sky-900/30 px-3 py-1 text-xs font-bold text-sky-700 dark:text-sky-400">Aktif</span>
                            @elseif($visit->status === 'completed')
                                <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400">Selesai</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-rose-100 dark:bg-rose-900/30 px-3 py-1 text-xs font-bold text-rose-700 dark:text-rose-400">Dibatalkan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.visits.show', $visit) }}"
                                class="inline-flex items-center gap-1 text-sm font-medium text-sky-600 dark:text-sky-400 hover:text-sky-800 dark:hover:text-sky-300 transition">
                                <span class="material-icons-round text-base">visibility</span>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center italic text-slate-500 dark:text-slate-400">
                            Belum ada data kunjungan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 dark:border-slate-700 p-4 md:p-5">
            {{ $visits->links() }}
        </div>
    </div>
</div>
@endsection
