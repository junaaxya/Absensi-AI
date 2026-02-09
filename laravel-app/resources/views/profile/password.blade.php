@extends('layouts.app')

@section('content')
<div class="profile-wrapper">

    <div class="profile-header">
        <h2>Ubah Password</h2>
        <p>Ubah Password Akun Karyawan</p>
    </div>

    <div class="profile-section">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PATCH')

            <p>
                <label>Password Lama</label><br>
                <input type="password" name="current_password" required>
                @error('current_password') <small style="color:red">{{ $message }}</small> @enderror
            </p>

            <p>
                <label>Password Baru</label><br>
                <input type="password" name="password" required>
            </p>

            <p>
                <label>Konfirmasi Password Baru</label><br>
                <input type="password" name="password_confirmation" required>
            </p>

            <div class="profile-footer">
                <button type="submit" class="btn-primary">🔒 Ubah Password</button>
                <a href="{{ route('profile.edit') }}" class="btn-secondary">✖ Batal</a>
            </div>
        </form>
    </div>

</div>
@endsection
