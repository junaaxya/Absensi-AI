@extends('layouts.absensi')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        .employee-dashboard {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
@endpush


@section('content')
        <!-- ANNOUNCEMENTS SECTION -->
        @if(isset($activeAnnouncements) && $activeAnnouncements->count() > 0)
            <div class="mb-6 space-y-4">
                @foreach($activeAnnouncements as $announcement)
                    @php
                        $bgColor = match($announcement->type) {
                            'info' => 'bg-blue-50 border-blue-100 text-blue-800',
                            'warning' => 'bg-yellow-50 border-yellow-100 text-yellow-800',
                            'danger' => 'bg-red-50 border-red-100 text-red-800',
                            default => 'bg-slate-50 border-slate-100 text-slate-800',
                        };
                        $icon = match($announcement->type) {
                            'info' => 'info',
                            'warning' => 'warning',
                            'danger' => 'error',
                            default => 'campaign',
                        };
                        $iconColor = match($announcement->type) {
                            'info' => 'text-blue-500',
                            'warning' => 'text-yellow-500',
                            'danger' => 'text-red-500',
                            default => 'text-slate-500',
                        };
                    @endphp
                    <div class="{{ $bgColor }} dark:bg-slate-800 dark:border-slate-700 border rounded-2xl p-4 flex items-start gap-3 shadow-sm relative overflow-hidden">
                        <div class="flex-shrink-0 mt-0.5">
                            <span class="material-icons-round {{ $iconColor }}">{{ $icon }}</span>
                        </div>
                        <div class="flex-1 z-10">
                            <h4 class="font-bold text-sm mb-1 dark:text-white">{{ $announcement->title }}</h4>
                            <p class="text-xs opacity-90 leading-relaxed dark:text-slate-300">{{ $announcement->content }}</p>
                            <p class="text-[10px] mt-2 opacity-70 font-medium dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($announcement->start_date)->format('d M Y') }}
                            </p>
                        </div>
                        <!-- Decorative Circle -->
                        <div class="absolute -right-4 -bottom-4 w-16 h-16 rounded-full bg-white dark:bg-slate-600 opacity-20 z-0"></div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- COMPACT PROFILE HEADER (Gojek/Shopee Style) -->
        <div class="md:hidden flex items-center justify-between mb-6 bg-white dark:bg-card-dark p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary/30 dark:bg-primary/20 flex items-center justify-center text-slate-900 dark:text-white font-bold border border-primary">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="leading-tight">
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Selamat Pagi,</p>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">{{ explode(' ', $user->name)[0] }}</h2>
                </div>
            </div>
            <div class="flex items-center gap-2">
                 <div class="text-right hidden sm:block">
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d M') }}</p>
                </div>
                <button class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>
            </div>
        </div>


    <div x-data="{}" class="employee-dashboard space-y-6 pb-16">
        <header class="relative overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-6 shadow-sm md:p-8">
            <div class="relative z-10 flex flex-col gap-2">
                <p class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Employee Dashboard</p>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white md:text-3xl">Halo, {{ $user->name }} 👋</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 md:text-base">
                    Ringkasan absensi Anda hari ini, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}.
                </p>
            </div>
            <div class="pointer-events-none absolute -right-10 -top-14 h-40 w-40 rounded-full bg-primary/30 dark:bg-primary/20 blur-2xl"></div>
        </header>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-6 shadow-sm xl:col-span-5">
                <h2 class="mb-5 flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
                    <span class="material-icons-round text-emerald-600 dark:text-primary">history</span>
                    Aktivitas Hari Ini
                </h2>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Absensi Masuk</p>
                        @if($attendanceToday && $attendanceToday->jam_masuk)
                            <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                                <span class="material-icons-round text-base">check_circle</span>
                                {{ \Carbon\Carbon::parse($attendanceToday->jam_masuk)->format('H:i') }} WIB
                            </p>
                        @else
                            <p class="mt-2 flex items-center gap-2 text-sm italic text-slate-500 dark:text-slate-400">
                                <span class="material-icons-round text-base">warning_amber</span>
                                Absen masuk belum dilakukan
                            </p>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Makan Siang</p>
                        <p class="mt-2 text-sm italic text-slate-500 dark:text-slate-400">Belum dilakukan</p>
                    </div>

                    <div class="rounded-2xl border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/20 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-sky-700 dark:text-sky-400">Absen Keluar</p>
                        @if($attendanceToday && $attendanceToday->jam_keluar)
                            <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-sky-700 dark:text-sky-400">
                                <span class="material-icons-round text-base">check_circle</span>
                                {{ \Carbon\Carbon::parse($attendanceToday->jam_keluar)->format('H:i') }} WIB
                            </p>
                        @else
                            <p class="mt-2 flex items-center gap-2 text-sm text-sky-700 dark:text-sky-400">
                                <span class="material-icons-round text-base">hourglass_empty</span>
                                Belum dilakukan
                            </p>
                        @endif
                    </div>
                </div>
            </section>

            <section class="space-y-4 xl:col-span-7">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Jam Masuk Kerja</p>
                        <p class="mt-2 text-xl font-bold text-slate-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($workStartTime)->format('H.i') }} WIB
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Jam Pulang Kerja</p>
                        <p class="mt-2 text-xl font-bold text-slate-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($workEndTime)->format('H.i') }} WIB
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-5 shadow-sm">
                    <h2 class="mb-3 text-sm font-bold text-slate-900 dark:text-white">Kegiatan Hari Ini</h2>
                    <textarea
                        class="h-32 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-4 text-sm text-slate-500 dark:text-slate-300"
                        placeholder="Tuliskan laporan singkat kegiatan hari ini..."
                        >{{ $attendanceToday?->kegiatan && !in_array($attendanceToday->kegiatan, ['hadir', 'hadir_lembur']) ? $attendanceToday->kegiatan : '' }}</textarea>
                    <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">Kolom ini siap dipakai saat fitur simpan kegiatan diaktifkan.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <button
                        @click="startAttendance('masuk')"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-sage px-4 py-4 text-sm font-bold text-slate-900 transition hover:brightness-95">
                        <span class="material-icons-round">login</span>
                        Absen Masuk
                    </button>

                    <button
                        @click="startAttendance('pulang')"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-peach px-4 py-4 text-sm font-bold text-slate-900 transition hover:brightness-95">
                        <span class="material-icons-round">logout</span>
                        Absen Keluar
                    </button>

                    <button
                        @click="$dispatch('open-modal', 'visit-checkin-modal')"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-sky px-4 py-4 text-sm font-bold text-slate-900 transition hover:brightness-95">
                        <span class="material-icons-round">location_on</span>
                        Absen Kunjungan
                    </button>

                    <button
                        @click="$dispatch('open-modal', 'izin')"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-lavender px-4 py-4 text-sm font-bold text-slate-900 transition hover:brightness-95">
                        <span class="material-icons-round">event_busy</span>
                        Pengajuan Ketidakhadiran
                    </button>
                </div>
            </section>
        </div>

        <!-- VIOLATION POINTS SUMMARY -->
        @if(isset($monthlyViolationPoints))
        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-6 shadow-sm">
            <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
                <span class="material-icons-round text-rose-500">gavel</span>
                Poin Pelanggaran Bulan Ini
            </h2>

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-2">Total Poin</p>
                    <div class="flex items-center gap-3">
                        <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $monthlyViolationPoints }}</p>
                        @php
                            $violationBadge = match(true) {
                                $monthlyViolationPoints === 0 => ['bg-sage text-slate-900', 'Bersih'],
                                $monthlyViolationPoints < 10 => ['bg-peach text-slate-900', 'Perhatian'],
                                default => ['bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400', 'Peringatan'],
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $violationBadge[0] }}">
                            {{ $violationBadge[1] }}
                        </span>
                    </div>

                    @if(isset($activeWarningLetter) && $activeWarningLetter)
                        @php
                            $spBadge = match($activeWarningLetter->type) {
                                'SP1' => 'bg-peach text-slate-900',
                                'SP2' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                'SP3' => 'bg-red-500 text-white',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <div class="mt-3 flex items-center gap-2">
                            <span class="material-icons-round text-sm text-rose-500">warning</span>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $spBadge }}">
                                {{ $activeWarningLetter->type }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">aktif</span>
                        </div>
                    @endif
                </div>

                @if(isset($recentViolations) && $recentViolations->count() > 0)
                <div class="flex-1 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-2">Riwayat Terakhir</p>
                    <div class="space-y-2">
                        @foreach($recentViolations->take(3) as $v)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-400">{{ $v->tanggal->format('d/m') }}</span>
                                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $v->violationType->name ?? '-' }}</span>
                                </div>
                                @php
                                    $recentBadge = match(true) {
                                        $v->points >= 10 => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                        $v->points >= 3 => 'bg-peach text-slate-900',
                                        default => 'bg-sage text-slate-900',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $recentBadge }}">
                                    {{ $v->points }}p
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </section>
        @endif

        <!-- ACTIVE VISIT SECTION -->
        @if(isset($activeVisit) && $activeVisit)
        <section x-data="visitTracker({{ $activeVisit->id }})" class="rounded-3xl border border-sky-200 dark:border-sky-800 bg-white dark:bg-card-dark p-6 shadow-sm">
            <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
                <span class="material-icons-round text-sky-500">location_on</span>
                Kunjungan Aktif
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-sky-50 dark:bg-sky-900/20 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Client</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $activeVisit->client_name }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-sky-50 dark:bg-sky-900/20 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Lokasi</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $activeVisit->location_name }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-sky-50 dark:bg-sky-900/20 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Check In</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $activeVisit->check_in_time->format('H:i') }} WIB</p>
                </div>
            </div>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <button @click="startVisitCheckout()"
                    class="flex items-center justify-center gap-2 rounded-2xl bg-peach px-6 py-3 text-sm font-bold text-slate-900 transition hover:brightness-95">
                    <span class="material-icons-round">logout</span>
                    Check Out Kunjungan
                </button>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span class="material-icons-round text-sm text-emerald-500">gps_fixed</span>
                    <span x-text="trackingStatus">Pelacakan GPS aktif</span>
                </div>
            </div>
        </section>
        @endif

        <!-- VISIT HISTORY -->
        @if(isset($visitHistory) && $visitHistory->count() > 0)
        <section class="overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark shadow-sm">
            <div class="border-b border-slate-100 dark:border-slate-700 p-4 md:p-6">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
                    <span class="material-icons-round text-sky-500">history</span>
                    Riwayat Kunjungan
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Lokasi</th>
                            <th class="px-6 py-4">Check In</th>
                            <th class="px-6 py-4">Check Out</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($visitHistory as $visit)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4">{{ $visit->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">{{ $visit->client_name }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $visit->location_name }}</td>
                            <td class="px-6 py-4">{{ $visit->check_in_time->format('H:i') }} WIB</td>
                            <td class="px-6 py-4">{{ $visit->check_out_time ? $visit->check_out_time->format('H:i') . ' WIB' : '-' }}</td>
                            <td class="px-6 py-4">
                                @if($visit->status === 'active')
                                    <span class="inline-flex items-center rounded-full bg-sky-100 dark:bg-sky-900/30 px-3 py-1 text-xs font-bold text-sky-700 dark:text-sky-400">Aktif</span>
                                @elseif($visit->status === 'completed')
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400">Selesai</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-rose-100 dark:bg-rose-900/30 px-3 py-1 text-xs font-bold text-rose-700 dark:text-rose-400">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif

        <section class="overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 dark:border-slate-700 p-4 md:flex-row md:items-center md:justify-between md:p-6">
                <form method="GET" action="{{ route('dashboard') }}" class="flex w-full flex-col gap-3 md:flex-row md:items-center">
                    <div class="relative w-full md:max-w-sm">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input
                            name="q"
                            type="text"
                            value="{{ $search }}"
                            placeholder="Cari status, kegiatan, atau jam..."
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white py-2 pl-10 pr-4 text-sm focus:border-sage focus:outline-none focus:ring-2 focus:ring-sage/20" />
                    </div>

                    <div class="flex w-full flex-col gap-2 sm:flex-row md:w-auto">
                        <input
                            name="start_date"
                            type="date"
                            value="{{ $startDate }}"
                            class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm focus:border-sage focus:outline-none focus:ring-2 focus:ring-sage/20" />
                        <input
                            name="end_date"
                            type="date"
                            value="{{ $endDate }}"
                            class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 dark:text-white px-3 py-2 text-sm focus:border-sage focus:outline-none focus:ring-2 focus:ring-sage/20" />
                    </div>

                    <div class="flex items-center gap-2 md:ml-auto">
                        <button type="submit" class="rounded-xl bg-sage px-4 py-2 text-sm font-semibold text-slate-900 hover:brightness-95">
                            Filter
                        </button>
                        <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Jam Masuk</th>
                            <th class="px-6 py-4">Jam Keluar</th>
                            <th class="px-6 py-4">Jam Kerja</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($attendanceHistory as $row)
                            @php
                                $durationLabel = '-';
                                if ($row->jam_masuk && $row->jam_keluar) {
                                    $minutes = \Carbon\Carbon::parse($row->jam_masuk)->diffInMinutes(\Carbon\Carbon::parse($row->jam_keluar));
                                    $hours = intdiv($minutes, 60);
                                    $remainingMinutes = $minutes % 60;
                                    $durationLabel = $remainingMinutes > 0 ? "{$hours}j {$remainingMinutes}m" : "{$hours}j";
                                }
                            @endphp
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4">{{ ($attendanceHistory->firstItem() ?? 0) + $loop->index }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4">{{ $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') . ' WIB' : '-' }}</td>
                                <td class="px-6 py-4">{{ $row->jam_keluar ? \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') . ' WIB' : '-' }}</td>
                                <td class="px-6 py-4">{{ $durationLabel }}</td>
                                <td class="px-6 py-4">
                                    @if($row->status === 'tepat_waktu')
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400">Tepat Waktu</span>
                                    @elseif($row->status === 'terlambat')
                                        <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/30 px-3 py-1 text-xs font-bold text-amber-700 dark:text-amber-400">Terlambat</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-bold text-slate-600 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $row->status ?? '-')) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                    @if($row->kegiatan === 'hadir_lembur')
                                        Lembur
                                    @elseif($row->kegiatan === 'hadir')
                                        Hadir
                                    @else
                                        {{ $row->kegiatan ?: '-' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center italic text-slate-500 dark:text-slate-400">Belum ada riwayat absensi pada filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700 p-4 md:p-5">
                {{ $attendanceHistory->links() }}
            </div>
        </section>

        <x-pastel-modal name="izin" title="Pengajuan Ketidakhadiran" maxWidth="lg">
            <div x-data="{
                selectedJenis: 'izin',
                selectedLeaveTypeId: '',
                leaveBalance: null,
                loadingBalance: false,
                async fetchBalance() {
                    if (!this.selectedLeaveTypeId) { this.leaveBalance = null; return; }
                    this.loadingBalance = true;
                    try {
                        const res = await fetch('{{ route('izin.balance') }}?leave_type_id=' + this.selectedLeaveTypeId);
                        this.leaveBalance = await res.json();
                    } catch (e) { this.leaveBalance = null; }
                    this.loadingBalance = false;
                }
            }">
                <form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Jenis Pengajuan</label>
                        <select name="jenis" x-model="selectedJenis" required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white px-4 py-2 transition focus:border-sage focus:ring-4 focus:ring-sage/20">
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="cuti">Cuti</option>
                            <option value="dinas">Dinas</option>
                            <option value="wfa">Work From Anywhere (WFA)</option>
                        </select>
                    </div>

                    <div x-show="selectedJenis === 'cuti'" x-transition>
                        <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Tipe Cuti</label>
                        <select name="leave_type_id" x-model="selectedLeaveTypeId" @change="fetchBalance()"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white px-4 py-2 transition focus:border-sage focus:ring-4 focus:ring-sage/20">
                            <option value="">-- Pilih Tipe Cuti --</option>
                            @if(isset($leaveTypes))
                                @foreach($leaveTypes ?? [] as $lt)
                                    <option value="{{ $lt->id }}">{{ $lt->name }} ({{ $lt->days_quota }} hari)</option>
                                @endforeach
                            @endif
                        </select>

                        <div x-show="leaveBalance" x-transition class="mt-2 rounded-xl border border-sage/30 bg-sage/10 p-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600 dark:text-slate-400">Sisa Cuti:</span>
                                <span class="font-bold text-slate-900 dark:text-white" x-text="leaveBalance?.remaining + ' hari'"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs mt-1">
                                <span class="text-slate-500">Kuota: <span x-text="leaveBalance?.quota"></span></span>
                                <span class="text-slate-500">Terpakai: <span x-text="leaveBalance?.used"></span></span>
                            </div>
                        </div>
                    </div>

                    <div x-show="selectedJenis === 'wfa'" x-transition>
                        <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Lokasi WFA</label>
                        <input type="text" name="wfa_location" placeholder="Contoh: Rumah, Cafe XYZ, Co-working Space"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white px-4 py-2 transition focus:border-sage focus:ring-4 focus:ring-sage/20"
                            :required="selectedJenis === 'wfa'">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" required
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white px-4 py-2 transition focus:border-sage focus:ring-4 focus:ring-sage/20">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" required
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white px-4 py-2 transition focus:border-sage focus:ring-4 focus:ring-sage/20">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Alasan</label>
                        <textarea name="alasan" required rows="3"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white px-4 py-2 transition focus:border-sage focus:ring-4 focus:ring-sage/20"></textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Lampirkan Foto atau Dokumen</label>
                        <input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                            class="w-full text-sm text-slate-600 dark:text-slate-400 file:mr-4 file:rounded-xl file:border-0 file:bg-sage/20 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-700 dark:file:text-slate-300 hover:file:bg-sage/30">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Format: PDF, JPG, PNG, DOCX (Max 5MB)</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="closeModal()"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-slate-800">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-sage px-4 py-2 text-sm font-medium text-slate-900 shadow-sm transition hover:brightness-95">
                            Ajukan
                        </button>
                    </div>
                </form>
            </div>
        </x-pastel-modal>
    </div>

    <!-- VISIT CHECK-IN MODAL -->
    <x-pastel-modal name="visit-checkin-modal" title="Absen Kunjungan" maxWidth="2xl">
        <div x-data="visitCheckinHandler()"
            @open-visit-camera.window="initVisitCamera()"
            @modal-closed.window="if ($event.detail === 'visit-checkin-modal') resetVisitState()"
            class="rounded-3xl p-2 md:p-4">

            <div x-show="visitStep === 'form'" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Nama Client / Perusahaan</label>
                    <input type="text" x-model="visitClientName" placeholder="PT. Contoh Sejahtera"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white px-4 py-2 text-sm transition focus:border-sage focus:ring-4 focus:ring-sage/20">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Nama Lokasi</label>
                    <input type="text" x-model="visitLocationName" placeholder="Gedung A, Lantai 5"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white px-4 py-2 text-sm transition focus:border-sage focus:ring-4 focus:ring-sage/20">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-400">Tujuan Kunjungan</label>
                    <textarea x-model="visitPurpose" rows="3" placeholder="Meeting pembahasan project..."
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 dark:text-white px-4 py-2 text-sm transition focus:border-sage focus:ring-4 focus:ring-sage/20"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="$dispatch('close-modal', 'visit-checkin-modal')"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="button" @click="proceedToCamera()"
                        :disabled="!visitClientName || !visitLocationName || !visitPurpose"
                        class="rounded-lg bg-sky px-4 py-2 text-sm font-bold text-slate-900 shadow-sm transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                        Lanjut — Ambil Foto
                    </button>
                </div>
            </div>

            <div x-show="visitStep === 'camera'" class="space-y-4">
                <div class="relative aspect-video overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800">
                    <video x-show="!visitCapturedPreview" x-ref="visitVideo" class="h-full w-full scale-x-[-1] object-cover" autoplay playsinline muted></video>
                    <img x-show="visitCapturedPreview" :src="visitCapturedPreview" alt="Foto kunjungan" class="h-full w-full scale-x-[-1] object-cover" />
                    <canvas x-ref="visitCanvas" class="hidden"></canvas>

                    <div x-show="!visitStream && !visitLoading && !visitCapturedPreview"
                        class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-slate-500 dark:text-slate-400">
                        <span class="material-icons-round text-4xl text-slate-400">photo_camera</span>
                        <p class="text-sm font-medium">Kamera belum menyala</p>
                    </div>

                    <div x-show="visitLoading" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/50 text-white">
                        <svg class="mb-2 h-8 w-8 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="visitLoadingText" class="text-sm font-medium"></span>
                    </div>

                    <div x-show="visitError"
                        class="absolute bottom-3 left-3 right-3 rounded-xl bg-red-500/90 px-3 py-2 text-center text-xs text-white"
                        x-text="visitError"></div>
                </div>

                <div class="flex gap-3">
                    <button @click="takeVisitPicture()" :disabled="visitLoading || !visitStream"
                        class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-sage px-4 py-3 text-sm font-bold text-slate-900 transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                        <span class="material-icons-round">camera_alt</span>
                        Ambil Foto
                    </button>
                    <button @click="submitVisitCheckin()" :disabled="visitLoading || !visitCapturedBlob"
                        class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-sky px-4 py-3 text-sm font-bold text-slate-900 transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                        <span class="material-icons-round">check</span>
                        Check In
                    </button>
                </div>

                <button type="button" @click="visitStep = 'form'; stopVisitCamera()"
                    class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                    Kembali ke Form
                </button>
            </div>

            <div x-show="visitStep === 'result'" class="space-y-4">
                <div class="rounded-2xl border p-6 text-center"
                    :class="visitResult?.success ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20' : 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20'">
                    <span class="material-icons-round text-5xl"
                        :class="visitResult?.success ? 'text-emerald-500' : 'text-red-500'"
                        x-text="visitResult?.success ? 'check_circle' : 'error'"></span>
                    <p class="mt-3 text-lg font-bold text-slate-900 dark:text-white" x-text="visitResult?.success ? 'Check-in Berhasil!' : 'Check-in Gagal'"></p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300" x-text="visitResult?.message"></p>
                </div>
                <button type="button" @click="closeVisitAndRefresh()"
                    class="w-full rounded-2xl bg-sky px-4 py-3 text-sm font-bold text-slate-900 transition hover:brightness-95">
                    Kembali ke Dashboard
                </button>
            </div>
        </div>
    </x-pastel-modal>

    <!-- VISIT CHECK-OUT MODAL -->
    <x-pastel-modal name="visit-checkout-modal" title="Check Out Kunjungan" maxWidth="xl">
        <div x-data="visitCheckoutHandler()"
            @open-visit-checkout-camera.window="initCheckoutCamera()"
            @modal-closed.window="if ($event.detail === 'visit-checkout-modal') resetCheckoutState()"
            class="rounded-3xl p-2 md:p-4">

            <div x-show="checkoutStep === 'camera'" class="space-y-4">
                <div class="relative aspect-video overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800">
                    <video x-show="!checkoutPreview" x-ref="checkoutVideo" class="h-full w-full scale-x-[-1] object-cover" autoplay playsinline muted></video>
                    <img x-show="checkoutPreview" :src="checkoutPreview" alt="Foto checkout" class="h-full w-full scale-x-[-1] object-cover" />
                    <canvas x-ref="checkoutCanvas" class="hidden"></canvas>

                    <div x-show="!checkoutStream && !checkoutLoading && !checkoutPreview"
                        class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-slate-500 dark:text-slate-400">
                        <span class="material-icons-round text-4xl text-slate-400">photo_camera</span>
                        <p class="text-sm font-medium">Kamera belum menyala</p>
                    </div>

                    <div x-show="checkoutLoading" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/50 text-white">
                        <svg class="mb-2 h-8 w-8 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="checkoutLoadingText" class="text-sm font-medium"></span>
                    </div>

                    <div x-show="checkoutError"
                        class="absolute bottom-3 left-3 right-3 rounded-xl bg-red-500/90 px-3 py-2 text-center text-xs text-white"
                        x-text="checkoutError"></div>
                </div>

                <div class="flex gap-3">
                    <button @click="takeCheckoutPicture()" :disabled="checkoutLoading || !checkoutStream"
                        class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-sage px-4 py-3 text-sm font-bold text-slate-900 transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                        <span class="material-icons-round">camera_alt</span>
                        Ambil Foto
                    </button>
                    <button @click="submitCheckout()" :disabled="checkoutLoading || !checkoutBlob"
                        class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-peach px-4 py-3 text-sm font-bold text-slate-900 transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                        <span class="material-icons-round">logout</span>
                        Check Out
                    </button>
                </div>

                <button type="button" @click="$dispatch('close-modal', 'visit-checkout-modal')"
                    class="w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                    Batal
                </button>
            </div>

            <div x-show="checkoutStep === 'result'" class="space-y-4">
                <div class="rounded-2xl border p-6 text-center"
                    :class="checkoutResult?.success ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20' : 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20'">
                    <span class="material-icons-round text-5xl"
                        :class="checkoutResult?.success ? 'text-emerald-500' : 'text-red-500'"
                        x-text="checkoutResult?.success ? 'check_circle' : 'error'"></span>
                    <p class="mt-3 text-lg font-bold text-slate-900 dark:text-white" x-text="checkoutResult?.success ? 'Check-out Berhasil!' : 'Check-out Gagal'"></p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300" x-text="checkoutResult?.message"></p>
                </div>
                <button type="button" @click="closeCheckoutAndRefresh()"
                    class="w-full rounded-2xl bg-sky px-4 py-3 text-sm font-bold text-slate-900 transition hover:brightness-95">
                    Kembali ke Dashboard
                </button>
            </div>
        </div>
    </x-pastel-modal>

    <x-pastel-modal name="camera-modal" title="Absensi" maxWidth="2xl">
        <div x-data="cameraHandler()"
            @open-camera.window="initCamera($event.detail.type)"
            @modal-closed.window="if ($event.detail === 'camera-modal') resetState()"
            class="glass-attendance rounded-3xl p-2 md:p-4">
            <div class="mb-4 flex items-center justify-between rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400">
                        <span class="material-icons-round">fingerprint</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white" x-text="attendanceType === 'pulang' ? 'Absen Pulang' : 'Absen Masuk'"></h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400"
                            x-text="step === 'result' ? 'Konfirmasi hasil verifikasi absensi.' : 'Pastikan wajah terlihat jelas saat pengambilan foto.'"></p>
                    </div>
                </div>
                <div class="hidden rounded-full bg-slate-100 dark:bg-slate-700 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300 md:block"
                    x-text="new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'"></div>
            </div>

            <div x-show="step === 'capture'">
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                        <div class="space-y-4">
                            <div class="relative aspect-square overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800">
                                <video x-show="!capturedPreview" x-ref="video" class="h-full w-full scale-x-[-1] object-cover" autoplay playsinline muted></video>
                                <img x-show="capturedPreview" :src="capturedPreview" alt="Hasil foto absensi" class="h-full w-full scale-x-[-1] object-cover" />
                                <canvas x-ref="canvas" class="hidden"></canvas>

                                <div x-show="!stream && !loading && !capturedPreview"
                                    class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-slate-500 dark:text-slate-400">
                                    <span class="material-icons-round text-4xl text-slate-400">photo_camera</span>
                                    <p class="text-sm font-medium">Kamera belum menyala</p>
                                    <p class="text-xs">Izinkan akses kamera di browser Anda</p>
                                </div>

                                <div x-show="loading" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/50 text-white">
                                    <svg class="mb-2 h-8 w-8 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="loadingText" class="text-sm font-medium"></span>
                                </div>

                                <div x-show="errorMessage"
                                    class="absolute bottom-3 left-3 right-3 rounded-xl bg-red-500/90 px-3 py-2 text-center text-xs text-white"
                                    x-text="errorMessage"></div>
                            </div>

                            <button @click="takePicture" :disabled="loading || !stream"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-sage px-4 py-3.5 text-sm font-bold text-slate-900 transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                                <span class="material-icons-round">camera_alt</span>
                                Ambil Absensi
                            </button>
                        </div>

                        <div class="flex flex-col gap-4">
                            <div>
                                <label for="kegiatan-modal" class="mb-2 block text-sm font-semibold text-slate-700">Kegiatan Hari Ini</label>
                                <textarea id="kegiatan-modal" x-model="kegiatanText" rows="7"
                                    placeholder="Tuliskan rencana kegiatan atau target Anda hari ini..."
                                    class="w-full resize-none rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 text-sm text-slate-700 dark:text-slate-300 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-sage focus:outline-none focus:ring-2 focus:ring-sage/20"></textarea>
                                <p class="mt-1 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-400">Opsional</p>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/20 p-4">
                                <span class="material-icons-round text-sky-600">info</span>
                                <div class="text-sm">
                                    <p class="font-semibold text-slate-800 dark:text-white">Informasi Penting</p>
                                    <p class="mt-1 text-slate-600 dark:text-slate-300">Pastikan wajah tidak tertutup masker atau objek lain saat mengambil absensi.</p>
                                </div>
                            </div>

                            <div class="mt-auto grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <button type="button" @click="$dispatch('close-modal', 'camera-modal')"
                                    class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    Batal
                                </button>
                                <button type="button" @click="saveAttendance" :disabled="loading || !capturedBlob"
                                    class="flex items-center justify-center gap-2 rounded-2xl bg-sky px-4 py-3 text-sm font-bold text-slate-900 transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                                    <span class="material-icons-round text-base">save</span>
                                    Simpan Kehadiran
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div class="flex items-center gap-3 rounded-2xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-3">
                            <span class="material-icons-round text-amber-600">location_on</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Lokasi</p>
                                <p class="text-xs font-semibold text-slate-700">{{ $officeName }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-violet-200 dark:border-violet-800 bg-violet-50 dark:bg-violet-900/20 p-3">
                            <span class="material-icons-round text-violet-600">verified_user</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-violet-700">Verifikasi</p>
                                <p class="text-xs font-semibold text-slate-700">Biometric Face Recognition</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-3">
                            <span class="material-icons-round text-emerald-600 dark:text-primary">history</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Status Hari Ini</p>
                                <p class="text-xs font-semibold text-slate-700"
                                    x-text="attendanceType === 'pulang' ? 'Proses Absen Pulang' : 'Belum Melakukan Absen Masuk'"></p>
                            </div>
                        </div>
                    </div>
            </div>

            <div x-show="step === 'result'" class="space-y-4">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 p-4">
                        <div class="mx-auto w-full max-w-2xl rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark shadow-sm">
                            <div class="border-b border-slate-200 dark:border-slate-700 px-4 py-3 text-center text-lg font-bold text-slate-800 dark:text-white"
                                x-text="attendanceType === 'pulang' ? 'Absen Pulang' : 'Absen Masuk'"></div>

                            <div class="space-y-4 p-4">
                                <div class="relative h-56 overflow-hidden rounded-lg bg-slate-700">
                                    <img x-show="capturedPreview" :src="capturedPreview" alt="Capture result" class="h-full w-full scale-x-[-1] object-cover opacity-70" />
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="relative h-36 w-36 rounded-2xl border-4"
                                            :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'">
                                            <div class="absolute -left-1 -top-1 h-4 w-4 border-l-4 border-t-4"
                                                :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'"></div>
                                            <div class="absolute -right-1 -top-1 h-4 w-4 border-r-4 border-t-4"
                                                :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'"></div>
                                            <div class="absolute -bottom-1 -left-1 h-4 w-4 border-b-4 border-l-4"
                                                :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'"></div>
                                            <div class="absolute -bottom-1 -right-1 h-4 w-4 border-b-4 border-r-4"
                                                :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'"></div>
                                            <div class="absolute -top-10 left-1/2 -translate-x-1/2 rounded-full px-3 py-1 text-xs font-semibold text-white"
                                                :class="attendanceResult?.success ? 'bg-emerald-500' : 'bg-red-500'">
                                                <span x-text="(attendanceResult?.data?.user_name || '{{ $user->name }}') + (attendanceResult?.success ? ' - Dikenali' : ' - Tidak dikenali')"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 rounded-lg border bg-slate-100 px-4 py-3"
                                    :class="attendanceResult?.success ? 'border-emerald-200' : 'border-red-200'">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full border-2 border-slate-800 bg-white">
                                        <span class="material-icons-round text-lg" x-text="attendanceResult?.success ? 'check' : 'close'"></span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900" x-text="attendanceResult?.success ? 'Wajah dikenali' : 'Wajah tidak dikenali'"></p>
                                        <p class="text-sm text-slate-600">Nama: <span class="font-semibold" x-text="attendanceResult?.data?.user_name || '{{ $user->name }}'"></span></p>
                                    </div>
                                </div>

                                <div class="rounded-lg border border-slate-200 p-3 text-sm text-slate-700">
                                    <div class="flex items-center gap-2">
                                        <span class="material-icons-round text-base">schedule</span>
                                        <span x-text="capturedAt"></span>
                                    </div>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="material-icons-round text-base">location_on</span>
                                        <span x-text="locationStatus"></span>
                                    </div>
                                </div>

                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="flex items-center gap-3 text-sm text-slate-700">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full border border-slate-800">
                                            <span class="material-icons-round text-sm">check</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold" x-text="attendanceType === 'pulang' ? 'Jam Pulang Tercatat' : 'Jam Masuk Tercatat'"></p>
                                            <p x-text="resultStatusLabel()"></p>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="attendanceResult?.success && attendanceType === 'masuk' && attendanceResult?.data?.status_masuk === 'terlambat'"
                                    class="flex items-start gap-2 rounded-lg border border-red-200 bg-white px-3 py-2 text-xs text-red-700">
                                    <span class="material-icons-round text-sm">warning</span>
                                    <p>Absen terlambat. Mohon tingkatkan ketepatan waktu.</p>
                                </div>

                                <div x-show="attendanceResult && !attendanceResult.success"
                                    class="flex items-start gap-2 rounded-lg border border-red-200 bg-white px-3 py-2 text-xs text-red-700">
                                    <span class="material-icons-round text-sm">error</span>
                                    <p x-text="attendanceResult?.message"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <button type="button" @click="closeAndRefresh" x-show="attendanceResult?.success"
                            class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 sm:col-span-2">
                            Kembali ke Dashboard
                        </button>
                        <button type="button" @click="retryCapture" x-show="attendanceResult && !attendanceResult.success"
                            class="rounded-2xl bg-sky px-4 py-3 text-sm font-bold text-slate-900 transition hover:brightness-95">
                            Coba Lagi
                        </button>
                        <button type="button" @click="$dispatch('close-modal', 'camera-modal')" x-show="attendanceResult && !attendanceResult.success"
                            class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            Tutup
                        </button>
                    </div>
            </div>
        </div>
    </x-pastel-modal>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const officeGpsTolerance = {{ $officeGpsTolerance ?? 150 }};

        document.addEventListener('alpine:init', () => {
            Alpine.data('cameraHandler', () => ({
                stream: null,
                loading: false,
                loadingText: 'Menyiapkan kamera...',
                errorMessage: '',
                step: 'capture',
                attendanceType: '',
                capturedBlob: null,
                capturedPreview: '',
                kegiatanText: '',
                attendanceResult: null,
                capturedAt: '',
                locationStatus: '',

                async initCamera(type) {
                    this.attendanceType = type;
                    this.resetState(false);
                    this.step = 'capture';
                    this.loading = true;
                    this.loadingText = 'Menyalakan kamera...';
                    this.errorMessage = '';

                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                        this.$refs.video.srcObject = this.stream;
                        this.loading = false;
                    } catch (err) {
                        this.loading = false;
                        this.errorMessage = 'Gagal akses kamera: ' + err.message;
                    }
                },

                takePicture() {
                    this.loading = true;
                    this.loadingText = 'Mengambil foto...';
                    this.errorMessage = '';

                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;

                    if (!video.videoWidth || !video.videoHeight) {
                        this.loading = false;
                        this.errorMessage = 'Kamera belum siap. Silakan coba lagi.';
                        return;
                    }

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob((blob) => {
                        if (!blob) {
                            this.loading = false;
                            this.errorMessage = 'Gagal mengambil gambar.';
                            return;
                        }

                        this.capturedBlob = blob;
                        if (this.capturedPreview) {
                            URL.revokeObjectURL(this.capturedPreview);
                        }
                        this.capturedPreview = URL.createObjectURL(blob);
                        this.loading = false;
                    }, 'image/jpeg', 0.8);
                },

                generateDeviceFingerprint() {
                    const ua = navigator.userAgent || '';
                    const screen = (window.screen.width || 0) + 'x' + (window.screen.height || 0);
                    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
                    const lang = navigator.language || '';
                    const raw = ua + '|' + screen + '|' + tz + '|' + lang;
                    let hash = 0;
                    for (let i = 0; i < raw.length; i++) {
                        const chr = raw.charCodeAt(i);
                        hash = ((hash << 5) - hash) + chr;
                        hash |= 0;
                    }
                    return Math.abs(hash).toString(36);
                },

                getGpsReading() {
                    return new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(
                            (position) => resolve({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                                accuracy: position.coords.accuracy,
                                mock: position.coords.mock || false,
                            }),
                            (err) => reject(err),
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    });
                },

                async collectGpsReadings() {
                    const readings = [];
                    for (let i = 0; i < 3; i++) {
                        this.loadingText = `Mengambil lokasi (${i + 1}/3)...`;
                        try {
                            const reading = await this.getGpsReading();
                            readings.push(reading);
                        } catch (err) {
                            throw new Error('Gagal dapat lokasi: ' + err.message);
                        }
                        if (i < 2) {
                            await new Promise(r => setTimeout(r, 1000));
                        }
                    }
                    return readings;
                },

                saveAttendance() {
                    if (!this.capturedBlob) {
                        this.errorMessage = 'Ambil foto absensi terlebih dahulu.';
                        return;
                    }

                    this.loading = true;
                    this.loadingText = 'Mengambil lokasi (1/3)...';
                    this.errorMessage = '';

                    if (!navigator.geolocation) {
                        this.loading = false;
                        this.errorMessage = 'Browser tidak support Geolocation.';
                        return;
                    }

                    this.collectGpsReadings().then(async (readings) => {
                        const avgLat = readings.reduce((s, r) => s + r.latitude, 0) / readings.length;
                        const avgLong = readings.reduce((s, r) => s + r.longitude, 0) / readings.length;
                        const avgAccuracy = readings.reduce((s, r) => s + r.accuracy, 0) / readings.length;
                        const mockDetected = readings.some(r => r.mock);

                        if (avgAccuracy > officeGpsTolerance) {
                            this.loading = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Lokasi Tidak Akurat',
                                html: `Akurasi GPS perangkat Anda: <strong>${Math.round(avgAccuracy)} meter</strong>.<br>` +
                                      `Batas toleransi: <strong>${officeGpsTolerance} meter</strong>.<br><br>` +
                                      `Sistem mendeteksi lokasi dari internet/provider, bukan GPS asli.<br>` +
                                      `<strong>Solusi:</strong> Aktifkan GPS (High Accuracy) di HP dan pastikan berada di area terbuka.`,
                                confirmButtonText: 'Mengerti',
                                confirmButtonColor: '#64748b'
                            });
                            return;
                        }

                        this.capturedAt = new Date().toLocaleString('id-ID', {
                            weekday: 'long',
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) + ' WIB';
                        this.locationStatus = `Lokasi terekam (${avgLat.toFixed(5)}, ${avgLong.toFixed(5)})`;

                        const deviceFingerprint = this.generateDeviceFingerprint();
                        const timezoneClient = Intl.DateTimeFormat().resolvedOptions().timeZone || '';

                        await this.submitAttendance(
                            this.capturedBlob, avgLat, avgLong, avgAccuracy,
                            readings, deviceFingerprint, timezoneClient, mockDetected
                        );
                    }).catch((err) => {
                        this.loading = false;
                        this.errorMessage = err.message || 'Gagal dapat lokasi.';
                    });
                },

                async submitAttendance(photoBlob, lat, long, accuracy, gpsReadings, deviceFingerprint, timezoneClient, mockDetected) {
                    this.loadingText = 'Memproses Absensi...';

                    const formData = new FormData();
                    formData.append('photo', photoBlob, 'selfie.jpg');
                    formData.append('type', this.attendanceType);
                    formData.append('latitude', lat);
                    formData.append('longitude', long);
                    formData.append('accuracy', accuracy);
                    formData.append('gps_readings', JSON.stringify(gpsReadings || []));
                    formData.append('device_fingerprint', deviceFingerprint || '');
                    formData.append('timezone_client', timezoneClient || '');
                    formData.append('mock_location_detected', mockDetected ? '1' : '0');

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;

                        const response = await fetch('/api/attendance/auto', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                Accept: 'application/json'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        this.loading = false;
                        this.stopCamera();
                        this.step = 'result';
                        this.attendanceResult = {
                            success: response.ok,
                            message: result?.message || 'Terjadi kesalahan sistem.',
                            data: result?.data || {}
                        };

                        if (!response.ok) {
                            this.errorMessage = '';
                        }
                    } catch (error) {
                        this.loading = false;
                        this.stopCamera();
                        this.step = 'result';
                        this.attendanceResult = {
                            success: false,
                            message: error.message || 'Terjadi kesalahan jaringan.',
                            data: {}
                        };
                        this.errorMessage = '';
                    }
                },

                resultStatusLabel() {
                    if (!this.attendanceResult) {
                        return '-';
                    }

                    if (!this.attendanceResult.success) {
                        return 'Status: Gagal diverifikasi';
                    }

                    if (this.attendanceType === 'pulang') {
                        const pulang = this.attendanceResult.data?.status_pulang;
                        if (pulang === 'lembur') {
                            return 'Status: Lembur';
                        }

                        return 'Status: Pulang tercatat';
                    }

                    const masuk = this.attendanceResult.data?.status_masuk;
                    if (masuk === 'tepat_waktu') {
                        return 'Status: Tepat waktu';
                    }
                    if (masuk === 'terlambat') {
                        return 'Status: Terlambat';
                    }

                    return 'Status: Berhasil tercatat';
                },

                closeAndRefresh() {
                    this.$dispatch('close-modal', 'camera-modal');
                    setTimeout(() => {
                        window.location.reload();
                    }, 150);
                },

                async retryCapture() {
                    await this.initCamera(this.attendanceType || 'masuk');
                },

                resetState(stopStream = true) {
                    this.loading = false;
                    this.loadingText = 'Menyiapkan kamera...';
                    this.errorMessage = '';
                    this.step = 'capture';
                    this.capturedBlob = null;
                    if (this.capturedPreview) {
                        URL.revokeObjectURL(this.capturedPreview);
                    }
                    this.capturedPreview = '';
                    this.kegiatanText = '';
                    this.attendanceResult = null;
                    this.capturedAt = '';
                    this.locationStatus = '';
                    if (stopStream) {
                        this.stopCamera();
                    }
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                }
            }));

        // Visit Check-in Handler
        Alpine.data('visitCheckinHandler', () => ({
            visitStep: 'form',
            visitClientName: '',
            visitLocationName: '',
            visitPurpose: '',
            visitStream: null,
            visitLoading: false,
            visitLoadingText: '',
            visitError: '',
            visitCapturedBlob: null,
            visitCapturedPreview: '',
            visitResult: null,

            proceedToCamera() {
                if (!this.visitClientName || !this.visitLocationName || !this.visitPurpose) return;
                this.visitStep = 'camera';
                this.$nextTick(() => this.initVisitCamera());
            },

            async initVisitCamera() {
                this.visitLoading = true;
                this.visitLoadingText = 'Menyalakan kamera...';
                this.visitError = '';
                try {
                    this.visitStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                    this.$refs.visitVideo.srcObject = this.visitStream;
                    this.visitLoading = false;
                } catch (err) {
                    this.visitLoading = false;
                    this.visitError = 'Gagal akses kamera: ' + err.message;
                }
            },

            takeVisitPicture() {
                this.visitLoading = true;
                this.visitLoadingText = 'Mengambil foto...';
                const video = this.$refs.visitVideo;
                const canvas = this.$refs.visitCanvas;
                if (!video.videoWidth || !video.videoHeight) {
                    this.visitLoading = false;
                    this.visitError = 'Kamera belum siap.';
                    return;
                }
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob((blob) => {
                    if (!blob) { this.visitLoading = false; this.visitError = 'Gagal mengambil gambar.'; return; }
                    this.visitCapturedBlob = blob;
                    if (this.visitCapturedPreview) URL.revokeObjectURL(this.visitCapturedPreview);
                    this.visitCapturedPreview = URL.createObjectURL(blob);
                    this.visitLoading = false;
                }, 'image/jpeg', 0.8);
            },

            getGpsReading() {
                return new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => resolve({ latitude: pos.coords.latitude, longitude: pos.coords.longitude, accuracy: pos.coords.accuracy }),
                        (err) => reject(err),
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                });
            },

            generateDeviceFingerprint() {
                const raw = (navigator.userAgent || '') + '|' + (window.screen.width || 0) + 'x' + (window.screen.height || 0) + '|' + (Intl.DateTimeFormat().resolvedOptions().timeZone || '') + '|' + (navigator.language || '');
                let hash = 0;
                for (let i = 0; i < raw.length; i++) { hash = ((hash << 5) - hash) + raw.charCodeAt(i); hash |= 0; }
                return Math.abs(hash).toString(36);
            },

            async submitVisitCheckin() {
                if (!this.visitCapturedBlob) { this.visitError = 'Ambil foto terlebih dahulu.'; return; }
                this.visitLoading = true;
                this.visitError = '';

                try {
                    const readings = [];
                    for (let i = 0; i < 3; i++) {
                        this.visitLoadingText = `Mengambil lokasi (${i + 1}/3)...`;
                        readings.push(await this.getGpsReading());
                        if (i < 2) await new Promise(r => setTimeout(r, 1000));
                    }

                    const avgLat = readings.reduce((s, r) => s + r.latitude, 0) / readings.length;
                    const avgLong = readings.reduce((s, r) => s + r.longitude, 0) / readings.length;
                    const avgAccuracy = readings.reduce((s, r) => s + r.accuracy, 0) / readings.length;

                    this.visitLoadingText = 'Memproses check-in...';

                    const formData = new FormData();
                    formData.append('photo', this.visitCapturedBlob, 'visit-selfie.jpg');
                    formData.append('latitude', avgLat);
                    formData.append('longitude', avgLong);
                    formData.append('accuracy', avgAccuracy);
                    formData.append('client_name', this.visitClientName);
                    formData.append('location_name', this.visitLocationName);
                    formData.append('purpose', this.visitPurpose);
                    formData.append('device_fingerprint', this.generateDeviceFingerprint());
                    formData.append('gps_readings', JSON.stringify(readings));
                    formData.append('timezone_client', Intl.DateTimeFormat().resolvedOptions().timeZone || '');

                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const response = await fetch('/api/visit/check-in', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' },
                        body: formData
                    });
                    const result = await response.json();

                    this.visitLoading = false;
                    this.stopVisitCamera();
                    this.visitStep = 'result';
                    this.visitResult = { success: response.ok, message: result?.message || 'Terjadi kesalahan.' };
                } catch (err) {
                    this.visitLoading = false;
                    this.visitError = err.message || 'Gagal memproses check-in.';
                }
            },

            stopVisitCamera() {
                if (this.visitStream) { this.visitStream.getTracks().forEach(t => t.stop()); this.visitStream = null; }
            },

            resetVisitState() {
                this.visitStep = 'form';
                this.visitLoading = false;
                this.visitError = '';
                this.visitCapturedBlob = null;
                if (this.visitCapturedPreview) URL.revokeObjectURL(this.visitCapturedPreview);
                this.visitCapturedPreview = '';
                this.visitResult = null;
                this.stopVisitCamera();
            },

            closeVisitAndRefresh() {
                this.$dispatch('close-modal', 'visit-checkin-modal');
                setTimeout(() => window.location.reload(), 150);
            }
        }));

        // Visit Check-out Handler
        Alpine.data('visitCheckoutHandler', () => ({
            checkoutStep: 'camera',
            checkoutStream: null,
            checkoutLoading: false,
            checkoutLoadingText: '',
            checkoutError: '',
            checkoutBlob: null,
            checkoutPreview: '',
            checkoutResult: null,

            async initCheckoutCamera() {
                this.checkoutLoading = true;
                this.checkoutLoadingText = 'Menyalakan kamera...';
                this.checkoutError = '';
                try {
                    this.checkoutStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                    this.$refs.checkoutVideo.srcObject = this.checkoutStream;
                    this.checkoutLoading = false;
                } catch (err) {
                    this.checkoutLoading = false;
                    this.checkoutError = 'Gagal akses kamera: ' + err.message;
                }
            },

            takeCheckoutPicture() {
                this.checkoutLoading = true;
                this.checkoutLoadingText = 'Mengambil foto...';
                const video = this.$refs.checkoutVideo;
                const canvas = this.$refs.checkoutCanvas;
                if (!video.videoWidth || !video.videoHeight) {
                    this.checkoutLoading = false;
                    this.checkoutError = 'Kamera belum siap.';
                    return;
                }
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob((blob) => {
                    if (!blob) { this.checkoutLoading = false; this.checkoutError = 'Gagal mengambil gambar.'; return; }
                    this.checkoutBlob = blob;
                    if (this.checkoutPreview) URL.revokeObjectURL(this.checkoutPreview);
                    this.checkoutPreview = URL.createObjectURL(blob);
                    this.checkoutLoading = false;
                }, 'image/jpeg', 0.8);
            },

            async submitCheckout() {
                if (!this.checkoutBlob) { this.checkoutError = 'Ambil foto terlebih dahulu.'; return; }
                this.checkoutLoading = true;
                this.checkoutError = '';

                try {
                    this.checkoutLoadingText = 'Mengambil lokasi...';
                    const pos = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                    });

                    this.checkoutLoadingText = 'Memproses check-out...';
                    const visitId = window.__activeVisitId;
                    const formData = new FormData();
                    formData.append('photo', this.checkoutBlob, 'checkout-selfie.jpg');
                    formData.append('latitude', pos.coords.latitude);
                    formData.append('longitude', pos.coords.longitude);

                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const response = await fetch(`/api/visit/${visitId}/check-out`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' },
                        body: formData
                    });
                    const result = await response.json();

                    this.checkoutLoading = false;
                    this.stopCheckoutCamera();
                    this.checkoutStep = 'result';
                    this.checkoutResult = { success: response.ok, message: result?.message || 'Terjadi kesalahan.' };
                } catch (err) {
                    this.checkoutLoading = false;
                    this.checkoutError = err.message || 'Gagal memproses check-out.';
                }
            },

            stopCheckoutCamera() {
                if (this.checkoutStream) { this.checkoutStream.getTracks().forEach(t => t.stop()); this.checkoutStream = null; }
            },

            resetCheckoutState() {
                this.checkoutStep = 'camera';
                this.checkoutLoading = false;
                this.checkoutError = '';
                this.checkoutBlob = null;
                if (this.checkoutPreview) URL.revokeObjectURL(this.checkoutPreview);
                this.checkoutPreview = '';
                this.checkoutResult = null;
                this.stopCheckoutCamera();
            },

            closeCheckoutAndRefresh() {
                this.$dispatch('close-modal', 'visit-checkout-modal');
                setTimeout(() => window.location.reload(), 150);
            }
        }));

        // Visit GPS Tracker (background tracking every 5 minutes)
        Alpine.data('visitTracker', (visitId) => ({
            trackingStatus: 'Pelacakan GPS aktif',
            trackingInterval: null,

            init() {
                window.__activeVisitId = visitId;
                this.startTracking();
            },

            startTracking() {
                this.sendLocation();
                this.trackingInterval = setInterval(() => this.sendLocation(), 5 * 60 * 1000);
            },

            async sendLocation() {
                try {
                    const pos = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                    });
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    await fetch(`/api/visit/${visitId}/track`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ latitude: pos.coords.latitude, longitude: pos.coords.longitude, accuracy: pos.coords.accuracy })
                    });
                    this.trackingStatus = 'Lokasi terakhir: ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                } catch (err) {
                    this.trackingStatus = 'Gagal kirim lokasi';
                }
            },

            startVisitCheckout() {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'visit-checkout-modal' }));
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('open-visit-checkout-camera'));
                }, 100);
            },

            destroy() {
                if (this.trackingInterval) clearInterval(this.trackingInterval);
            }
        }));
        });

        function startAttendance(type) {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'camera-modal' }));
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-camera', { detail: { type: type } }));
            }, 100);
        }
    </script>
@endsection
