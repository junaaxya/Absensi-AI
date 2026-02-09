<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Sistem Absensi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex w-full">
        
        <!-- LEFT SIDE: FORM (Gray Background) -->
        <div class="w-full lg:w-1/2 bg-[#E5E5E5] flex flex-col justify-center items-center p-8 relative">
            
            <!-- DECORATION CIRCLES (Optional, based on previous design, but maybe not in new one. Keeping clean for now) -->
            
            <div class="w-full max-w-md">
                <h1 class="text-4xl font-extrabold text-black mb-2">MASUK</h1>
                <p class="text-gray-600 mb-8 text-lg">
                    Selamat Datang di Sistem Absensi <br>
                    Perangkat Desa Bencah
                </p>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email/Username -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Username / Email</label>
                        <input id="email" type="text" name="email" :value="old('email')" required autofocus
                            class="w-full px-4 py-3 rounded-lg bg-[#D9D9D9] border-transparent focus:border-gray-500 focus:bg-white focus:ring-0 transition duration-200"
                            placeholder="">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div x-data="{ show: false }" class="relative">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required
                            class="w-full px-4 py-3 rounded-lg bg-[#D9D9D9] border-transparent focus:border-gray-500 focus:bg-white focus:ring-0 transition duration-200"
                            placeholder="">
                        
                        <!-- Toggle Show/Hide -->
                        <button type="button" @click="show = !show" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center mt-6 text-gray-600 hover:text-gray-800">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                        
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Button -->
                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-black bg-[#D9D9D9] hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 border-2 border-black transition duration-200">
                            Masuk
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Corner Decorations -->
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-gray-300 rounded-tr-full opacity-50 mix-blend-multiply filter blur-xl"></div>
            <div class="absolute top-0 left-0 w-24 h-24 bg-gray-200 rounded-br-full opacity-50"></div>
        </div>
        
        <!-- RIGHT SIDE: ILLUSTRATION (White Background) -->
        <div class="hidden lg:flex w-1/2 bg-white items-center justify-center relative p-12">
            <!-- Placeholder illustration using CSS/SVG since we don't have the asset -->
            <div class="relative w-full max-w-lg aspect-square">
                 <!-- Simple Office Illustration SVG Placeholder -->
                 <svg viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-blue-500">
                    <circle cx="250" cy="250" r="200" fill="#EBF4FF" />
                    <!-- Minimalist Desk/Person -->
                    <rect x="150" y="300" width="200" height="10" fill="#3B82F6" />
                    <rect x="170" y="310" width="20" height="40" fill="#3B82F6" />
                    <rect x="310" y="310" width="20" height="40" fill="#3B82F6" />
                    <rect x="200" y="240" width="100" height="60" rx="4" fill="#60A5FA" />
                    <circle cx="250" cy="180" r="30" fill="#1E40AF" />
                    <path d="M220 230 C220 230, 250 210, 280 230 L280 300 L220 300 Z" fill="#2563EB" />
                 </svg>
            </div>
             <!-- Decorative elements -->
             <div class="absolute top-10 right-10 w-20 h-20 bg-blue-100 rounded-full blur-xl"></div>
             <div class="absolute bottom-10 right-20 w-32 h-32 bg-blue-50 rounded-full blur-2xl"></div>
        </div>
        
    </div>
</body>
</html>
