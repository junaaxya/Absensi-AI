<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Attendance Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <style type="text/tailwindcss">
        :root {
            --sage: #C8D5B9;
            --sky: #B8D4E3;
            --peach: #F5D5CB;
            --lavender: #D4C5E2;
            --bg-neutral: #F9FBFA;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-neutral);
        }
        .pie-chart {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: conic-gradient(
                var(--sage) 0% {{ $persentaseHadir }}%,
                var(--peach) {{ $persentaseHadir }}% {{ $persentaseHadir + $persentaseTerlambat }}%,
                var(--lavender) {{ $persentaseHadir + $persentaseTerlambat }}% {{ $persentaseHadir + $persentaseTerlambat + $persentaseIzin }}%,
                #Fecdd3 {{ $persentaseHadir + $persentaseTerlambat + $persentaseIzin }}% 100%
            );
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="flex min-h-screen text-slate-800">
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col h-screen sticky top-0 hidden md:flex">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 rounded-full bg-[var(--lavender)] flex items-center justify-center overflow-hidden">
                    @if(Auth::user()->foto)
                        <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Admin" class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-slate-600">person</span>
                    @endif
                </div>
                <div>
                    <h2 class="text-sm font-bold leading-tight uppercase">{{ Auth::user()->name }}</h2>
                    <p class="text-xs text-slate-500">Admin</p>
                </div>
            </div>
            <nav class="space-y-1">
                <a class="flex items-center gap-3 px-4 py-3 bg-[var(--sage)] text-slate-800 rounded-xl font-medium transition-colors" href="{{ route('admin.dashboard') }}">
                    <span class="material-symbols-outlined">dashboard</span>
                    Dashboard
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl transition-colors" href="{{ route('admin.attendance') }}">
                    <span class="material-symbols-outlined">calendar_today</span>
                    Absensi
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl transition-colors" href="{{ route('admin.absence.index') }}">
                    <span class="material-symbols-outlined">hourglass_empty</span>
                    Manajemen Ketidakhadiran
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl transition-colors" href="{{ route('employees.index') }}">
                    <span class="material-symbols-outlined">badge</span>
                    Manajemen Karyawan
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl transition-colors" href="{{ route('admin.settings.index') }}">
                    <span class="material-symbols-outlined">settings</span>
                    Pengaturan Sistem
                </a>
            </nav>
        </div>
        <div class="mt-auto p-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-rose-500 hover:bg-rose-50 rounded-xl transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center px-4 sm:px-8 justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center">
                    <div class="w-4 h-4 rounded-full border-2 border-white"></div>
                </div>
                <h1 class="text-xl font-bold tracking-tight">Sistem Absensi</h1>
            </div>
            
            <!-- Mobile Menu Button (Simple implementation) -->
            <div class="md:hidden">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-rose-500 font-medium text-sm">Logout</button>
                </form>
            </div>
        </header>

        <div class="p-4 sm:p-8 max-w-7xl mx-auto w-full">
            <section class="mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-1">Selamat Datang, Admin</h2>
                <p class="text-slate-500">Ringkasan Absensi Perangkat Desa Hari Ini ({{ $today->translatedFormat('l, d F Y') }})</p>
            </section>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Karyawan -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-slate-400">groups</span>
                            <h3 class="text-sm font-semibold text-slate-500">Total Karyawan</h3>
                        </div>
                        <p class="text-4xl font-bold">{{ $totalKaryawan }}</p>
                    </div>
                </div>

                <!-- Hadir Hari Ini -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-[var(--sage)]">check_circle</span>
                            <h3 class="text-sm font-semibold text-slate-500">Hadir Hari Ini</h3>
                        </div>
                        <p class="text-4xl font-bold">{{ $totalHadir }}</p>
                    </div>
                </div>

                <!-- Terlambat -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-[var(--peach)]">schedule</span>
                            <h3 class="text-sm font-semibold text-slate-500">Terlambat</h3>
                        </div>
                        <p class="text-4xl font-bold">{{ $hadirTerlambat }}</p>
                    </div>
                </div>

                <!-- Alpha -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-rose-400">close</span>
                            <h3 class="text-sm font-semibold text-slate-500">Alpha</h3>
                        </div>
                        <p class="text-4xl font-bold">{{ $alpha }}</p>
                    </div>
                </div>
            </div>

            <!-- Detail Izin/Sakit/Dinas -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-[var(--sky)]/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[var(--sky)]">assignment</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Izin</h4>
                        <p class="text-xl font-bold">{{ $izin }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-[var(--peach)]/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[var(--peach)]">medical_services</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sakit</h4>
                        <p class="text-xl font-bold">{{ $sakit }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-[var(--lavender)]/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[var(--lavender)]">business_center</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider leading-tight">Dinas Luar</h4>
                        <p class="text-xl font-bold">{{ $dinas }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-slate-400">event_busy</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cuti</h4>
                        <p class="text-xl font-bold">{{ $cuti }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-slate-200 mb-8 shadow-sm">
                <span class="material-symbols-outlined text-slate-400">calendar_month</span>
                <span class="text-sm font-medium">Hari ini: <span class="text-slate-900">{{ $today->translatedFormat('l, d F Y') }}</span></span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Chart Section -->
                <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <h3 class="text-lg font-bold mb-8">Ringkasan Absen Hari Ini</h3>
                    <div class="flex flex-col md:flex-row items-center justify-center gap-12">
                        <div class="relative flex-shrink-0">
                            <div class="pie-chart"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="bg-white w-28 h-28 rounded-full shadow-inner flex flex-col items-center justify-center">
                                    <span class="text-2xl font-bold">{{ $persentaseHadir }}%</span>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold">Hadir</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow w-full max-w-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full bg-[var(--sage)]"></div>
                                    <span class="text-sm font-medium">{{ $persentaseHadir }}% Hadir</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full bg-[var(--peach)]"></div>
                                    <span class="text-sm font-medium">{{ $persentaseTerlambat }}% Terlambat</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full bg-[var(--lavender)]"></div>
                                    <span class="text-sm font-medium text-slate-400">{{ $persentaseIzin }}% Izin, Sakit, Dinas Luar</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
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
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-[var(--peach)]/20 flex items-center justify-center text-slate-800">
                                <span class="material-symbols-outlined text-3xl">hourglass_empty</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 leading-tight">Pengajuan Ketidakhadiran</h3>
                                <p class="text-xs text-slate-400">Menunggu Persetujuan</p>
                            </div>
                        </div>
                        @if($pendingRequest > 0)
                        <div class="absolute top-4 right-4 w-6 h-6 bg-slate-900 text-white rounded-full flex items-center justify-center text-[10px] font-bold">
                            {{ $pendingRequest }}
                        </div>
                        @endif
                        <button class="mt-6 w-full py-3 bg-[var(--sage)] hover:opacity-90 transition-all rounded-xl font-bold text-sm">
                            Lihat Pengajuan
                        </button>
                    </div>
                    <div class="bg-[var(--sky)] p-6 rounded-2xl shadow-sm border border-[var(--sky)]">
                        <p class="text-sm text-slate-700 italic">
                            "Sistem diperbarui secara real-time berdasarkan input perangkat desa di lapangan."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
