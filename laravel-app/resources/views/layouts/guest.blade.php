<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Absensi') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-text-primary bg-neutral-cream">
    <div class="min-h-screen flex flex-col justify-center items-center p-6 relative">
        <!-- Decorative blobs -->
        <div class="absolute top-10 left-10 w-32 h-32 bg-pastel-sage/20 rounded-full blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-pastel-sky/20 rounded-full blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-pastel-lavender/20 rounded-full blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
        
        <!-- Card -->
        <div class="w-full max-w-md bg-neutral-warm p-8 rounded-2xl shadow-soft border border-neutral-stone/50 z-10">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
