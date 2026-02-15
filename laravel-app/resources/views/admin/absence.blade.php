<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Manajemen Ketidakhadiran - Admin Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#D4C5E2", // Lavender
                        sage: "#C8D5B9",
                        sky: "#B8D4E3",
                        peach: "#F5D5CB",
                        lavender: "#D4C5E2",
                        "background-light": "#F8F9FA",
                        "background-dark": "#111827",
                    },
                    fontFamily: {
                        display: ["Outfit", "sans-serif"],
                        sans: ["Plus Jakarta Sans", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                        'xl': '1rem',
                        '2xl': '1.5rem',
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .sidebar-item-active {
            background-color: #D4C5E2;
            color: #4B4B4B;
        }
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #D4C5E2;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 min-h-screen flex" x-data="{ detailModalOpen: false, selectedIzin: null }">
    
    <!-- SIDEBAR -->
    <aside class="w-72 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col fixed h-full z-50 hidden md:flex">
        <div class="p-8 pb-4">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-full bg-lavender flex items-center justify-center text-gray-700 font-bold overflow-hidden shadow-sm">
                    @if(Auth::user()->foto)
                        <img alt="Profile" src="{{ asset('storage/' . Auth::user()->foto) }}" class="w-full h-full object-cover"/>
                    @else
                        <span class="material-icons-round text-gray-600">person</span>
                    @endif
                </div>
                <div>
                    <h3 class="font-bold text-sm leading-tight text-gray-900 dark:text-white uppercase tracking-wider">{{ Auth::user()->name }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Admin</p>
                </div>
            </div>
            <nav class="space-y-2">
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all" href="{{ route('admin.dashboard') }}">
                    <span class="material-icons-round text-xl">dashboard</span>
                    Dashboard
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all" href="{{ route('admin.attendance') }}">
                    <span class="material-icons-round text-xl">event_available</span>
                    Absensi
                </a>
                <a class="sidebar-item-active flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all shadow-sm" href="{{ route('admin.absence.index') }}">
                    <span class="material-icons-round text-xl">hourglass_empty</span>
                    Manajemen Ketidakhadiran
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all" href="{{ route('employees.index') }}">
                    <span class="material-icons-round text-xl">people_alt</span>
                    Manajemen Karyawan
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all" href="{{ route('admin.settings.index') }}">
                    <span class="material-icons-round text-xl">settings</span>
                    Pengaturan Sistem
                </a>
            </nav>
        </div>
        <div class="mt-auto p-8 pt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                    <span class="material-icons-round text-xl">logout</span>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 md:ml-72">
        <header class="h-20 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 px-8 flex items-center justify-between sticky top-0 z-40">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-black dark:bg-white rounded-full flex items-center justify-center">
                    <span class="material-icons-round text-white dark:text-black">admin_panel_settings</span>
                </div>
                <h1 class="font-bold text-lg tracking-tight dark:text-white">Sistem Absensi</h1>
            </div>
            <div class="flex items-center gap-4">
                <button class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors" onclick="document.documentElement.classList.toggle('dark')">
                    <span class="material-icons-round dark:hidden">dark_mode</span>
                    <span class="material-icons-round hidden dark:block">light_mode</span>
                </button>
            </div>
        </header>

        <div class="p-4 sm:p-8 max-w-7xl mx-auto">
            
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Manajemen Ketidakhadiran</h2>
                <p class="text-gray-500 dark:text-gray-400">Verifikasi dan Pengelolaan Pengajuan Ketidakhadiran Karyawan</p>
            </div>

            <!-- FILTERS -->
            <form method="GET" action="{{ route('admin.absence.index') }}" class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4 flex-1">
                        <div class="relative min-w-[240px]">
                            <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                            <input name="q" value="{{ request('q') }}" class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-lavender dark:text-white" placeholder="Cari data karyawan..." type="text"/>
                        </div>
                        <div class="relative min-w-[160px]">
                            <select name="status" class="w-full pl-4 pr-10 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-lavender dark:text-white appearance-none">
                                <option value="">Semua Status</option>
                                <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="Di Validasi" {{ request('status') == 'Di Validasi' ? 'selected' : '' }}>Di Validasi</option>
                                <option value="Di Tolak" {{ request('status') == 'Di Tolak' ? 'selected' : '' }}>Di Tolak</option>
                            </select>
                            <span class="material-icons-round absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">expand_more</span>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 px-4 py-2 rounded-xl">
                            <span class="material-icons-round text-gray-400 text-xl">calendar_today</span>
                            <input name="start_date" value="{{ request('start_date') }}" class="bg-transparent border-none p-0 text-sm focus:ring-0 dark:text-white" type="date"/>
                            <span class="text-gray-400">/</span>
                            <input name="end_date" value="{{ request('end_date') }}" class="bg-transparent border-none p-0 text-sm focus:ring-0 dark:text-white" type="date"/>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2.5 rounded-xl flex items-center gap-2 font-semibold transition-all shadow-sm">
                            <span class="material-icons-round text-xl">filter_alt</span>
                            Filter
                        </button>
                        <button type="button" class="bg-sage hover:opacity-90 text-gray-700 px-6 py-2.5 rounded-xl flex items-center gap-2 font-semibold transition-all shadow-sm opacity-50 cursor-not-allowed">
                            <span class="material-icons-round text-xl">download</span>
                            Export Excel
                        </button>
                    </div>
                </div>
            </form>

            <!-- TABLE -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Mulai</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Selesai</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alasan</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($izins as $index => $izin)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors {{ $izin->status === 'pending' ? 'bg-orange-50/30 dark:bg-orange-900/10' : '' }}">
                                <td class="px-6 py-4 text-sm font-medium">{{ $izins->firstItem() + $index }}</td>
                                <td class="px-6 py-4 text-sm font-semibold">{{ $izin->user->name }}</td>
                                <td class="px-6 py-4 text-sm">{{ ucfirst($izin->jenis) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 truncate max-w-[150px]">{{ $izin->alasan }}</td>
                                <td class="px-6 py-4">
                                    @if($izin->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-peach text-orange-800 text-xs font-bold">
                                            <span class="material-icons-round text-sm">schedule</span>
                                            Menunggu
                                        </span>
                                    @elseif($izin->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sage text-green-800 text-xs font-bold">
                                            <span class="material-icons-round text-sm">check_circle</span>
                                            Di Validasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold">
                                            <span class="material-icons-round text-sm">cancel</span>
                                            Di Tolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="detailModalOpen = true; selectedIzin = {{ $izin->toJson() }}; selectedIzin.user = {{ $izin->user->toJson() }}" 
                                            class="bg-sky/30 hover:bg-sky text-sky-900 dark:text-sky-300 px-4 py-1.5 rounded-lg text-xs font-bold transition-all">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500 italic">
                                    Tidak ada data pengajuan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6">
                    {{ $izins->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL DETAIL -->
    <div x-show="detailModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="detailModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

            <div x-show="detailModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 w-full max-w-2xl border border-white/20 dark:border-slate-700">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-lavender/30 rounded-lg">
                            <span class="material-symbols-rounded text-lavender-700 dark:text-lavender-300">description</span>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Detail Pengajuan Ketidakhadiran</h2>
                    </div>
                    <button @click="detailModalOpen = false" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-colors text-slate-400">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-8 overflow-y-auto max-h-[70vh]">
                    <section>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-rounded text-sky font-variation-fill text-xl">person</span>
                            <h3 class="font-semibold text-slate-700 dark:text-slate-300">Identitas Karyawan</h3>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-5 rounded-xl border border-slate-100 dark:border-slate-700 flex items-center gap-5">
                            <div class="w-20 h-20 rounded-2xl bg-sky/20 flex items-center justify-center overflow-hidden border-2 border-white dark:border-slate-700 shadow-sm">
                                <template x-if="selectedIzin && selectedIzin.user.foto">
                                    <img :src="'/storage/' + selectedIzin.user.foto" class="w-full h-full object-cover"/>
                                </template>
                                <template x-if="!selectedIzin || !selectedIzin.user.foto">
                                    <span class="material-icons-round text-3xl text-sky-600">person</span>
                                </template>
                            </div>
                            <div class="grid grid-cols-2 gap-x-8 gap-y-1 flex-1">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Nama Lengkap</p>
                                    <p class="font-bold text-slate-800 dark:text-slate-100" x-text="selectedIzin?.user.name"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">NIP / Username</p>
                                    <p class="text-slate-700 dark:text-slate-300" x-text="selectedIzin?.user.username"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Jabatan</p>
                                    <p class="text-slate-700 dark:text-slate-300" x-text="selectedIzin?.user.jabatan || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Email</p>
                                    <p class="text-slate-700 dark:text-slate-300" x-text="selectedIzin?.user.email"></p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-rounded text-lavender font-variation-fill text-xl">event_note</span>
                            <h3 class="font-semibold text-slate-700 dark:text-slate-300">Detail Pengajuan</h3>
                        </div>
                        <div class="overflow-hidden border border-slate-100 dark:border-slate-700 rounded-xl">
                            <table class="w-full text-left border-collapse">
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                    <tr class="bg-white dark:bg-slate-800">
                                        <td class="py-3 px-4 text-sm font-medium text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/30 w-1/3">Jenis Pengajuan</td>
                                        <td class="py-3 px-4 text-sm text-slate-800 dark:text-slate-100" x-text="selectedIzin ? selectedIzin.jenis.charAt(0).toUpperCase() + selectedIzin.jenis.slice(1) : ''"></td>
                                    </tr>
                                    <tr class="bg-white dark:bg-slate-800">
                                        <td class="py-3 px-4 text-sm font-medium text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/30">Tanggal Pelaksanaan</td>
                                        <td class="py-3 px-4 text-sm text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                            <span class="material-symbols-rounded text-slate-400 text-lg">calendar_today</span>
                                            <span x-text="selectedIzin ? (selectedIzin.tanggal_mulai + ' - ' + selectedIzin.tanggal_selesai) : ''"></span>
                                        </td>
                                    </tr>
                                    <tr class="bg-white dark:bg-slate-800">
                                        <td class="py-3 px-4 text-sm font-medium text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/30">Alasan</td>
                                        <td class="py-3 px-4 text-sm font-bold text-slate-800 dark:text-slate-100" x-text="selectedIzin?.alasan"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <section>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-rounded text-sky font-variation-fill text-xl">attach_file</span>
                                <h3 class="font-semibold text-slate-700 dark:text-slate-300">Dokumen Pendukung</h3>
                            </div>
                            <template x-if="selectedIzin && selectedIzin.dokumen">
                                <a :href="'/storage/' + selectedIzin.dokumen" target="_blank" class="group relative flex items-center gap-4 p-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl hover:border-sky/50 transition-colors cursor-pointer bg-slate-50/30 dark:bg-slate-900/20">
                                    <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-lg flex items-center justify-center shadow-sm text-sky">
                                        <span class="material-symbols-rounded">article</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 group-hover:text-sky transition-colors">Lihat Dokumen</p>
                                        <p class="text-xs text-slate-400">Klik untuk mengunduh</p>
                                    </div>
                                    <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="material-symbols-rounded text-slate-400">download</span>
                                    </div>
                                </a>
                            </template>
                            <template x-if="!selectedIzin || !selectedIzin.dokumen">
                                <div class="p-4 border-2 border-dashed border-slate-200 rounded-xl text-center text-slate-400 text-sm">
                                    Tidak ada dokumen dilampirkan
                                </div>
                            </template>
                        </section>

                        <section>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-rounded text-peach font-variation-fill text-xl">info</span>
                                <h3 class="font-semibold text-slate-700 dark:text-slate-300">Status Saat Ini</h3>
                            </div>
                            <div class="bg-peach/20 dark:bg-peach/10 border border-peach/30 rounded-xl p-4 flex flex-col items-center justify-center min-h-[84px]">
                                <div class="flex items-center gap-2 font-semibold mb-1"
                                     :class="{
                                         'text-orange-700 dark:text-orange-300': selectedIzin?.status === 'pending',
                                         'text-green-700 dark:text-green-300': selectedIzin?.status === 'approved',
                                         'text-gray-700 dark:text-gray-300': selectedIzin?.status === 'rejected'
                                     }">
                                    <span class="material-symbols-rounded text-lg" 
                                          :class="{'animate-pulse': selectedIzin?.status === 'pending'}"
                                          x-text="selectedIzin?.status === 'pending' ? 'hourglass_top' : (selectedIzin?.status === 'approved' ? 'check_circle' : 'cancel')"></span>
                                    <span x-text="selectedIzin?.status === 'pending' ? 'Menunggu Persetujuan' : (selectedIzin?.status === 'approved' ? 'Disetujui' : 'Ditolak')"></span>
                                </div>
                                <p class="text-[10px] uppercase tracking-widest font-bold opacity-70"
                                   x-text="selectedIzin?.status === 'pending' ? 'Pending Review' : 'Final Decision'"></p>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-4">
                    <button @click="detailModalOpen = false" class="px-6 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 font-semibold transition-all flex items-center gap-2">
                        Tutup
                    </button>
                    
                    <template x-if="selectedIzin && selectedIzin.status === 'pending'">
                        <div class="flex gap-3">
                            <form :action="`/admin/absence-management/${selectedIzin?.id}/status`" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 hover:border-red-200 transition-all">
                                    <span class="material-symbols-rounded text-lg">close</span>
                                    Tolak
                                </button>
                            </form>
                            
                            <form :action="`/admin/absence-management/${selectedIzin?.id}/status`" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="flex items-center gap-2 px-8 py-2.5 rounded-xl bg-primary text-slate-800 font-bold hover:shadow-lg hover:shadow-sage/20 hover:scale-[1.02] transition-all">
                                    <span class="material-symbols-rounded text-lg">check_circle</span>
                                    Validasi
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
