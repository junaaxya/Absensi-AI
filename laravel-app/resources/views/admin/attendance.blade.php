<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Sistem Absensi - Admin Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#C8D5B9", // Sage
                        sage: "#C8D5B9",
                        sky: "#B8D4E3",
                        peach: "#F5D5CB",
                        lavender: "#D4C5E2",
                        "background-light": "#F9FAFB",
                        "background-dark": "#111827",
                    },
                    fontFamily: {
                        display: ["Plus Jakarta Sans", "sans-serif"],
                        sans: ["Plus Jakarta Sans", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "12px",
                        xl: "16px",
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100 transition-colors duration-200">
    <div class="flex min-h-screen overflow-hidden">
        <!-- SIDEBAR -->
        <aside class="w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col h-screen sticky top-0 hidden md:flex">
            <div class="p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-lavender flex items-center justify-center overflow-hidden border-2 border-white dark:border-slate-700 shadow-sm">
                    @if(Auth::user()->foto)
                        <img alt="Profile Avatar" class="w-full h-full object-cover" src="{{ asset('storage/' . Auth::user()->foto) }}"/>
                    @else
                        <span class="material-icons-round text-slate-600">person</span>
                    @endif
                </div>
                <div>
                    <h2 class="font-bold text-sm text-slate-900 dark:text-white leading-tight uppercase">{{ Auth::user()->name }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Admin Portal</p>
                </div>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto scrollbar-hide">
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all" href="{{ route('admin.dashboard') }}">
                    <span class="material-icons-round text-[20px]">dashboard</span>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 bg-lavender text-slate-800 dark:text-slate-900 rounded-xl transition-all shadow-sm" href="{{ route('admin.attendance') }}">
                    <span class="material-icons-round text-[20px]">fact_check</span>
                    <span class="font-bold">Absensi</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all" href="{{ route('admin.absence.index') }}">
                    <span class="material-icons-round text-[20px]">event_busy</span>
                    <span class="font-medium text-sm">Manajemen Ketidakhadiran</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all" href="{{ route('employees.index') }}">
                    <span class="material-icons-round text-[20px]">badge</span>
                    <span class="font-medium text-sm">Manajemen Karyawan</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all" href="{{ route('admin.settings.index') }}">
                    <span class="material-icons-round text-[20px]">settings</span>
                    <span class="font-medium">Pengaturan Sistem</span>
                </a>
            </nav>
            <div class="p-4 mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                        <span class="material-icons-round text-[20px]">logout</span>
                        <span class="font-bold">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-4 sm:p-8">
            <header class="flex justify-between items-center mb-10">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-slate-900 dark:bg-white rounded-full flex items-center justify-center">
                        <span class="material-icons-round text-white dark:text-slate-900">fingerprint</span>
                    </div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">Sistem Absensi</h1>
                </div>
                <div class="flex items-center gap-4">
                    <button class="p-2 bg-white dark:bg-slate-800 rounded-full border border-slate-200 dark:border-slate-700 shadow-sm text-slate-600 dark:text-slate-400" onclick="document.documentElement.classList.toggle('dark')">
                        <span class="material-icons-round">dark_mode</span>
                    </button>
                </div>
            </header>

            <div class="mb-8 p-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Data Absensi</h2>
                <p class="text-slate-500 dark:text-slate-400">Menampilkan Rekap & Data Absensi Masuk Seluruh Karyawan</p>
            </div>

            <!-- FILTERS -->
            <form method="GET" action="{{ route('admin.attendance') }}" class="mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="md:col-span-3 relative">
                    <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input name="q" value="{{ request('q') }}" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" placeholder="Cari karyawan..." type="text"/>
                </div>
                <div class="md:col-span-2">
                    <select name="status" class="w-full py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="dinas" {{ request('status') == 'dinas' ? 'selected' : '' }}>Dinas</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <div class="flex-1 relative">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">calendar_today</span>
                        <input name="start_date" value="{{ request('start_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" type="date"/>
                    </div>
                    <span class="text-slate-400">/</span>
                    <div class="flex-1 relative">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">calendar_today</span>
                        <input name="end_date" value="{{ request('end_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" type="date"/>
                    </div>
                </div>
                <div class="md:col-span-3 flex justify-end gap-2">
                    <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-all shadow-sm active:scale-95">
                        <span class="material-icons-round">filter_alt</span>
                        Filter
                    </button>
                    <!-- TODO: Implement Export -->
                    <button type="button" class="bg-sage hover:bg-[#b8c6a7] text-slate-800 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-all shadow-md active:scale-95 opacity-50 cursor-not-allowed">
                        <span class="material-icons-round">file_download</span>
                        Export
                    </button>
                </div>
            </form>

            <!-- TABLE -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Masuk</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Keluar</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Kerja</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            {{-- 1. RENDER IZIN ROWS (PINNED TOP) --}}
                            @foreach($izins as $izin)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors bg-orange-50/50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-500 dark:text-slate-400">-</td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">{{ $izin->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }}
                                    @if($izin->tanggal_mulai != $izin->tanggal_selesai)
                                        - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-400">-</td>
                                <td class="px-6 py-4 text-sm text-slate-400">-</td>
                                <td class="px-6 py-4 text-sm text-slate-400">-</td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        <span class="inline-flex items-center gap-1.5 bg-lavender text-slate-800 px-4 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                            <span class="material-icons-round text-sm">history_edu</span>
                                            {{ ucfirst($izin->jenis) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300 italic font-medium">{{ $izin->alasan }}</td>
                            </tr>
                            @endforeach

                            {{-- 2. RENDER ATTENDANCE ROWS --}}
                            @forelse($attendances as $index => $row)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                                    {{ $attendances->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">{{ $row->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                
                                {{-- Jam Masuk --}}
                                <td class="px-6 py-4">
                                    @if($row->jam_masuk)
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') }} WIB</span>
                                            @if($row->status === 'terlambat')
                                                <span class="inline-block mt-1 px-2 py-0.5 bg-peach text-slate-800 text-[10px] font-bold rounded uppercase tracking-tighter w-fit">Terlambat</span>
                                            @endif
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Jam Keluar --}}
                                <td class="px-6 py-4">
                                    @if($row->jam_keluar)
                                        <div class="flex flex-col">
                                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') }} WIB</span>
                                            @if($row->kegiatan === 'hadir_lembur')
                                                <span class="inline-block mt-1 px-2 py-0.5 bg-sky text-slate-800 text-[10px] font-bold rounded uppercase tracking-tighter w-fit">Lembur</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">-</span>
                                    @endif
                                </td>

                                {{-- Jam Kerja --}}
                                <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">
                                    @if($row->jam_masuk && $row->jam_keluar)
                                        @php
                                            $start = \Carbon\Carbon::parse($row->jam_masuk);
                                            $end = \Carbon\Carbon::parse($row->jam_keluar);
                                            if ($end->lessThan($start)) $end->addDay();
                                            $diff = $start->diff($end);
                                        @endphp
                                        {{ $diff->h }} Jam {{ $diff->i }} menit
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        <span class="inline-flex items-center gap-1.5 bg-sage text-slate-800 px-4 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                            <span class="material-icons-round text-sm">check_circle</span>
                                            Hadir
                                        </span>
                                    </div>
                                </td>

                                {{-- Keterangan --}}
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">-</td>
                            </tr>
                            @empty
                                @if($izins->isEmpty())
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-slate-500 italic">
                                        Tidak ada data absensi untuk periode ini.
                                    </td>
                                </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="p-6">
                    {{ $attendances->links() }}
                </div>
            </div>
        </main>
    </div>
</body>
</html>
