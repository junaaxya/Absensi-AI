@extends('layouts.admin')

@section('header-title', 'Dashboard')
@section('header-subtitle', $today->translatedFormat('l, d F Y'))

@push('styles')
    <style>
        .pie-chart {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: conic-gradient(var(--sage) 0%
                    {{ $persentaseHadir }}
                    %,
                    var(--peach)
                    {{ $persentaseHadir }}
                    %
                    {{ $persentaseHadir + $persentaseTerlambat }}
                    %,
                    var(--lavender)
                    {{ $persentaseHadir + $persentaseTerlambat }}
                    %
                    {{ $persentaseHadir + $persentaseTerlambat + $persentaseIzin }}
                    %,
                    #Fecdd3
                    {{ $persentaseHadir + $persentaseTerlambat + $persentaseIzin }}
                    % 100%);
        }
    </style>
@endpush

@section('content')

    <section class="mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-1">Selamat Datang, Admin</h2>
        <p class="text-slate-500 dark:text-slate-400">Ringkasan Absensi Perangkat Desa Hari Ini</p>
    </section>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Karyawan -->
        <div
            class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-slate-400">groups</span>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Total Karyawan</h3>
                </div>
                <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ $totalKaryawan }}</p>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div
            class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-sage">check_circle</span>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Hadir Hari Ini</h3>
                </div>
                <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ $totalHadir }}</p>
            </div>
        </div>

        <!-- Terlambat -->
        <div
            class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-peach">schedule</span>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Terlambat</h3>
                </div>
                <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ $hadirTerlambat }}</p>
            </div>
        </div>

        <!-- Alpha -->
        <div
            class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-rose-400">close</span>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Alpha</h3>
                </div>
                <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ $alpha }}</p>
            </div>
        </div>
    </div>

    <!-- Detail Izin/Sakit/Dinas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div
            class="bg-white dark:bg-card-dark p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-sky/20 flex items-center justify-center">
                <span class="material-icons-round text-sky dark:text-sky-dark">assignment</span>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Izin</h4>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $izin }}</p>
            </div>
        </div>
        <div
            class="bg-white dark:bg-card-dark p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-peach/20 flex items-center justify-center">
                <span class="material-icons-round text-peach">medical_services</span>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sakit</h4>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $sakit }}</p>
            </div>
        </div>
        <div
            class="bg-white dark:bg-card-dark p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-lavender/20 flex items-center justify-center">
                <span class="material-icons-round text-lavender">business_center</span>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider leading-tight">Dinas Luar</h4>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $dinas }}</p>
            </div>
        </div>
        <div
            class="bg-white dark:bg-card-dark p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <span class="material-icons-round text-slate-400">event_busy</span>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cuti</h4>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $cuti }}</p>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart Section -->
        <div
            class="lg:col-span-2 bg-white dark:bg-card-dark p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold mb-8 text-slate-900 dark:text-white">Ringkasan Absen Hari Ini</h3>
            <div class="flex flex-col md:flex-row items-center justify-center gap-12">
                <div class="relative flex-shrink-0">
                    <div class="pie-chart"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div
                            class="bg-white dark:bg-card-dark w-28 h-28 rounded-full shadow-inner flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $persentaseHadir }}%</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Hadir</span>
                        </div>
                    </div>
                </div>
                <div class="flex-grow w-full max-w-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-sage"></div>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $persentaseHadir }}%
                                Hadir</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-peach"></div>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $persentaseTerlambat }}%
                                Terlambat</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-lavender"></div>
                            <span class="text-sm font-medium text-slate-400">{{ $persentaseIzin }}% Izin, Sakit,
                                Dinas</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-rose-200"></div>
                            <span class="text-sm font-medium text-slate-400">{{ $persentaseAlpha }}% Alpha</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Widgets -->
        <div class="flex flex-col gap-6">
            <div
                class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 relative">
                <div class="flex items-center gap-4 mb-2">
                    <div
                        class="w-12 h-12 rounded-xl bg-peach/20 flex items-center justify-center text-slate-800 dark:text-slate-200">
                        <span class="material-icons-round text-3xl">hourglass_empty</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white leading-tight">Pengajuan Ketidakhadiran</h3>
                        <p class="text-xs text-slate-400">Menunggu Persetujuan</p>
                    </div>
                </div>
                @if($pendingRequest > 0)
                    <div
                        class="absolute top-4 right-4 w-6 h-6 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-full flex items-center justify-center text-[10px] font-bold">
                        {{ $pendingRequest }}
                    </div>
                @endif
                <button onclick="window.location='{{ route('admin.absence.index') }}'"
                    class="mt-6 w-full py-3 bg-sage hover:brightness-95 transition-all rounded-xl font-bold text-sm text-slate-900">
                    Lihat Pengajuan
                </button>
            </div>

            <div class="bg-sky p-6 rounded-2xl shadow-sm border border-sky">
                <p class="text-sm text-slate-700 italic">
                    "Sistem diperbarui secara real-time berdasarkan input perangkat desa di lapangan."
                </p>
            </div>
        </div>
    </div>
@endsection