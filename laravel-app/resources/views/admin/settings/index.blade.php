<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Pengaturan Sistem - Pastel Modern</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style type="text/tailwindcss">
        :root {
            --sage: #C8D5B9;
            --lavender: #D4C5E2;
            --sky: #B8D4E3;
            --peach: #F5D5CB;
            --sidebar-neutral: #F9F9FB;
        }
        @layer base {
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        [x-cloak] { display: none !important; }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#C8D5B9",
                        secondary: "#D4C5E2",
                        accent: "#B8D4E3",
                        warning: "#F5D5CB",
                        sidebar: "#F9F9FB",
                    },
                    borderRadius: {
                        DEFAULT: "12px",
                        'xl': "16px",
                        '2xl': "24px",
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex" x-data="{ activeTab: 'jam_kerja' }">
    
    <!-- SIDEBAR -->
    <aside class="w-72 bg-[var(--sidebar-neutral)] border-r border-slate-200/60 flex flex-col shrink-0">
        <div class="p-8 flex flex-col items-center border-b border-slate-200/40">
            <div class="relative mb-4">
                <div class="w-20 h-20 rounded-full bg-white shadow-sm flex items-center justify-center overflow-hidden border-4 border-white">
                    @if(Auth::user()->foto)
                        <img alt="User Profile" class="w-full h-full object-cover" src="{{ asset('storage/' . Auth::user()->foto) }}"/>
                    @else
                        <span class="material-symbols-outlined text-4xl text-slate-300">person</span>
                    @endif
                </div>
                <div class="absolute bottom-1 right-1 w-4 h-4 bg-green-400 border-2 border-white rounded-full"></div>
            </div>
            <div class="text-center">
                <h3 class="font-bold text-slate-800 tracking-tight uppercase">{{ Auth::user()->name }}</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Administrator</p>
            </div>
        </div>
        <nav class="flex-1 p-6 space-y-1.5 mt-2">
            <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-white hover:shadow-sm rounded-xl transition-all group" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined text-slate-400 group-hover:text-slate-600">dashboard</span>
                <span class="font-medium text-sm">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-white hover:shadow-sm rounded-xl transition-all group" href="{{ route('admin.attendance') }}">
                <span class="material-symbols-outlined text-slate-400 group-hover:text-slate-600">calendar_today</span>
                <span class="font-medium text-sm">Absensi</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-white hover:shadow-sm rounded-xl transition-all group" href="{{ route('admin.absence.index') }}">
                <span class="material-symbols-outlined text-slate-400 group-hover:text-slate-600">hourglass_empty</span>
                <span class="font-medium text-sm">Ketidakhadiran</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-white hover:shadow-sm rounded-xl transition-all group" href="{{ route('employees.index') }}">
                <span class="material-symbols-outlined text-slate-400 group-hover:text-slate-600">group</span>
                <span class="font-medium text-sm">Karyawan</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 bg-[var(--lavender)] text-slate-800 rounded-xl shadow-sm transition-all" href="{{ route('admin.settings.index') }}">
                <span class="material-symbols-outlined text-slate-700">settings</span>
                <span class="font-bold text-sm">Pengaturan Sistem</span>
            </a>
        </nav>
        <div class="p-6 border-t border-slate-200/40">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-slate-600 hover:bg-[var(--peach)] hover:text-slate-800 rounded-xl transition-all group">
                    <span class="material-symbols-outlined text-[var(--peach)] group-hover:text-slate-800">logout</span>
                    <span class="font-bold text-sm">Keluar Sesi</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto bg-white">
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-100 px-8 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Sistem Absensi</h2>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider" id="current-date"></p>
                    <p class="text-sm font-bold text-slate-700" id="current-time"></p>
                </div>
                <div class="h-8 w-px bg-slate-100"></div>
                <button class="p-2 text-slate-400 hover:bg-slate-50 rounded-full transition-colors relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-rose-400 rounded-full border-2 border-white"></span>
                </button>
            </div>
        </header>

        <div class="p-10 max-w-5xl">
            
            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-100 text-green-600 px-4 py-3 rounded-2xl relative shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-500">check_circle</span>
                    <span class="block sm:inline font-medium text-sm">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            <div class="mb-10">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Pengaturan Sistem</h1>
                <p class="text-slate-400 mt-1 font-medium">Atur konfigurasi sistem absensi sesuai dengan kebutuhan instansi.</p>
            </div>

            <div class="flex gap-2 bg-slate-50 p-1.5 rounded-2xl mb-10 w-fit">
                <button @click="activeTab = 'jam_kerja'" 
                        :class="activeTab === 'jam_kerja' ? 'bg-[var(--lavender)] text-slate-800 shadow-sm' : 'text-slate-500 hover:bg-white hover:text-slate-700'"
                        class="px-8 py-2.5 text-sm font-bold rounded-xl transition-all">
                    Jam Kerja
                </button>
                
                <button @click="activeTab = 'lokasi'" 
                        :class="activeTab === 'lokasi' ? 'bg-[var(--lavender)] text-slate-800 shadow-sm' : 'text-slate-500 hover:bg-white hover:text-slate-700'"
                        class="px-6 py-2.5 text-sm font-bold rounded-xl transition-all">
                    Lokasi Presensi
                </button>
            </div>

            <!-- FORM JAM KERJA -->
            <div x-show="activeTab === 'jam_kerja'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <form action="{{ route('admin.settings.work-hours.update') }}" method="POST" id="workHoursForm">
                    @csrf
                    @method('PATCH')
                    
                    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 bg-slate-50/30">
                            <h3 class="text-base font-bold flex items-center gap-2 text-slate-700">
                                <span class="material-symbols-outlined text-[var(--lavender)]">schedule</span>
                                Konfigurasi Jam Kerja Utama
                            </h3>
                        </div>
                        
                        <div class="p-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <!-- JAM MASUK -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Jam Masuk</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="material-symbols-outlined text-slate-300 group-focus-within:text-[var(--sky)]">alarm</span>
                                        </div>
                                        <input name="work_start_time" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-[var(--sky)]/20 focus:border-[var(--sky)] border shadow-sm transition-all font-semibold text-slate-700" type="time" value="{{ \Carbon\Carbon::parse($settings->work_start_time)->format('H:i') }}"/>
                                    </div>
                                    <p class="text-[11px] text-slate-400 ml-1">Waktu standar karyawan memulai absen masuk.</p>
                                </div>

                                <!-- JAM PULANG -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Jam Pulang</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="material-symbols-outlined text-slate-300 group-focus-within:text-[var(--sky)]">history</span>
                                        </div>
                                        <input name="work_end_time" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-[var(--sky)]/20 focus:border-[var(--sky)] border shadow-sm transition-all font-semibold text-slate-700" type="time" value="{{ \Carbon\Carbon::parse($settings->work_end_time)->format('H:i') }}"/>
                                    </div>
                                    <p class="text-[11px] text-slate-400 ml-1">Waktu standar karyawan dapat melakukan absen pulang.</p>
                                </div>

                                <!-- LEMBUR -->
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Rentang Jam Lembur</label>
                                    <div class="flex flex-col sm:flex-row items-center gap-4">
                                        <div class="relative group flex-1 w-full">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <span class="material-symbols-outlined text-slate-300 group-focus-within:text-[var(--sky)]">more_time</span>
                                            </div>
                                            <input name="overtime_start_time" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-[var(--sky)]/20 focus:border-[var(--sky)] border shadow-sm transition-all font-semibold text-slate-700" type="time" value="{{ \Carbon\Carbon::parse($settings->overtime_start_time)->format('H:i') }}"/>
                                        </div>
                                        <span class="text-slate-300 font-bold px-2">s/d</span>
                                        <div class="relative group flex-1 w-full">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <span class="material-symbols-outlined text-slate-300 group-focus-within:text-[var(--sky)]">dark_mode</span>
                                            </div>
                                            <input name="overtime_end_time" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-[var(--sky)]/20 focus:border-[var(--sky)] border shadow-sm transition-all font-semibold text-slate-700" type="time" value="{{ \Carbon\Carbon::parse($settings->overtime_end_time)->format('H:i') }}"/>
                                        </div>
                                    </div>
                                </div>

                                <!-- TOLERANSI -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Toleransi Keterlambatan</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="material-symbols-outlined text-slate-300 group-focus-within:text-[var(--sky)]">avg_time</span>
                                        </div>
                                        <input name="late_tolerance_minutes" class="w-full pl-12 pr-20 py-3.5 bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-[var(--sky)]/20 focus:border-[var(--sky)] border shadow-sm transition-all font-semibold text-slate-700" placeholder="15" type="number" value="{{ $settings->late_tolerance_minutes }}"/>
                                        <div class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none">
                                            <span class="text-sm font-bold text-slate-400">Menit</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-12 p-5 bg-[var(--sky)]/15 border border-[var(--sky)]/30 rounded-2xl flex items-start gap-4">
                                <span class="material-symbols-outlined text-[var(--sky)] text-2xl">info</span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Catatan Konfigurasi</h4>
                                    <p class="text-xs text-slate-600 mt-1 leading-relaxed font-medium">Pengaturan jam kerja yang diubah akan disinkronisasikan ke seluruh jadwal kerja aktif mulai siklus harian berikutnya.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="mt-10 flex justify-end gap-4">
                    <form action="{{ route('admin.settings.work-hours.reset') }}" method="POST" onsubmit="return confirm('Kembalikan pengaturan ke default (08:00 - 17:00)?');">
                        @csrf
                        <button type="submit" class="px-8 py-3.5 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-2xl transition-all">
                            Atur Ulang
                        </button>
                    </form>
                    
                    <button type="submit" form="workHoursForm" class="px-10 py-3.5 bg-[var(--sage)] text-white font-bold rounded-2xl shadow-lg shadow-[var(--sage)]/30 hover:shadow-[var(--sage)]/50 hover:-translate-y-0.5 transition-all flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>

            <!-- FORM LOKASI PRESENSI -->
            <div x-show="activeTab === 'lokasi'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <form action="{{ route('admin.settings.location.update') }}" method="POST" id="locationForm">
                    @csrf
                    @method('PATCH')
                    
                    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 bg-slate-50/30">
                            <h3 class="text-base font-bold flex items-center gap-2 text-slate-700">
                                <span class="material-symbols-outlined text-[var(--lavender)]">location_on</span>
                                Konfigurasi Lokasi Kantor
                            </h3>
                        </div>
                        
                        <div class="p-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Latitude</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="material-symbols-outlined text-slate-300 group-focus-within:text-[var(--sky)]">explore</span>
                                        </div>
                                        <input name="office_latitude" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-[var(--sky)]/20 focus:border-[var(--sky)] border shadow-sm transition-all font-semibold text-slate-700" type="text" value="{{ $settings->office_latitude }}"/>
                                    </div>
                                    <p class="text-[11px] text-slate-400 ml-1">Koordinat garis lintang lokasi kantor.</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Longitude</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="material-symbols-outlined text-slate-300 group-focus-within:text-[var(--sky)]">explore</span>
                                        </div>
                                        <input name="office_longitude" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-[var(--sky)]/20 focus:border-[var(--sky)] border shadow-sm transition-all font-semibold text-slate-700" type="text" value="{{ $settings->office_longitude }}"/>
                                    </div>
                                    <p class="text-[11px] text-slate-400 ml-1">Koordinat garis bujur lokasi kantor.</p>
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Radius Absensi (KM)</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="material-symbols-outlined text-slate-300 group-focus-within:text-[var(--sky)]">radar</span>
                                        </div>
                                        <input name="office_radius" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl focus:ring-4 focus:ring-[var(--sky)]/20 focus:border-[var(--sky)] border shadow-sm transition-all font-semibold text-slate-700" type="number" step="0.01" value="{{ $settings->office_radius }}"/>
                                    </div>
                                    <p class="text-[11px] text-slate-400 ml-1">Jarak maksimum (dalam Kilometer) karyawan diizinkan melakukan absensi dari titik pusat kantor.</p>
                                </div>
                            </div>

                            <div class="mt-12 p-5 bg-[var(--sky)]/15 border border-[var(--sky)]/30 rounded-2xl flex items-start gap-4">
                                <span class="material-symbols-outlined text-[var(--sky)] text-2xl">map</span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Informasi Lokasi</h4>
                                    <p class="text-xs text-slate-600 mt-1 leading-relaxed font-medium">Pastikan koordinat lokasi akurat. Radius yang terlalu kecil dapat menyulitkan absensi jika GPS perangkat kurang presisi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="mt-10 flex justify-end gap-4">
                    <form action="{{ route('admin.settings.location.reset') }}" method="POST" onsubmit="return confirm('Kembalikan lokasi ke default?');">
                        @csrf
                        <button type="submit" class="px-8 py-3.5 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-2xl transition-all">
                            Reset Lokasi
                        </button>
                    </form>
                    
                    <button type="submit" form="locationForm" class="px-10 py-3.5 bg-[var(--sage)] text-white font-bold rounded-2xl shadow-lg shadow-[var(--sage)]/30 hover:shadow-[var(--sage)]/50 hover:-translate-y-0.5 transition-all flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">my_location</span>
                        Update Lokasi
                    </button>
                </div>
            </div>

        </div>
    </main>

    <script>
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', dateOptions);
            document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', timeOptions) + ' WIB';
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
</body>
</html>
