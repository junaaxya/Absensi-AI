@extends('layouts.absensi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER CARD -->
    <div class="bg-gradient-to-r from-pastel-sage/20 to-pastel-sky/20 p-8 rounded-2xl border border-neutral-stone/30 flex flex-col md:flex-row items-center gap-6 shadow-sm">
        <!-- Avatar -->
        <div class="relative group shrink-0">
             <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-soft bg-white">
                <img src="{{ $user->foto ? asset('storage/'.$user->foto) : asset('img/default.png') }}" class="w-full h-full object-cover" alt="Avatar">
             </div>
             <!-- Photo Upload Form (Hidden input + Label) -->
             <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                @csrf @method('PATCH')
                <input type="file" name="foto" id="uploadFoto" hidden onchange="this.form.submit()">
                <label for="uploadFoto" class="block w-8 h-8 bg-pastel-sky hover:bg-pastel-sky-dark text-blue-700 rounded-full flex items-center justify-center cursor-pointer shadow-sm transition transform hover:scale-105 border-2 border-white" title="Ganti Foto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </label>
             </form>
        </div>
        
        <!-- Name & Role -->
        <div class="text-center md:text-left">
            <h2 class="text-2xl font-bold text-text-primary">{{ $user->name }}</h2>
            <p class="text-text-secondary">{{ $user->email }}</p>
            <div class="mt-2 flex flex-wrap justify-center md:justify-start gap-2">
                <x-pastel-badge type="info">{{ $user->role ?? 'Karyawan' }}</x-pastel-badge>
                @if($user->jabatan)
                    <x-pastel-badge type="warning">{{ $user->jabatan }}</x-pastel-badge>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- ACCOUNT INFO -->
        <x-pastel-card header="Informasi Akun">
            <dl class="space-y-4 divide-y divide-neutral-stone/30">
                <div class="pt-2 flex justify-between">
                    <dt class="text-sm font-medium text-text-secondary">Nama Lengkap</dt>
                    <dd class="text-sm text-text-primary font-medium">{{ $user->name }}</dd>
                </div>
                <div class="pt-4 flex justify-between">
                    <dt class="text-sm font-medium text-text-secondary">Email</dt>
                    <dd class="text-sm text-text-primary font-medium">{{ $user->email }}</dd>
                </div>
                <div class="pt-4 flex justify-between">
                    <dt class="text-sm font-medium text-text-secondary">Role</dt>
                    <dd class="text-sm text-text-primary font-medium">{{ $user->role ?? 'Karyawan' }}</dd>
                </div>
                <div class="pt-4 flex justify-between">
                    <dt class="text-sm font-medium text-text-secondary">Jabatan</dt>
                    <dd class="text-sm text-text-primary font-medium">{{ $user->jabatan ?? '-' }}</dd>
                </div>
            </dl>
        </x-pastel-card>

        <!-- SECURITY & ACTIONS -->
        <div class="space-y-6">
            <x-pastel-card header="Keamanan & Aksi">
                <div class="space-y-4">
                    <button x-data @click="$dispatch('open-modal', 'password-modal')" class="w-full py-3 px-4 bg-white border border-neutral-stone hover:bg-neutral-stone/10 rounded-xl flex items-center justify-between transition group shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-pastel-sage/20 rounded-lg text-pastel-sage-dark">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <span class="font-medium text-text-primary">Ubah Password</span>
                        </div>
                        <span class="text-text-secondary group-hover:translate-x-1 transition">→</span>
                    </button>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-3 px-4 bg-pastel-rose/10 hover:bg-pastel-rose/20 text-pastel-rose-dark border border-pastel-rose/20 rounded-xl flex items-center justify-between transition group shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                </div>
                                <span class="font-medium">Keluar Aplikasi</span>
                            </div>
                        </button>
                    </form>
                </div>
            </x-pastel-card>

            <!-- NOTIFICATIONS -->
            <x-pastel-card header="Notifikasi">
                <div class="space-y-3">
                    <div class="bg-pastel-sky/10 border border-pastel-sky/20 rounded-lg p-3 text-sm text-text-primary flex gap-3">
                        <span class="text-blue-500 text-lg">ℹ️</span>
                        <span>Pengajuan Ketidakhadiran anda divalidasi, cek data presensi</span>
                    </div>
                    <div class="bg-pastel-rose/10 border border-pastel-rose/20 rounded-lg p-3 text-sm text-text-primary flex gap-3">
                        <span class="text-red-500 text-lg">⚠️</span>
                        <span>Pengajuan Ketidakhadiran anda ditolak, Silahkan Absensi seperti biasa</span>
                    </div>
                </div>
            </x-pastel-card>
        </div>
    </div>

    <!-- PASSWORD MODAL -->
    <x-pastel-modal name="password-modal" title="Ubah Password">
        <form method="POST" action="{{ route('password.update') }}" class="p-6">
            @csrf @method('PATCH')
            
            <div class="space-y-4">
                 <div>
                    <x-input-label for="current_password" value="Password Lama" />
                    <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
                </div>

                 <div>
                    <x-input-label for="password" value="Password Baru" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>

                 <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                    <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <x-secondary-button x-on:click="$dispatch('close-modal', 'password-modal')">
                    Batal
                </x-secondary-button>
                <x-primary-button>
                    Simpan Password
                </x-primary-button>
            </div>
        </form>
    </x-pastel-modal>

</div>
@endsection
