@extends('layouts.admin')

@section('header-title', 'Anomali GPS')
@section('header-subtitle', 'Pantau aktivitas absensi yang terdeteksi anomali lokasi atau perangkat')

@section('content')

    <div class="space-y-6">

        {{-- Filter --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.anomaly.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Karyawan</label>
                    <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="Nama / NIP"
                        class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm w-44 focus:border-sage focus:ring-2 focus:ring-sage/20" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Skor Min</label>
                    <input type="number" name="score_min" value="{{ request('score_min') }}" min="0" max="100" placeholder="0"
                        class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm w-24 focus:border-sage focus:ring-2 focus:ring-sage/20" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Skor Max</label>
                    <input type="number" name="score_max" value="{{ request('score_max') }}" min="0" max="100" placeholder="100"
                        class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm w-24 focus:border-sage focus:ring-2 focus:ring-sage/20" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Dari</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm focus:border-sage focus:ring-2 focus:ring-sage/20" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm focus:border-sage focus:ring-2 focus:ring-sage/20" />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-xl bg-sage px-4 py-2 text-sm font-bold text-slate-900 hover:brightness-95">Filter</button>
                    <a href="{{ route('admin.anomaly.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800">Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-5 py-4">Karyawan</th>
                            <th class="px-5 py-4">Waktu</th>
                            <th class="px-5 py-4">Anomaly Score</th>
                            <th class="px-5 py-4">Flags</th>
                            <th class="px-5 py-4">Device</th>
                            <th class="px-5 py-4">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($attendances as $att)
                            @php
                                $scoreBadge = match(true) {
                                    $att->anomaly_score === 0 => 'bg-sage text-slate-900',
                                    $att->anomaly_score < 30 => 'bg-sky text-slate-900',
                                    $att->anomaly_score < 60 => 'bg-peach text-slate-900',
                                    default => 'bg-rose-200 dark:bg-rose-900/40 text-rose-800 dark:text-rose-300',
                                };

                                $flagLabels = [
                                    'gps_too_stable' => 'GPS Stabil',
                                    'speed_anomaly' => 'Kecepatan',
                                    'accuracy_paradox' => 'Akurasi',
                                    'unknown_device' => 'Perangkat Baru',
                                    'timezone_mismatch' => 'Timezone',
                                    'mock_location' => 'Mock GPS',
                                ];
                            @endphp
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800 dark:text-white">{{ $att->user->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $att->user->username ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($att->tanggal)->format('d M Y') }}
                                    <span class="text-xs text-slate-400">
                                        {{ $att->jam_masuk ? \Carbon\Carbon::parse($att->jam_masuk)->format('H:i') : '' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $scoreBadge }}">
                                        {{ $att->anomaly_score }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(($att->anomaly_flags ?? []) as $flag)
                                            <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-700 px-2 py-0.5 text-[10px] font-semibold text-slate-600 dark:text-slate-300">
                                                {{ $flagLabels[$flag] ?? $flag }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500 dark:text-slate-400 max-w-[120px] truncate" title="{{ $att->device_fingerprint }}">
                                    {{ $att->device_fingerprint ? substr($att->device_fingerprint, 0, 12) . '...' : '-' }}
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $att->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center italic text-slate-500 dark:text-slate-400">
                                    Tidak ada data anomali ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700 p-4">
                {{ $attendances->links() }}
            </div>
        </div>

    </div>

@endsection
