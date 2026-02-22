<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Presensi</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .filled-icon {
            font-variation-settings: 'FILL' 1;
        }
    </style>
</head>

<body class="bg-neutral-cream text-text-primary antialiased" x-data="{ sidebarOpen: false }">
    @if(session('success') || session('error') || $errors->any())
        <div x-data="{
            toasts: [],
            init() {
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

                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('status') && urlParams.has('message')) {
                    const status = urlParams.get('status');
                    const message = urlParams.get('message');
                    if (status === 'success' || status === 'error') {
                        this.addToast(status, message);
                        const newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
                        window.history.replaceState({ path: newUrl }, '', newUrl);
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
        }" class="fixed top-4 right-4 z-[100] flex w-full max-w-sm flex-col gap-3 px-4 md:px-0">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-show="true" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                    x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-x-8 scale-95" :class="{
                        'bg-emerald-50 border-emerald-200': toast.type === 'success',
                        'bg-red-50 border-red-200': toast.type === 'error'
                    }" class="relative cursor-pointer overflow-hidden rounded-2xl border p-4 shadow-soft backdrop-blur-md"
                    @click="removeToast(toast.id)">
                    <div class="flex items-start gap-3">
                        <div :class="{
                            'bg-emerald-100 text-emerald-600': toast.type === 'success',
                            'bg-red-100 text-red-600': toast.type === 'error'
                        }" class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full">
                            <svg x-show="toast.type === 'success'" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg x-show="toast.type === 'error'" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p :class="{ 'text-emerald-800': toast.type === 'success', 'text-red-800': toast.type === 'error' }"
                                class="text-sm font-bold" x-text="toast.type === 'success' ? 'Berhasil!' : 'Gagal!'"></p>
                            <p :class="{ 'text-emerald-700': toast.type === 'success', 'text-red-700': toast.type === 'error' }"
                                class="mt-0.5 text-sm leading-tight" x-text="toast.message"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @endif

    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-200 bg-white transition-transform duration-300 md:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-20 items-center gap-3 border-b border-slate-100 px-6">
            <div class="flex h-10 w-10 items-center justify-center">
                <x-application-logo class="h-10 w-10 rounded-xl shadow-soft" />
            </div>
            <div>
                <h1 class="leading-tight font-bold text-slate-900">Presensi</h1>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Employee Portal</p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 scrollbar-hide">
            <div class="mb-6 flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-slate-200">
                    @if(Auth::user()->foto)
                        <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Profile" class="h-full w-full object-cover" />
                    @else
                        <span class="material-icons-round text-slate-500">person</span>
                    @endif
                </div>
                <div class="min-w-0 overflow-hidden">
                    <h3 class="truncate text-sm font-bold uppercase text-slate-900">{{ Auth::user()->name }}</h3>
                    <p class="truncate text-[10px] font-medium uppercase tracking-wide text-slate-500">{{ Auth::user()->role }}</p>
                </div>
            </div>

            <nav class="space-y-1">
                <p class="mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Menu Utama</p>

                <a href="{{ route('dashboard') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ request()->routeIs('dashboard') ? 'bg-pastel-sage/30 text-text-primary font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="material-icons-round text-[20px] {{ request()->routeIs('dashboard') ? 'filled-icon' : '' }}">dashboard</span>
                    <span class="text-sm">Dashboard</span>
                </a>

                <a href="{{ route('izin.index') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ request()->routeIs('izin.*') ? 'bg-pastel-sage/30 text-text-primary font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="material-icons-round text-[20px] {{ request()->routeIs('izin.*') ? 'filled-icon' : '' }}">event_busy</span>
                    <span class="text-sm">Izin & Cuti</span>
                </a>

                <a href="{{ route('profile.edit') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ request()->routeIs('profile.*') ? 'bg-pastel-sage/30 text-text-primary font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="material-icons-round text-[20px] {{ request()->routeIs('profile.*') ? 'filled-icon' : '' }}">person</span>
                    <span class="text-sm">Profil Saya</span>
                </a>

                @if(Auth::user()->role === 'admin')
                    <p class="mb-2 mt-6 px-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Administrator</p>

                    <a href="{{ route('employees.index') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ request()->routeIs('employees.*') ? 'bg-pastel-sky/30 text-text-primary font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span class="material-icons-round text-[20px] {{ request()->routeIs('employees.*') ? 'filled-icon' : '' }}">groups</span>
                        <span class="text-sm">Data Karyawan</span>
                    </a>

                    <a href="{{ route('admin.absence.index') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ request()->routeIs('admin.absence.*') || request()->routeIs('admin.izin.*') ? 'bg-pastel-rose/30 text-text-primary font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span class="material-icons-round text-[20px] {{ request()->routeIs('admin.absence.*') || request()->routeIs('admin.izin.*') ? 'filled-icon' : '' }}">fact_check</span>
                        <span class="text-sm">Approval Izin</span>
                    </a>

                    <a href="{{ route('admin.attendance') }}"
                        class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ request()->routeIs('admin.attendance') || request()->routeIs('admin.attendance.*') ? 'bg-pastel-peach/30 text-text-primary font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span class="material-icons-round text-[20px] {{ request()->routeIs('admin.attendance') || request()->routeIs('admin.attendance.*') ? 'filled-icon' : '' }}">assessment</span>
                        <span class="text-sm">Laporan Absensi</span>
                    </a>
                @endif
            </nav>
        </div>

        <div class="border-t border-slate-100 bg-slate-50/70 p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-bold text-rose-500 transition-all hover:bg-rose-50 hover:text-rose-600">
                    <span class="material-icons-round text-[18px]">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm md:hidden"></div>

    <div class="min-h-screen md:ml-[17.25rem]">
        <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200 bg-white/90 px-6 backdrop-blur-md">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="-ml-2 rounded-xl p-2 text-slate-600 hover:bg-slate-100 md:hidden">
                    <span class="material-icons-round">menu</span>
                </button>
                <div>
                    <p class="text-xs font-medium text-slate-500">@yield('header-subtitle', \Carbon\Carbon::now()->translatedFormat('l, d F Y'))</p>
                    <h2 class="text-lg font-bold text-slate-900">@yield('header-title', 'Dashboard')</h2>
                </div>
            </div>
        </header>

        <main class="p-6 md:p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
