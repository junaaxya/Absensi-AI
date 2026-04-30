<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Presensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        if (localStorage.getItem('darkMode') === 'true' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body class="font-sans antialiased bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 transition-colors duration-300 min-h-screen pb-24 md:pb-0 md:pl-64">

    <!-- Global Toast Notification System -->
    @if(session('success') || session('error') || $errors->any())
        <div x-data="{
                                        toasts: [],
                                        init() {
                                        // Checks for Session Flash Messages
                                        @if(session('success'))
                                            this.addToast('success', '{{ session('success') }}');
                                        @endif
                                        @if(session('error'))
                                            this.addToast('error', '{{ session('error') }}');
                                        @endif
                                        @if($errors->any())
                                            @foreach($errors->all() as $error)
                                                this.addToast('error', '{{ $error }}');
                                            @endforeach
                                        @endif

                                        // Check for URL Query Parameters (e.g. from JS redirects)
                                        const urlParams = new URLSearchParams(window.location.search);
                                        if (urlParams.has('status') && urlParams.has('message')) {
                                            const status = urlParams.get('status'); // 'success' or 'error'
                                            const message = urlParams.get('message');
                                            if (status === 'success' || status === 'error') {
                                                this.addToast(status, message);

                                                // Clean URL
                                                const newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
                                                window.history.replaceState({path: newUrl}, '', newUrl);
                                            }
                                        }
                                    },
                                        addToast(type, message) {
                                            const id = Date.now() + Math.random();
                                            this.toasts.push({ id, type, message });
                                            setTimeout(() => this.removeToast(id), 5000);
                                        },
                                        removeToast(id) {
                                            this.toasts = this.toasts.filter(t => t.id !== id);
                                        }
                                    }"
            class="fixed top-4 right-4 z-[100] flex flex-col gap-3 w-full max-w-sm px-4 md:px-0">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-show="true" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                    x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-x-8 scale-95" :class="{
                                                    'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-700': toast.type === 'success',
                                                    'bg-red-50 border-red-200 dark:bg-red-900/30 dark:border-red-700': toast.type === 'error'
                                                 }"
                    class="rounded-2xl border p-4 shadow-soft backdrop-blur-md relative overflow-hidden cursor-pointer"
                    @click="removeToast(toast.id)">
                    <div class="flex items-start gap-3">
                        <div :class="{
                                                        'bg-emerald-100 text-emerald-600 dark:bg-emerald-800 dark:text-emerald-300': toast.type === 'success',
                                                        'bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-300': toast.type === 'error'
                                                    }"
                            class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center">
                            <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p :class="{ 'text-emerald-800 dark:text-emerald-300': toast.type === 'success', 'text-red-800 dark:text-red-300': toast.type === 'error' }"
                                class="text-sm font-bold" x-text="toast.type === 'success' ? 'Berhasil!' : 'Gagal!'">
                            </p>
                            <p :class="{ 'text-emerald-700 dark:text-emerald-400': toast.type === 'success', 'text-red-700 dark:text-red-400': toast.type === 'error' }"
                                class="text-sm mt-0.5 leading-tight" x-text="toast.message"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @endif

    <!-- DESKTOP SIDEBAR -->
    <aside
        class="hidden md:flex flex-col w-64 h-screen fixed inset-y-0 left-0 bg-white dark:bg-card-dark border-r border-slate-200 dark:border-slate-800 backdrop-blur-xl z-40">
        <!-- Logo Area -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-200 dark:border-slate-800">
            <div class="w-10 h-10 flex items-center justify-center">
                <x-application-logo class="w-10 h-10 shadow-soft rounded-xl" />
            </div>
            <span class="font-bold text-xl text-slate-900 dark:text-white tracking-tight">Presensi</span>
        </div>

        <!-- Dark Mode Toggle -->
        <div class="px-4 pt-4">
            <button onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'))"
                class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="dark:hidden">Mode Gelap</span>
                <span class="hidden dark:inline">Mode Terang</span>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-4 overflow-y-auto no-scrollbar" x-data="employeeSidebar()" x-init="init()">

            {{-- ═══════════════════════════════════════ --}}
            {{-- DASHBOARD — standalone, always visible  --}}
            {{-- ═══════════════════════════════════════ --}}
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('dashboard') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                href="{{ route('dashboard') }}">
                <span class="material-icons-round text-[20px]">home</span>
                <span class="text-sm">Dashboard</span>
            </a>

            {{-- ═══════════════════════════════════════ --}}
            {{-- GROUP: KEHADIRAN                       --}}
            {{-- ═══════════════════════════════════════ --}}
            <div class="mt-4">
                <button @click="toggle('kehadiran')"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    <span class="flex items-center gap-2">
                        <span class="material-icons-round text-[18px]">schedule</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Kehadiran</span>
                    </span>
                    <span class="material-icons-round text-[16px] transition-transform duration-200" :class="isOpen('kehadiran') ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="isOpen('kehadiran')" x-collapse x-cloak class="mt-1 ml-2 space-y-0.5 border-l-2 border-slate-100 dark:border-slate-800 pl-2">
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('izin.*') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('izin.index') }}">
                        <span class="material-icons-round text-[18px]">event_note</span>
                        Izin & Cuti
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('violations.*') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('violations.index') }}">
                        <span class="material-icons-round text-[18px]">gavel</span>
                        Pelanggaran
                    </a>
                </div>
            </div>

            {{-- ═══════════════════════════════════════ --}}
            {{-- GROUP: KEUANGAN                        --}}
            {{-- ═══════════════════════════════════════ --}}
            <div>
                <button @click="toggle('keuangan')"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    <span class="flex items-center gap-2">
                        <span class="material-icons-round text-[18px]">payments</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Keuangan</span>
                    </span>
                    <span class="material-icons-round text-[16px] transition-transform duration-200" :class="isOpen('keuangan') ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="isOpen('keuangan')" x-collapse x-cloak class="mt-1 ml-2 space-y-0.5 border-l-2 border-slate-100 dark:border-slate-800 pl-2">
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('payslips.*') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('payslips.index') }}">
                        <span class="material-icons-round text-[18px]">receipt_long</span>
                        Slip Gaji
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('my-assets.*') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('my-assets.index') }}">
                        <span class="material-icons-round text-[18px]">inventory_2</span>
                        Aset Saya
                    </a>
                </div>
            </div>

            {{-- ═══════════════════════════════════════ --}}
            {{-- GROUP: PEKERJAAN                       --}}
            {{-- ═══════════════════════════════════════ --}}
            <div>
                <button @click="toggle('pekerjaan')"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    <span class="flex items-center gap-2">
                        <span class="material-icons-round text-[18px]">work</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Pekerjaan</span>
                    </span>
                    <span class="material-icons-round text-[16px] transition-transform duration-200" :class="isOpen('pekerjaan') ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="isOpen('pekerjaan')" x-collapse x-cloak class="mt-1 ml-2 space-y-0.5 border-l-2 border-slate-100 dark:border-slate-800 pl-2">
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('my-tasks.*') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('my-tasks.index') }}">
                        <span class="material-icons-round text-[18px]">checklist</span>
                        Task Saya
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('tickets.*') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('tickets.index') }}">
                        <span class="material-icons-round text-[18px]">confirmation_number</span>
                        Tiket Saya
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('forms.*') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('forms.index') }}">
                        <span class="material-icons-round text-[18px]">dynamic_form</span>
                        Form Internal
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('training.*') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('training.index') }}">
                        <span class="material-icons-round text-[18px]">school</span>
                        Training
                    </a>
                </div>
            </div>

            {{-- ═══════════════════════════════════════ --}}
            {{-- GROUP: AKUN                            --}}
            {{-- ═══════════════════════════════════════ --}}
            <div>
                <button @click="toggle('akun')"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    <span class="flex items-center gap-2">
                        <span class="material-icons-round text-[18px]">person</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Akun</span>
                    </span>
                    <span class="material-icons-round text-[16px] transition-transform duration-200" :class="isOpen('akun') ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="isOpen('akun')" x-collapse x-cloak class="mt-1 ml-2 space-y-0.5 border-l-2 border-slate-100 dark:border-slate-800 pl-2">
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('profile.edit') ? 'bg-primary/20 dark:bg-primary/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('profile.edit') }}">
                        <span class="material-icons-round text-[18px]">manage_accounts</span>
                        Profil Saya
                    </a>
                </div>
            </div>

            {{-- ═══════════════════════════════════════ --}}
            {{-- GROUP: ADMINISTRATOR (role-gated)      --}}
            {{-- ═══════════════════════════════════════ --}}
            @hasanyrole('Direktur|Vice President|Manager|Supervisor|Team Leader')
            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                <button @click="toggle('administrator')"
                    class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    <span class="flex items-center gap-2">
                        <span class="material-icons-round text-[18px]">admin_panel_settings</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Administrator</span>
                    </span>
                    <span class="material-icons-round text-[16px] transition-transform duration-200" :class="isOpen('administrator') ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="isOpen('administrator')" x-collapse x-cloak class="mt-1 ml-2 space-y-0.5 border-l-2 border-slate-100 dark:border-slate-800 pl-2">
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('employees.*') ? 'bg-sky/20 dark:bg-sky/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('employees.index') }}">
                        <span class="material-icons-round text-[18px]">groups</span>
                        Data Karyawan
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.absence.*') || request()->routeIs('admin.izin.*') ? 'bg-peach/20 dark:bg-peach/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('admin.absence.index') }}">
                        <span class="material-icons-round text-[18px]">fact_check</span>
                        Approval Izin
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.attendance') || request()->routeIs('admin.attendance.*') ? 'bg-lavender/20 dark:bg-lavender/10 text-slate-900 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                        href="{{ route('admin.attendance') }}">
                        <span class="material-icons-round text-[18px]">assessment</span>
                        Laporan Absensi
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm text-sky-600 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-sky-900/20 font-medium"
                        href="{{ route('admin.dashboard') }}">
                        <span class="material-icons-round text-[18px]">open_in_new</span>
                        Buka Panel Admin
                    </a>
                </div>
            </div>
            @endhasanyrole
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-3 mb-3">
                <div
                    class="w-10 h-10 rounded-full bg-primary/30 dark:bg-primary/20 flex items-center justify-center text-slate-900 dark:text-white font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate capitalize">{{ Auth::user()->getRoleNames()->first() ?? '-' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-2 text-sm font-medium text-rose-500 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Header (conditionally hidden if dashboard provides its own) -->
    <div class="md:hidden flex items-center justify-between mb-6" x-show="!$routeIs('dashboard')">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center">
                <x-application-logo class="w-10 h-10 shadow-soft rounded-xl" />
            </div>
            <div>
                <h1 class="font-bold text-lg text-slate-900 dark:text-white leading-tight">Presensi</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            </div>
        </div>
        <button onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'))"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 transition-colors">
            <svg class="w-5 h-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>
    </div>

    <main class="px-4 pb-4 md:px-6 md:pb-6">
    @yield('content')
    <div class="h-20 md:h-0"></div> <!-- Spacer for bottom nav mobile -->
    </main>

    <!-- MOBILE BOTTOM NAVIGATION (Shopee/Gojek Style) -->
    <nav
        class="md:hidden fixed bottom-0 left-0 right-0 h-[70px] bg-white dark:bg-card-dark border-t border-slate-200 dark:border-slate-800 z-50 flex items-center justify-around px-4 pb-2 shadow-[0_-4px_20px_rgba(0,0,0,0.03)] rounded-t-[20px]">

        <a href="{{ route('dashboard') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 pt-2 transition-all {{ request()->routeIs('dashboard') ? 'text-emerald-600 dark:text-primary' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <div
                class="{{ request()->routeIs('dashboard') ? 'bg-emerald-50 dark:bg-primary/20' : 'bg-transparent' }} p-1.5 rounded-xl transition-all">
                <svg class="w-6 h-6 stroke-[2px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>

        <a href="{{ route('izin.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 pt-2 transition-all {{ request()->routeIs('izin.index') ? 'text-emerald-600 dark:text-primary' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <div
                class="{{ request()->routeIs('izin.index') ? 'bg-emerald-50 dark:bg-primary/20' : 'bg-transparent' }} p-1.5 rounded-xl transition-all">
                <svg class="w-6 h-6 stroke-[2px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="text-[10px] font-medium">Izin</span>
        </a>

        <a href="{{ route('tickets.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 pt-2 transition-all {{ request()->routeIs('tickets.*') ? 'text-emerald-600 dark:text-primary' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <div
                class="{{ request()->routeIs('tickets.*') ? 'bg-emerald-50 dark:bg-primary/20' : 'bg-transparent' }} p-1.5 rounded-xl transition-all">
                <svg class="w-6 h-6 stroke-[2px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <span class="text-[10px] font-medium">Tiket</span>
        </a>

        @hasanyrole('Direktur|Vice President|Manager|Supervisor|Team Leader')
            <a href="{{ route('employees.index') }}"
                class="flex flex-col items-center justify-center w-full h-full gap-1 pt-2 transition-all {{ request()->routeIs('employees.*') ? 'text-emerald-600 dark:text-primary' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
                <div
                    class="{{ request()->routeIs('employees.*') ? 'bg-emerald-50 dark:bg-primary/20' : 'bg-transparent' }} p-1.5 rounded-xl transition-all">
                    <svg class="w-6 h-6 stroke-[2px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium">Admin</span>
            </a>
        @endhasanyrole

        <a href="{{ route('profile.edit') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 pt-2 transition-all {{ request()->routeIs('profile.edit') ? 'text-emerald-600 dark:text-primary' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <div
                class="{{ request()->routeIs('profile.edit') ? 'bg-emerald-50 dark:bg-primary/20' : 'bg-transparent' }} p-1.5 rounded-xl transition-all">
                <svg class="w-6 h-6 stroke-[2px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    </nav>

    <script>
        function employeeSidebar() {
            return {
                openGroups: [],
                toggle(group) {
                    if (this.openGroups.includes(group)) {
                        this.openGroups = this.openGroups.filter(g => g !== group);
                    } else {
                        this.openGroups.push(group);
                    }
                    this.save();
                },
                isOpen(group) {
                    return this.openGroups.includes(group);
                },
                save() {
                    localStorage.setItem('emp_sidebar_open', JSON.stringify(this.openGroups));
                },
                init() {
                    // Restore from localStorage
                    const saved = localStorage.getItem('emp_sidebar_open');
                    if (saved) {
                        try { this.openGroups = JSON.parse(saved); } catch(e) { this.openGroups = []; }
                    }
                    // Auto-expand group containing current page
                    this.autoExpandActive();
                },
                autoExpandActive() {
                    const path = window.location.pathname;
                    const map = {
                        'kehadiran': ['/izin', '/my-violations'],
                        'keuangan': ['/my-payslips', '/my-assets'],
                        'pekerjaan': ['/my-tasks', '/tickets', '/forms', '/training'],
                        'akun': ['/profile'],
                        'administrator': ['/admin', '/employees'],
                    };
                    for (const [group, paths] of Object.entries(map)) {
                        if (paths.some(p => path.startsWith(p))) {
                            if (!this.openGroups.includes(group)) {
                                this.openGroups.push(group);
                            }
                        }
                    }
                    this.save();
                }
            };
        }
    </script>
    @stack('scripts')
</body>

</html>
