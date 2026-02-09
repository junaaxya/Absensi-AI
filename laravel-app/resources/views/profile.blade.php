@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<div class="profile-wrapper">

    <!-- HEADER PROFIL -->
    <div class="profile-header">
        <h2>Profil</h2>
        <p>Atur profil sesuai dengan identitas anda!</p>
    </div>

    <!-- INFORMASI AKUN -->
    <div class="profile-section">
        <h3>Informasi Akun</h3>
        <div class="info-akun">

            <!-- FOTO -->
            <div class="foto-box">
                <div class="foto-preview">
                    <img src="{{ $user->foto ? asset('storage/'.$user->foto) : asset('img/default.png') }}">
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <input type="file" name="foto" id="uploadFoto" hidden onchange="this.form.submit()">
                    <label for="uploadFoto" class="btn-foto">Pilih Foto</label>
                </form>
            </div>

            <!-- DATA USER -->
            <div class="data-akun">
                <p><b>Nama Lengkap</b> : {{ $user->name }}</p>
                <p><b>Email</b> : {{ $user->email }}</p>
                <p><b>Role</b> : {{ $user->role ?? 'Karyawan' }}</p>
                <p><b>Jabatan</b> : {{ $user->jabatan ?? '-' }}</p>
            </div>

        </div>
    </div>

    <!-- BAGIAN BAWAH -->
    <div class="profile-bottom">

        <div class="notifikasi">
            <h3>Notifikasi Masuk</h3>
            <p>Pengajuan Ketidakhadiran anda divalidasi, cek data presensi</p>
            <p>Pengajuan Ketidakhadiran anda ditolak, Silahkan Absensi seperti biasa</p>
        </div>

        <div class="akun-login">
            <h3>Akun Login</h3>
            <p><b>Username</b> : {{ $user->email }}</p>
            <p><b>Password</b> : ********</p>
        </div>

    </div>

    <!-- FOOTER BUTTON -->
    <div class="profile-footer">
        <a href="/dashboard" class="btn-primary">⬅ Kembali Ke Dashboard</a>
        <a href="/profile" class="btn-secondary">Ubah Password</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-danger">Logout</button>
        </form>
    </div>

</div>
@endsection
