<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Presensi</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

<header class="header">
<div class="topbar">
    <!-- KIRI -->
    <div class="logo">
        <img src="{{ asset('images/LW.png') }}" class="logo-img">
        <span class="logo-text">Sistem Presensi</span>
    </div>

    <!-- KANAN -->
    <div class="user-info">
        <span class="user-name">{{ Auth::user()->name }}</span>
        <a href="{{ route('profile.edit') }}" class="profile-link">
            Lihat Profil
        </a>
    </div>
</div>

</header>
</header>

<main class="container">
    @yield('content')
</main>

</body>
</html>
