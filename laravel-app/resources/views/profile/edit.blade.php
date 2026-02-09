@extends('layouts.app')

@section('content')
<div class="profile-wrapper">

    <!-- HEADER -->
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
                    <label for="uploadFoto" class="btn-foto">
                        {{ $user->foto ? 'Edit Foto' : 'Pilih Foto' }}
                    </label>
                </form>
            </div>

            <!-- DATA -->
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
            <div class="notif-item">Pengajuan Ketidakhadiran anda divalidasi, cek data presensi</div>
            <div class="notif-item">Pengajuan Ketidakhadiran anda ditolak, Silahkan Absensi seperti biasa</div>
        </div>

        <div class="akun-login">
            <h3>Akun Login</h3>

            <div class="form-group">
                <label>Username</label>
                <input type="text" value="{{ $user->email }}" readonly>
            </div>

            <div class="form-group password-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" value="********" id="passwordField" readonly>
                    <button type="button" onclick="togglePassword()" class="toggle-btn">👁</button>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="profile-footer">
        <div class="footer-left">
            <a href="/dashboard" class="btn-primary">Dashboard</a>
            <button type="button" onclick="openPasswordModal()" class="btn-secondary">🔒 Ubah Password</button>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-danger">Logout</button>
        </form>
    </div>

</div>

<!-- MODAL UBAH PASSWORD -->
<div id="passwordModal" class="modal-overlay">
    <div class="modal-box">

        <div class="modal-header">
            <h3>Ubah Password</h3>
            <span class="modal-close" onclick="closePasswordModal()">✖</span>
        </div>

        <div class="modal-body">

            <!-- RINGKASAN USER -->
            <div class="modal-user-box">
                <div class="user-row">
                     <span class="label">Nama</span>
                    <span class="value">{{ auth()->user()->name }}</span>
                </div>
                 <div class="user-row">
                    <span class="label">Email</span>
                    <span class="value">{{ auth()->user()->email }}</span>
                </div>
            </div>

            <div class="modal-section-title">Reset Password</div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PATCH')

                <div class="modal-field">
                    <label>Password Lama</label>
                    <input type="password" name="current_password" required>
                </div>

                <div class="modal-field">
                    <label>Password Baru</label>
                    <input type="password" name="password" required>
                </div>

                <div class="modal-field">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn-primary">🔒 Ubah Password</button>
                    <button type="button" class="btn-secondary" onclick="closePasswordModal()">✖ Batal</button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- SCRIPT -->
<script>
function togglePassword() {
    const field = document.getElementById("passwordField");
    field.type = field.type === "password" ? "text" : "password";
}

function openPasswordModal() {
    document.getElementById('passwordModal').style.display = 'flex';
}

function closePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
}
</script>

@endsection
