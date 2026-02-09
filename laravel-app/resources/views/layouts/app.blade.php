<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Presensi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <!-- HEADER -->
    <div class="topbar">
        <div class="logo">
            <img src="{{ asset('images/LW.png') }}" class="logo-img">
            <span class="logo-text">Sistem Presensi</span>
        </div>
    </div>

    <!-- CONTENT -->
    <main class="container">
        @yield('content')
    </main>

</body>
</html>
