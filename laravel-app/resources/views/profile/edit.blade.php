@extends('layouts.absensi')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        .profile-page {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
@endpush

@section('content')
    <div class="profile-page mx-auto max-w-6xl space-y-6 pb-16">
        <header class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">Profil</h1>
            <p class="mt-2 text-base text-slate-500 md:text-lg">Atur profil sesuai dengan identitas anda!</p>
        </header>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-3">
                <div class="border-b border-slate-200 bg-emerald-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800">
                        <span class="material-icons-round text-emerald-700">person</span>
                        Informasi Akun
                    </h2>
                </div>

                <div class="flex flex-col items-start gap-8 p-8 md:flex-row md:gap-10">
                    <div class="flex w-full flex-col items-center gap-3 md:w-auto">
                        <div class="relative">
                            <div class="h-40 w-40 overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 bg-slate-100">
                                <img src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('img/default.png') }}" alt="Foto Profil"
                                    class="h-full w-full object-cover">
                            </div>

                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
                                class="absolute -bottom-3 -right-3">
                                @csrf
                                @method('PATCH')
                                <input type="file" name="foto" id="uploadFoto" hidden onchange="this.form.submit()">
                                <label for="uploadFoto"
                                    class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border-2 border-white bg-slate-900 text-white shadow-md transition hover:bg-slate-700"
                                    title="Pilih Foto">
                                    <span class="material-icons-round text-base">add_a_photo</span>
                                </label>
                            </form>
                        </div>

                        <label for="uploadFoto" class="cursor-pointer text-sm font-semibold text-slate-600 transition hover:text-slate-900">
                            Pilih Foto
                        </label>
                    </div>

                    <div class="grid flex-1 grid-cols-1 gap-x-12 gap-y-6 md:grid-cols-2">
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Nama Lengkap</label>
                            <p class="border-b border-slate-100 pb-2 text-lg font-medium text-slate-900">{{ $user->name }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Email</label>
                            <p class="border-b border-slate-100 pb-2 text-lg font-medium text-slate-900">{{ $user->email }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Role</label>
                            <p class="border-b border-slate-100 pb-2 text-lg font-medium text-slate-900">
                                {{ ucfirst($user->role ?? 'karyawan') }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Jabatan</label>
                            <p class="border-b border-slate-100 pb-2 text-lg font-medium text-slate-900">
                                {{ $user->jabatan ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="h-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800">
                        <span class="material-icons-round text-slate-500">notifications</span>
                        Notifikasi Masuk
                    </h2>
                </div>

                <div class="space-y-4 p-6">
                    <div class="flex gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <span class="material-icons-round text-emerald-600">check_circle</span>
                        <p class="text-sm text-slate-700">Pengajuan Ketidakhadiran anda di validasi, cek data presensi.</p>
                    </div>
                    <div class="flex gap-3 rounded-xl border border-red-200 bg-red-50 p-4">
                        <span class="material-icons-round text-red-500">cancel</span>
                        <p class="text-sm text-slate-700">Pengajuan Ketidakhadiran anda di Tolak, Silahkan Absensi seperti biasa.</p>
                    </div>
                </div>
            </section>

            <section class="h-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800">
                        <span class="material-icons-round text-slate-500">lock_person</span>
                        Akun Login
                    </h2>
                </div>

                <div class="space-y-6 p-6">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Username</label>
                        <p class="rounded-lg border border-slate-200 bg-slate-50 p-3 font-mono text-lg text-slate-900">
                            {{ $user->username ?? '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Password</label>
                        <p class="rounded-lg border border-slate-200 bg-slate-50 p-3 font-mono text-lg tracking-tight text-slate-900">
                            ••••••••••••
                        </p>
                    </div>

                    <div class="space-y-3 border-t border-slate-100 pt-2">
                        <button x-data @click="$dispatch('open-modal', 'password-modal')"
                            class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-left transition hover:bg-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-emerald-100 p-2 text-emerald-700">
                                    <span class="material-icons-round text-base">lock</span>
                                </div>
                                <span class="font-semibold text-slate-800">Ubah Password</span>
                            </div>
                            <span class="material-icons-round text-slate-500">chevron_right</span>
                        </button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-left transition hover:bg-rose-100">
                                <div class="flex items-center gap-3">
                                    <div class="rounded-lg bg-white p-2 text-rose-600">
                                        <span class="material-icons-round text-base">logout</span>
                                    </div>
                                    <span class="font-semibold text-rose-700">Logout</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        <x-pastel-modal name="password-modal" title="Ubah Password">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4 p-6">
                @csrf
                @method('PATCH')

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
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full"
                        required />
                    <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                </div>

                <div class="flex justify-end gap-3 pt-2">
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
