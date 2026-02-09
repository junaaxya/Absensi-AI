<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | Sistem Absensi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-text-primary bg-neutral-cream">
    <div class="min-h-screen flex w-full">
        
        <!-- LEFT SIDE: FORM -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 lg:p-12 relative bg-neutral-cream">
            
            <!-- Decorative Background Elements for Left Side -->
            <div class="absolute top-10 left-10 w-32 h-32 bg-pastel-sage/20 rounded-full blur-3xl mix-blend-multiply filter opacity-70 animate-blob"></div>
            <div class="absolute top-10 right-10 w-32 h-32 bg-pastel-sky/20 rounded-full blur-3xl mix-blend-multiply filter opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-10 left-20 w-32 h-32 bg-pastel-lavender/20 rounded-full blur-3xl mix-blend-multiply filter opacity-70 animate-blob animation-delay-4000"></div>

            <div class="w-full max-w-md bg-neutral-warm p-8 rounded-2xl shadow-soft border border-neutral-stone/50 z-10 relative">
                <div class="mb-6 text-center">
                    <h1 class="text-3xl font-bold text-text-primary mb-2 tracking-tight">Buat Akun Baru</h1>
                    <p class="text-text-secondary text-sm">
                        Daftar untuk mengakses Sistem Absensi <br>
                        <span class="font-semibold text-pastel-sage-dark">Perangkat Desa Bencah</span>
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-text-secondary mb-1.5 ml-1">Nama Lengkap</label>
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                            class="w-full px-4 py-3 rounded-xl bg-white border border-neutral-stone text-text-primary focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition duration-200 placeholder-neutral-muted/70"
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="text-pastel-rose-dark text-sm mt-1 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-text-secondary mb-1.5 ml-1">Email</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                            class="w-full px-4 py-3 rounded-xl bg-white border border-neutral-stone text-text-primary focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition duration-200 placeholder-neutral-muted/70"
                            placeholder="Masukkan email aktif">
                        @error('email')
                            <p class="text-pastel-rose-dark text-sm mt-1 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div x-data="{ show: false }" class="relative">
                        <label for="password" class="block text-sm font-medium text-text-secondary mb-1.5 ml-1">Password</label>
                        <div class="relative">
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                                class="w-full px-4 py-3 rounded-xl bg-white border border-neutral-stone text-text-primary focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition duration-200 placeholder-neutral-muted/70 pr-12"
                                placeholder="Buat password">
                            
                            <!-- Toggle Show/Hide -->
                            <button type="button" @click="show = !show" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer z-10">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        
                        @error('password')
                            <p class="text-pastel-rose-dark text-sm mt-1 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div x-data="{ show: false }" class="relative">
                        <label for="password_confirmation" class="block text-sm font-medium text-text-secondary mb-1.5 ml-1">Konfirmasi Password</label>
                        <div class="relative">
                            <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                                class="w-full px-4 py-3 rounded-xl bg-white border border-neutral-stone text-text-primary focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition duration-200 placeholder-neutral-muted/70 pr-12"
                                placeholder="Ulangi password">
                            
                            <!-- Toggle Show/Hide -->
                            <button type="button" @click="show = !show" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer z-10">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        
                        @error('password_confirmation')
                            <p class="text-pastel-rose-dark text-sm mt-1 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Button -->
                    <div class="pt-2">
                        <button type="submit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-text-primary bg-pastel-sage hover:bg-pastel-sage-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pastel-sage transition duration-200 transform hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98]">
                            Daftar
                        </button>
                    </div>

                    <!-- Link to Login -->
                    <div class="text-center pt-2">
                        <p class="text-sm text-text-secondary">
                            Sudah punya akun? 
                            <a href="{{ route('login') }}" class="font-medium text-pastel-sage-dark hover:text-text-primary transition duration-200 underline decoration-pastel-sage/50 decoration-2 underline-offset-4 hover:decoration-pastel-sage">
                                Masuk di sini
                            </a>
                        </p>
                    </div>
                </form>
            </div>
            
            <p class="absolute bottom-6 text-xs text-neutral-muted text-center font-medium">
                &copy; {{ date('Y') }} Sistem Absensi Desa Bencah
            </p>
        </div>
        
        <!-- RIGHT SIDE: ILLUSTRATION -->
        <div class="hidden lg:flex w-1/2 bg-pastel-sage/10 items-center justify-center relative p-12 overflow-hidden">
            
            <!-- Abstract Background Shapes -->
            <div class="absolute top-0 right-0 w-full h-full bg-gradient-to-bl from-neutral-cream to-pastel-sage/20 opacity-50"></div>
            <div class="absolute top-20 right-20 w-72 h-72 bg-pastel-sage/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
            <div class="absolute bottom-20 left-20 w-72 h-72 bg-pastel-sky/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-pastel-lavender/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>

            <!-- Illustration Container -->
            <div class="relative w-full max-w-lg z-10 flex flex-col items-center">
                 <!-- Abstract Minimalist SVG Illustration for Registration -->
                 <div class="w-full aspect-square relative mb-8 transition-transform duration-700 hover:scale-105">
                     <svg viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-2xl">
                        <defs>
                            <linearGradient id="clipboardGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.9" />
                                <stop offset="100%" style="stop-color:#f5f3f0;stop-opacity:0.8" />
                            </linearGradient>
                        </defs>
                        
                        <!-- Background Circle -->
                        <circle cx="250" cy="250" r="200" class="fill-white/40" />
                        
                        <!-- Clipboard/Form Shape -->
                        <rect x="140" y="80" width="220" height="340" rx="16" fill="url(#clipboardGradient)" class="stroke-white stroke-2 shadow-lg" />
                        
                        <!-- Clip at top -->
                        <rect x="190" y="60" width="120" height="40" rx="8" class="fill-pastel-sage shadow-md" />
                        <circle cx="250" cy="80" r="10" class="fill-white/50" />
                        
                        <!-- Form Lines -->
                        <g transform="translate(0, 40)">
                            <!-- Line 1 -->
                            <rect x="170" y="120" width="160" height="12" rx="6" class="fill-pastel-sky/50" />
                            <rect x="170" y="140" width="100" height="8" rx="4" class="fill-neutral-stone" />
                            
                            <!-- Line 2 -->
                            <rect x="170" y="180" width="160" height="12" rx="6" class="fill-pastel-lavender/50" />
                            <rect x="170" y="200" width="100" height="8" rx="4" class="fill-neutral-stone" />
                            
                            <!-- Line 3 -->
                            <rect x="170" y="240" width="160" height="12" rx="6" class="fill-pastel-peach/50" />
                            <rect x="170" y="260" width="100" height="8" rx="4" class="fill-neutral-stone" />
                        </g>

                        <!-- Person Shape (Abstract) -->
                        <g transform="translate(280, 280)">
                             <circle cx="50" cy="0" r="40" class="fill-pastel-sage shadow-md" />
                             <path d="M10,80 Q50,30 90,80 v20 h-80 z" class="fill-pastel-sage/80" />
                        </g>
                        
                        <!-- Pencil Icon Floating -->
                        <g transform="translate(360, 100) rotate(15)">
                            <path d="M0,0 L20,0 L30,60 L10,60 Z" class="fill-pastel-rose" />
                            <path d="M10,60 L15,75 L20,60" class="fill-text-primary" />
                        </g>

                        <!-- Decorative Elements -->
                        <circle cx="120" cy="400" r="15" class="fill-pastel-sky/60 animate-bounce" style="animation-duration: 3.5s;" />
                        <rect x="400" y="150" width="15" height="15" rx="3" class="fill-pastel-peach/60 animate-pulse" style="animation-duration: 4.5s;" />
                     </svg>
                 </div>
                 
                 <div class="text-center">
                    <h3 class="text-2xl font-bold text-text-primary mb-2">Bergabung Sekarang</h3>
                    <p class="text-text-secondary max-w-xs mx-auto leading-relaxed">
                        Mulai perjalanan produktivitas Anda dengan sistem yang terintegrasi.
                    </p>
                 </div>
            </div>
        </div>
        
    </div>
</body>
</html>
