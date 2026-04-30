<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Absensi - Admin Portal</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined|Material+Icons+Round"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        danger: "#F5D5CB", // reuse peach for danger context per design
                        secondary: "#D4C5E2",
                        accent: "#B8D4E3",
                        highlight: "#D4C5E2", // Lavender alias
                        "background-light": "#F8F9FA",
                        "background-dark": "#121212",
                        "card-dark": "#1E1E1E",
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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        [x-cloak] {
            display: none !important;
        }

        .filled-icon {
            font-variation-settings: 'FILL' 1;
        }
    </style>
    @stack('styles')
</head>

<body
    class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 transition-colors duration-300"
    x-data="{ sidebarOpen: false }">

    <!-- SIDEBAR -->
    <aside
        class="w-64 bg-white dark:bg-card-dark border-r border-slate-200 dark:border-slate-800 flex flex-col fixed inset-y-0 left-0 z-50 transition-transform duration-300 md:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <!-- Logo Area - Matching User layout height/padding -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-100 dark:border-slate-800">
            <div class="w-10 h-10 flex items-center justify-center">
                <x-application-logo class="w-10 h-10 shadow-soft rounded-xl" />
            </div>
            <div>
                <h1 class="font-bold text-lg text-slate-900 dark:text-white leading-tight">Presensi</h1>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Admin Portal</p>
            </div>
        </div>

        <div class="p-4 flex-1 overflow-y-auto scrollbar-hide">
            <!-- User Profile Summary in Sidebar -->
            <div
                class="mb-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden">
                    <img alt="Profile" class="w-full h-full object-cover"
                            src="{{ Auth::user()->profile_photo_url }}" />
                </div>
                <div class="overflow-hidden">
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate uppercase">
                        {{ Auth::user()->name }}
                    </h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium tracking-wide uppercase">
                        {{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}
                    </p>
                </div>
            </div>

            <nav class="space-y-1" x-data="sidebarNav()" x-init="init()">

                {{-- ═══════════════════════════════════════ --}}
                {{-- DASHBOARD — always visible, no group   --}}
                {{-- ═══════════════════════════════════════ --}}
                <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                    href="{{ route('admin.dashboard') }}">
                    <span class="material-icons-round text-[20px] {{ request()->routeIs('admin.dashboard') ? 'filled-icon' : '' }}">dashboard</span>
                    <span class="text-sm">Dashboard</span>
                </a>

                {{-- ═══════════════════════════════════════ --}}
                {{-- GROUP: KEHADIRAN                       --}}
                {{-- ═══════════════════════════════════════ --}}
                @canany(['view_team_attendance', 'approve_team_izin', 'manage_violations', 'view_anomaly_attendance', 'view_visit_attendance', 'manage_leave_balances'])
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
                        @can('view_team_attendance')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.attendance') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.attendance') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.attendance') ? 'filled-icon' : '' }}">fact_check</span>
                            Absensi
                        </a>
                        @endcan
                        @can('approve_team_izin')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.absence.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.absence.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.absence.*') ? 'filled-icon' : '' }}">event_busy</span>
                            Ketidakhadiran
                        </a>
                        @endcan
                        @can('view_visit_attendance')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.visits.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.visits.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.visits.*') ? 'filled-icon' : '' }}">location_on</span>
                            Kunjungan
                        </a>
                        @endcan
                        @can('manage_leave_balances')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.leave-balances.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.leave-balances.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.leave-balances.*') ? 'filled-icon' : '' }}">event_available</span>
                            Saldo Cuti
                        </a>
                        @endcan
                        @can('manage_violations')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.violations.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.violations.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.violations.*') ? 'filled-icon' : '' }}">gavel</span>
                            Pelanggaran
                        </a>
                        @endcan
                        @can('view_anomaly_attendance')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.anomaly.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.anomaly.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.anomaly.*') ? 'filled-icon' : '' }}">gps_off</span>
                            Anomali GPS
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- ═══════════════════════════════════════ --}}
                {{-- GROUP: KEPEGAWAIAN                     --}}
                {{-- ═══════════════════════════════════════ --}}
                @canany(['manage_employees', 'manage_recruitment', 'manage_companies'])
                <div>
                    <button @click="toggle('kepegawaian')"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        <span class="flex items-center gap-2">
                            <span class="material-icons-round text-[18px]">badge</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Kepegawaian</span>
                        </span>
                        <span class="material-icons-round text-[16px] transition-transform duration-200" :class="isOpen('kepegawaian') ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="isOpen('kepegawaian')" x-collapse x-cloak class="mt-1 ml-2 space-y-0.5 border-l-2 border-slate-100 dark:border-slate-800 pl-2">
                        @can('manage_employees')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('employees.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('employees.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('employees.*') ? 'filled-icon' : '' }}">groups</span>
                            Data Karyawan
                        </a>
                        @endcan
                        @can('manage_recruitment')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.recruitment.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.recruitment.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.recruitment.*') ? 'filled-icon' : '' }}">person_search</span>
                            Rekrutmen
                        </a>
                        @endcan
                        @can('manage_companies')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.companies.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.companies.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.companies.*') ? 'filled-icon' : '' }}">business</span>
                            Perusahaan
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- ═══════════════════════════════════════ --}}
                {{-- GROUP: KEUANGAN                        --}}
                {{-- ═══════════════════════════════════════ --}}
                @canany(['manage_payroll', 'manage_assets'])
                <div>
                    <button @click="toggle('keuangan')"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        <span class="flex items-center gap-2">
                            <span class="material-icons-round text-[18px]">account_balance_wallet</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Keuangan</span>
                        </span>
                        <span class="material-icons-round text-[16px] transition-transform duration-200" :class="isOpen('keuangan') ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="isOpen('keuangan')" x-collapse x-cloak class="mt-1 ml-2 space-y-0.5 border-l-2 border-slate-100 dark:border-slate-800 pl-2">
                        @can('manage_payroll')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.payroll.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.payroll.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.payroll.*') ? 'filled-icon' : '' }}">payments</span>
                            Payroll
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.salary-components.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.salary-components.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.salary-components.*') ? 'filled-icon' : '' }}">tune</span>
                            Komponen Gaji
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->is('admin/rate-management*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.rate-management.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->is('admin/rate-management*') ? 'filled-icon' : '' }}">percent</span>
                            Tarif & Pajak
                        </a>
                        @endcan
                        @can('manage_assets')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.assets.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.assets.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.assets.*') ? 'filled-icon' : '' }}">inventory_2</span>
                            Aset
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- ═══════════════════════════════════════ --}}
                {{-- GROUP: OPERASIONAL                     --}}
                {{-- ═══════════════════════════════════════ --}}
                @canany(['manage_projects', 'manage_tickets', 'manage_forms', 'manage_training', 'manage_announcements'])
                <div>
                    <button @click="toggle('operasional')"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        <span class="flex items-center gap-2">
                            <span class="material-icons-round text-[18px]">work</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Operasional</span>
                        </span>
                        <span class="material-icons-round text-[16px] transition-transform duration-200" :class="isOpen('operasional') ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="isOpen('operasional')" x-collapse x-cloak class="mt-1 ml-2 space-y-0.5 border-l-2 border-slate-100 dark:border-slate-800 pl-2">
                        @can('manage_projects')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.projects.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.projects.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.projects.*') ? 'filled-icon' : '' }}">assignment</span>
                            Project
                        </a>
                        @endcan
                        @can('manage_tickets')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.tickets.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.tickets.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.tickets.*') ? 'filled-icon' : '' }}">confirmation_number</span>
                            Tiket Layanan
                        </a>
                        @endcan
                        @can('manage_forms')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.forms.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.forms.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.forms.*') ? 'filled-icon' : '' }}">dynamic_form</span>
                            Form Internal
                        </a>
                        @endcan
                        @can('manage_training')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.training.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.training.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.training.*') ? 'filled-icon' : '' }}">school</span>
                            Training
                        </a>
                        @endcan
                        @can('manage_announcements')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.announcements.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.announcements.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.announcements.*') ? 'filled-icon' : '' }}">campaign</span>
                            Pengumuman
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- ═══════════════════════════════════════ --}}
                {{-- GROUP: PENGATURAN                      --}}
                {{-- ═══════════════════════════════════════ --}}
                @canany(['manage_system_settings', 'manage_departments', 'manage_shifts', 'manage_holidays', 'manage_leave_types', 'view_audit_logs', 'export_data'])
                <div>
                    <button @click="toggle('pengaturan')"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        <span class="flex items-center gap-2">
                            <span class="material-icons-round text-[18px]">settings</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Pengaturan</span>
                        </span>
                        <span class="material-icons-round text-[16px] transition-transform duration-200" :class="isOpen('pengaturan') ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="isOpen('pengaturan')" x-collapse x-cloak class="mt-1 ml-2 space-y-0.5 border-l-2 border-slate-100 dark:border-slate-800 pl-2">
                        @canany(['manage_system_settings', 'manage_departments', 'manage_shifts', 'manage_holidays', 'manage_leave_types'])
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.settings.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.settings.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.settings.*') ? 'filled-icon' : '' }}">tune</span>
                            Sistem
                        </a>
                        @endcanany
                        @can('view_audit_logs')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.audit-logs.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.audit-logs.index') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.audit-logs.*') ? 'filled-icon' : '' }}">history</span>
                            Audit Log
                        </a>
                        @endcan
                        @can('export_data')
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('admin.export.*') ? 'bg-primary text-slate-900 font-bold shadow-sm shadow-primary/30' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                            href="{{ route('admin.export.attendance') }}">
                            <span class="material-icons-round text-[18px] {{ request()->routeIs('admin.export.*') ? 'filled-icon' : '' }}">download</span>
                            Export Data
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany
            </nav>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center justify-center gap-2 px-4 py-2 w-full rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-600 transition-all text-sm font-bold">
                    <span class="material-icons-round text-[18px]">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- OVERLAY FOR MOBILE SIDEBAR -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 bg-slate-900/80 z-40 md:hidden backdrop-blur-sm"></div>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="md:ml-64 min-h-screen flex flex-col">

        <!-- HEADER (Optional, can be overridden by views) -->
        <header
            class="h-20 bg-white/80 dark:bg-card-dark/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <!-- Mobile Menu Button -->
                <button @click="sidebarOpen = true"
                    class="p-2 -ml-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 md:hidden">
                    <span class="material-icons-round">menu</span>
                </button>

                <!-- Page Title Placeholder (can be targeted via stack or yield if needed, basically breadcrumb area) -->
                <div class="hidden md:block">
                    <p class="text-xs text-slate-500 font-medium">
                        @yield('header-subtitle', \Carbon\Carbon::now()->translatedFormat('l, d F Y')) </p>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">@yield('header-title', 'Dashboard')
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                    class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:ring-2 hover:ring-primary/50 transition-all"
                    onclick="document.documentElement.classList.toggle('dark')">
                    <span class="material-icons-round dark:hidden text-[20px]">dark_mode</span>
                    <span class="material-icons-round hidden dark:block text-[20px]">light_mode</span>
                </button>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 p-6 md:p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-sm"
                    role="alert">
                    <span class="material-icons-round text-green-600">check_circle</span>
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 bg-sky-100 border border-sky-200 text-sky-700 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-sm"
                    role="alert">
                    <span class="material-icons-round text-sky-600">info</span>
                    <span class="block sm:inline font-medium">{{ session('info') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function sidebarNav() {
            return {
                openGroups: [],
                init() {
                    // Restore from localStorage
                    const saved = localStorage.getItem('sidebar_open_groups');
                    if (saved) {
                        this.openGroups = JSON.parse(saved);
                    }
                    // Auto-expand group containing active page
                    this.autoExpandActive();
                },
                autoExpandActive() {
                    const path = window.location.pathname;
                    const map = {
                        'kehadiran': ['/admin/attendance', '/admin/absence', '/admin/visits', '/admin/leave-balances', '/admin/violations', '/admin/anomaly'],
                        'kepegawaian': ['/employees', '/admin/recruitment', '/admin/companies'],
                        'keuangan': ['/admin/payroll', '/admin/salary-components', '/admin/rate-management', '/admin/assets'],
                        'operasional': ['/admin/projects', '/admin/tickets', '/admin/forms', '/admin/training', '/admin/announcements'],
                        'pengaturan': ['/admin/settings', '/admin/audit-logs', '/admin/export']
                    };
                    for (const [group, routes] of Object.entries(map)) {
                        if (routes.some(r => path.startsWith(r))) {
                            if (!this.openGroups.includes(group)) {
                                this.openGroups.push(group);
                                this.save();
                            }
                            break;
                        }
                    }
                },
                toggle(group) {
                    const idx = this.openGroups.indexOf(group);
                    if (idx === -1) {
                        this.openGroups.push(group);
                    } else {
                        this.openGroups.splice(idx, 1);
                    }
                    this.save();
                },
                isOpen(group) {
                    return this.openGroups.includes(group);
                },
                save() {
                    localStorage.setItem('sidebar_open_groups', JSON.stringify(this.openGroups));
                }
            };
        }
    </script>
    @stack('scripts')
</body>

</html>