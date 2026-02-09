<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Presensi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-cream text-text-primary min-h-screen">

    <!-- TOPBAR -->
    <header class="bg-neutral-warm border-b border-neutral-stone/50 sticky top-0 z-50 shadow-soft">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/LW.png') }}" class="w-10 h-10 object-contain" alt="Logo">
                    <span class="font-bold text-lg text-text-primary hidden sm:block">Sistem Presensi</span>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center gap-3 sm:gap-6">
                    <span class="text-sm font-medium text-text-secondary hidden md:block">
                        {{ Auth::user()->name }}
                    </span>
                    
                    <a href="{{ route('profile.edit') }}" 
                       class="px-3 py-1.5 text-sm text-pastel-sage-dark hover:bg-pastel-sage/20 rounded-lg transition font-medium">
                        Profil
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-3 py-1.5 text-sm text-text-secondary hover:text-pastel-rose-dark hover:bg-pastel-rose/20 rounded-lg transition font-medium">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

</body>
</html>
