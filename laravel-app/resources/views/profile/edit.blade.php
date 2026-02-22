@extends('layouts.employee-dashboard')

@section('content')
    <div x-data="{}" class="mx-auto max-w-6xl space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Profil Saya</h2>
                <p class="text-slate-500 dark:text-slate-400">Atur profil sesuai dengan identitas Anda.</p>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="document.documentElement.classList.toggle('dark')" class="w-10 h-10 rounded-full flex items-center justify-center bg-card-light dark:bg-card-dark border border-slate-200 dark:border-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-800">
                    <span class="material-symbols-outlined">dark_mode</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <section class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-card-light dark:bg-card-dark shadow-sm lg:col-span-3">
                <div class="border-b border-slate-200 dark:border-slate-700 bg-emerald-50 dark:bg-emerald-500/10 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white">
                        <span class="material-symbols-outlined text-primary">person</span>
                        Informasi Akun
                    </h2>
                </div>

                <div class="flex flex-col items-start gap-8 p-8 md:flex-row md:gap-10">
                    <div class="flex w-full flex-col items-center gap-3 md:w-auto">
                        <div class="relative">
                            <div class="h-40 w-40 overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800">
                                <img src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('img/default.png') }}" alt="Foto Profil"
                                    class="h-full w-full object-cover">
                            </div>

                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
                                class="absolute -bottom-3 -right-3">
                                @csrf
                                @method('PATCH')
                                <input type="file" name="foto" id="uploadFoto" hidden onchange="this.form.submit()">
                                <label for="uploadFoto"
                                    class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border-2 border-white dark:border-slate-800 bg-slate-900 dark:bg-slate-700 text-white shadow-md transition hover:bg-slate-700 dark:hover:bg-slate-600"
                                    title="Pilih Foto">
                                    <span class="material-symbols-outlined text-base">add_a_photo</span>
                                </label>
                            </form>
                        </div>

                        <label for="uploadFoto" class="cursor-pointer text-sm font-semibold text-slate-600 dark:text-slate-400 transition hover:text-slate-900 dark:hover:text-white">
                            Pilih Foto
                        </label>
                    </div>

                    <div class="grid flex-1 grid-cols-1 gap-x-12 gap-y-6 md:grid-cols-2">
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Nama Lengkap</label>
                            <p class="border-b border-slate-100 dark:border-slate-700 pb-2 text-lg font-medium text-slate-900 dark:text-white">{{ $user->name }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Email</label>
                            <p class="border-b border-slate-100 dark:border-slate-700 pb-2 text-lg font-medium text-slate-900 dark:text-white">{{ $user->email }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Role</label>
                            <p class="border-b border-slate-100 dark:border-slate-700 pb-2 text-lg font-medium text-slate-900 dark:text-white">
                                {{ ucfirst($user->role ?? 'karyawan') }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jabatan</label>
                            <p class="border-b border-slate-100 dark:border-slate-700 pb-2 text-lg font-medium text-slate-900 dark:text-white">
                                {{ $user->jabatan ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="h-full overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-card-light dark:bg-card-dark shadow-sm lg:col-span-2">
                <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white">
                        <span class="material-symbols-outlined text-slate-500">notifications</span>
                        Notifikasi Masuk
                    </h2>
                </div>

                <div class="space-y-4 p-6">
                    <div class="flex gap-3 rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 p-4">
                        <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">check_circle</span>
                        <p class="text-sm text-slate-700 dark:text-slate-300">Pengajuan Ketidakhadiran anda di validasi, cek data presensi.</p>
                    </div>
                    <div class="flex gap-3 rounded-xl border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 p-4">
                        <span class="material-symbols-outlined text-red-500 dark:text-red-400">cancel</span>
                        <p class="text-sm text-slate-700 dark:text-slate-300">Pengajuan Ketidakhadiran anda di Tolak, Silahkan Absensi seperti biasa.</p>
                    </div>
                </div>
            </section>

            <section class="h-full overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-card-light dark:bg-card-dark shadow-sm">
                <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white">
                        <span class="material-symbols-outlined text-slate-500">lock_person</span>
                        Akun Login
                    </h2>
                </div>

                <div class="space-y-6 p-6">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Username</label>
                        <p class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-3 font-mono text-lg text-slate-900 dark:text-white">
                            {{ $user->username ?? '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Password</label>
                        <p class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-3 font-mono text-lg tracking-tight text-slate-900 dark:text-white">
                            ••••••••••••
                        </p>
                    </div>

                    <div class="space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                        <button @click="$dispatch('open-modal', 'password-modal')"
                            class="flex w-full items-center justify-between rounded-xl border border-slate-200 dark:border-slate-700 bg-card-light dark:bg-card-dark px-4 py-3 text-left transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-emerald-100 dark:bg-emerald-500/20 p-2 text-emerald-700 dark:text-emerald-400">
                                    <span class="material-symbols-outlined text-base">lock</span>
                                </div>
                                <span class="font-semibold text-slate-800 dark:text-white">Ubah Password</span>
                            </div>
                            <span class="material-symbols-outlined text-slate-500 dark:text-slate-400">chevron_right</span>
                        </button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center justify-between rounded-xl border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 px-4 py-3 text-left transition hover:bg-red-100 dark:hover:bg-red-500/20">
                                <div class="flex items-center gap-3">
                                    <div class="rounded-lg bg-white dark:bg-slate-800 p-2 text-red-600 dark:text-red-400">
                                        <span class="material-symbols-outlined text-base">logout</span>
                                    </div>
                                    <span class="font-semibold text-red-700 dark:text-red-400">Logout</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        <!-- Password Modal (Alpine) -->
        <div x-data="{ show: false }"
            @open-modal.window="if ($event.detail === 'password-modal') { show = true }"
            @close-modal.window="if ($event.detail === 'password-modal') { show = false }"
            x-show="show" x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/80 p-4 backdrop-blur-sm">
            
            <div x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.outside="$dispatch('close-modal', 'password-modal')"
                class="relative w-full max-w-lg transform overflow-hidden rounded-3xl bg-card-light dark:bg-card-dark text-left shadow-xl transition-all border border-slate-200 dark:border-slate-700">
                
                <div class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Ubah Password</h3>
                    <button @click="$dispatch('close-modal', 'password-modal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4 p-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Password Lama</label>
                        <input id="current_password" name="current_password" type="password" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm" required autofocus />
                        <x-input-error class="mt-2 text-sm text-red-600 dark:text-red-400" :messages="$errors->get('current_password')" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Password Baru</label>
                        <input id="password" name="password" type="password" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm" required />
                        <x-input-error class="mt-2 text-sm text-red-600 dark:text-red-400" :messages="$errors->get('password')" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Password Baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm" required />
                        <x-input-error class="mt-2 text-sm text-red-600 dark:text-red-400" :messages="$errors->get('password_confirmation')" />
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="$dispatch('close-modal', 'password-modal')"
                            class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
