<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Portal Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#10b981", // Emerald-500
                        "background-light": "#f8fafc", // Slate-50
                        "background-dark": "#0f172a", // Slate-900
                        "card-light": "#ffffff",
                        "card-dark": "#1e293b",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "12px",
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-size: 20px; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 min-h-screen" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-cloak></div>

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
               class="fixed inset-y-0 left-0 z-50 w-72 bg-card-light dark:bg-card-dark border-r border-slate-200 dark:border-slate-700 flex flex-col transition-transform duration-300 lg:static lg:block">
            
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">analytics</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-tight">Presensi</p>
                        <h1 class="text-sm font-bold tracking-tight text-slate-800 dark:text-white uppercase">Employee Portal</h1>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-500 hover:text-slate-800 dark:hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="px-4 py-6">
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl flex items-center gap-4 border border-slate-100 dark:border-slate-700/50">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary overflow-hidden">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined">person</span>
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wide truncate">{{ explode(' ', Auth::user()->name)[0] }}</p>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase truncate">{{ Auth::user()->role ?? 'Karyawan' }}</p>
                    </div>
                </div>
            </div>
            
            <nav class="flex-1 px-4 py-2 space-y-1 overflow-y-auto">
                <p class="px-4 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menu Utama</p>
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}" href="{{ route('dashboard') }}">
                    <span class="material-symbols-outlined">dashboard</span>
                    Dashboard
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('izin.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}" href="{{ route('izin.index') }}">
                    <span class="material-symbols-outlined">event_busy</span>
                    Izin & Cuti
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('profile.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}" href="{{ route('profile.edit') }}">
                    <span class="material-symbols-outlined">account_circle</span>
                    Profil Saya
                </a>

                @if(Auth::user()->role === 'admin')
                    <p class="px-4 py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Admin</p>
                    <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800" href="{{ route('admin.dashboard') }}">
                        <span class="material-symbols-outlined">admin_panel_settings</span>
                        Admin Panel
                    </a>
                @endif
            </nav>
            
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-colors">
                        <span class="material-symbols-outlined">logout</span>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto scroll-smooth flex flex-col relative w-full">
            
            <!-- Mobile Header -->
            <header class="lg:hidden flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-700 bg-card-light dark:bg-card-dark sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-sm">analytics</span>
                    </div>
                    <h1 class="text-sm font-bold tracking-tight text-slate-800 dark:text-white uppercase">Presensi</h1>
                </div>
                <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-800 dark:hover:text-white">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </header>

            <div class="p-6 md:p-8 max-w-7xl mx-auto w-full space-y-8 pb-24">
                
                <!-- Toast Notifications (reusing Alpine logic) -->
                @if(session('success') || session('error') || $errors->any())
                    <div x-data="{
                        toasts: [],
                        init() {
                            @if(session('success')) this.addToast('success', '{{ session('success') }}'); @endif
                            @if(session('error')) this.addToast('error', '{{ session('error') }}'); @endif
                            @if($errors->any()) @foreach($errors->all() as $error) this.addToast('error', '{{ $error }}'); @endforeach @endif
                        },
                        addToast(type, message) {
                            const id = Date.now();
                            this.toasts.push({ id, type, message });
                            setTimeout(() => this.removeToast(id), 5000);
                        },
                        removeToast(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
                    }" class="fixed top-4 right-4 z-[100] flex w-full max-w-sm flex-col gap-3 px-4 md:px-0">
                        <template x-for="toast in toasts" :key="toast.id">
                            <div x-show="true" class="relative cursor-pointer overflow-hidden rounded-xl border p-4 shadow-sm backdrop-blur-md"
                                :class="toast.type === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/50 border-emerald-200 dark:border-emerald-800' : 'bg-red-50 dark:bg-red-900/50 border-red-200 dark:border-red-800'"
                                @click="removeToast(toast.id)">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined mt-0.5" :class="toast.type === 'success' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'" x-text="toast.type === 'success' ? 'check_circle' : 'error'"></span>
                                    <div>
                                        <p class="text-sm font-bold" :class="toast.type === 'success' ? 'text-emerald-800 dark:text-emerald-300' : 'text-red-800 dark:text-red-300'" x-text="toast.type === 'success' ? 'Berhasil!' : 'Gagal!'"></p>
                                        <p class="mt-0.5 text-sm" :class="toast.type === 'success' ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400'" x-text="toast.message"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
