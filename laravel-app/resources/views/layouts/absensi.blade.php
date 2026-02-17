<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Presensi</title>
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
</head>

<body class="font-sans antialiased bg-neutral-cream text-text-primary min-h-screen pb-24 md:pb-0 md:pl-64">

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
                                                    'bg-emerald-50 border-emerald-200': toast.type === 'success',
                                                    'bg-red-50 border-red-200': toast.type === 'error'
                                                 }"
                    class="rounded-2xl border p-4 shadow-soft backdrop-blur-md relative overflow-hidden cursor-pointer"
                    @click="removeToast(toast.id)">
                    <div class="flex items-start gap-3">
                        <div :class="{
                                                        'bg-emerald-100 text-emerald-600': toast.type === 'success',
                                                        'bg-red-100 text-red-600': toast.type === 'error'
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
                            <p :class="{ 'text-emerald-800': toast.type === 'success', 'text-red-800': toast.type === 'error' }"
                                class="text-sm font-bold" x-text="toast.type === 'success' ? 'Berhasil!' : 'Gagal!'">
                            </p>
                            <p :class="{ 'text-emerald-700': toast.type === 'success', 'text-red-700': toast.type === 'error' }"
                                class="text-sm mt-0.5 leading-tight" x-text="toast.message"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @endif

    <!-- DESKTOP SIDEBAR -->
    <aside
        class="hidden md:flex flex-col w-64 h-screen fixed inset-y-0 left-0 bg-white/80 backdrop-blur-xl border-r border-neutral-stone/50 z-40">
        <!-- Logo Area -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-neutral-stone/30">
            <div class="w-10 h-10 flex items-center justify-center">
                <x-application-logo class="w-10 h-10 shadow-soft rounded-xl" />
            </div>
            <span class="font-bold text-xl text-text-primary tracking-tight">Presensi</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto no-scrollbar">
            <p class="px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">Menu Utama</p>

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('dashboard') ? 'bg-pastel-sage/20 text-pastel-sage-dark font-semibold' : 'text-text-secondary hover:bg-neutral-stone/30 hover:text-text-primary' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('izin.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('izin.index') ? 'bg-pastel-sage/20 text-pastel-sage-dark font-semibold' : 'text-text-secondary hover:bg-neutral-stone/30 hover:text-text-primary' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Izin & Cuti</span>
            </a>

            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('profile.edit') ? 'bg-pastel-sage/20 text-pastel-sage-dark font-semibold' : 'text-text-secondary hover:bg-neutral-stone/30 hover:text-text-primary' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Profil Saya</span>
            </a>

            @if(Auth::user()->role === 'admin')
                <div class="pt-6 pb-2">
                    <p class="px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider">Administrator</p>
                </div>

                <a href="{{ route('employees.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('employees.*') ? 'bg-pastel-sky/20 text-pastel-sky-dark font-semibold' : 'text-text-secondary hover:bg-neutral-stone/30 hover:text-text-primary' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Data Karyawan</span>
                </a>

                <a href="{{ route('admin.absence.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('admin.absence.*') || request()->routeIs('admin.izin.*') ? 'bg-pastel-rose/20 text-pastel-rose-dark font-semibold' : 'text-text-secondary hover:bg-neutral-stone/30 hover:text-text-primary' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>Approval Izin</span>
                </a>

                <a href="{{ route('admin.attendance') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('admin.attendance') || request()->routeIs('admin.attendance.*') ? 'bg-pastel-peach/20 text-amber-700 font-semibold' : 'text-text-secondary hover:bg-neutral-stone/30 hover:text-text-primary' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Laporan Absensi</span>
                </a>
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-neutral-stone/30 bg-neutral-stone/10">
            <div class="flex items-center gap-3 mb-3">
                <div
                    class="w-10 h-10 rounded-full bg-pastel-sage/50 flex items-center justify-center text-pastel-sage-dark font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-text-primary truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-text-secondary truncate capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-2 text-sm font-medium text-pastel-rose-dark hover:bg-pastel-rose/10 rounded-lg transition">
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
                <h1 class="font-bold text-lg text-text-primary leading-tight">Presensi</h1>
                <p class="text-xs text-text-secondary">{{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            </div>
        </div>
    </div>

    @yield('content')
    <div class="h-20 md:h-0"></div> <!-- Spacer for bottom nav mobile -->
    </main>

    <!-- MOBILE BOTTOM NAVIGATION (Shopee/Gojek Style) -->
    <nav
        class="md:hidden fixed bottom-0 left-0 right-0 h-[70px] bg-white border-t border-gray-100 z-50 flex items-center justify-around px-4 pb-2 shadow-[0_-4px_20px_rgba(0,0,0,0.03)] rounded-t-[20px]">

        <a href="{{ route('dashboard') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 pt-2 transition-all {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">
            <div
                class="{{ request()->routeIs('dashboard') ? 'bg-emerald-50' : 'bg-transparent' }} p-1.5 rounded-xl transition-all">
                <svg class="w-6 h-6 stroke-[2px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>

        <a href="{{ route('izin.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 pt-2 transition-all {{ request()->routeIs('izin.index') ? 'text-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">
            <div
                class="{{ request()->routeIs('izin.index') ? 'bg-emerald-50' : 'bg-transparent' }} p-1.5 rounded-xl transition-all">
                <svg class="w-6 h-6 stroke-[2px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="text-[10px] font-medium">Izin</span>
        </a>

        @if(Auth::user()->role === 'admin')
            <a href="{{ route('employees.index') }}"
                class="flex flex-col items-center justify-center w-full h-full gap-1 pt-2 transition-all {{ request()->routeIs('employees.*') ? 'text-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">
                <div
                    class="{{ request()->routeIs('employees.*') ? 'bg-emerald-50' : 'bg-transparent' }} p-1.5 rounded-xl transition-all">
                    <svg class="w-6 h-6 stroke-[2px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium">Admin</span>
            </a>
        @endif

        <a href="{{ route('profile.edit') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 pt-2 transition-all {{ request()->routeIs('profile.edit') ? 'text-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">
            <div
                class="{{ request()->routeIs('profile.edit') ? 'bg-emerald-50' : 'bg-transparent' }} p-1.5 rounded-xl transition-all">
                <svg class="w-6 h-6 stroke-[2px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    </nav @stack('scripts') </body>

</html>
