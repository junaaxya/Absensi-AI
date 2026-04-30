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
        {{-- Header --}}
        <header class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 shadow-sm">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white md:text-4xl">Profil Saya</h1>
            <p class="mt-2 text-base text-slate-500 md:text-lg">Kelola informasi pribadi dan lihat data kepegawaian Anda.</p>
        </header>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- SECTION 1: Informasi Akun (Read-Only Display + Photo) --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm">
            <div class="border-b border-slate-200 dark:border-slate-700 bg-sage/10 px-6 py-4">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white">
                    <span class="material-icons-round text-emerald-700 dark:text-emerald-400">person</span>
                    Informasi Akun
                </h2>
            </div>

            <div class="flex flex-col items-start gap-8 p-8 md:flex-row md:gap-10">
                {{-- Photo Upload --}}
                <div class="flex w-full flex-col items-center gap-3 md:w-auto">
                    <div class="relative">
                        <div class="h-40 w-40 overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700">
                            <img src="{{ $user->profile_photo_url }}" alt="Foto Profil" class="h-full w-full object-cover">
                        </div>
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="absolute -bottom-3 -right-3">
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
                    <label for="uploadFoto" class="cursor-pointer text-sm font-semibold text-slate-600 dark:text-slate-400 transition hover:text-slate-900 dark:hover:text-white">
                        Ubah Foto
                    </label>
                </div>

                {{-- Account Info (Read-Only) --}}
                <div class="grid flex-1 grid-cols-1 gap-x-12 gap-y-5 md:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Nama Lengkap</label>
                        <p class="border-b border-slate-100 dark:border-slate-700 pb-2 text-lg font-medium text-slate-900 dark:text-white">{{ $user->name }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Email</label>
                        <p class="border-b border-slate-100 dark:border-slate-700 pb-2 text-lg font-medium text-slate-900 dark:text-white">{{ $user->email }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Username</label>
                        <p class="border-b border-slate-100 dark:border-slate-700 pb-2 text-lg font-medium font-mono text-slate-900 dark:text-white">{{ $user->username ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Role</label>
                        <p class="border-b border-slate-100 dark:border-slate-700 pb-2 text-lg font-medium text-slate-900 dark:text-white">
                            {{ $user->getRoleNames()->first() ?? 'Karyawan' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- SECTION 2: Data Kepegawaian (Read-Only) --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm">
                <div class="border-b border-slate-200 dark:border-slate-700 bg-sky/10 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white">
                            <span class="material-icons-round text-blue-600 dark:text-blue-400">badge</span>
                            Data Kepegawaian
                        </h2>
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 dark:bg-slate-700 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="material-icons-round text-[12px]">lock</span>
                            Hanya Baca
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <p class="text-xs text-slate-400 leading-relaxed mb-2">
                        Data ini dikelola oleh Admin/HRD. Hubungi HRD jika ada perubahan.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NIK</label>
                            <p class="text-sm font-medium text-slate-900 dark:text-white mt-0.5">{{ $user->nik ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jabatan</label>
                            <p class="text-sm font-medium text-slate-900 dark:text-white mt-0.5">{{ $user->jabatan ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Departemen</label>
                            <p class="text-sm font-medium text-slate-900 dark:text-white mt-0.5">{{ $user->department?->name ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Shift Kerja</label>
                            <p class="text-sm font-medium text-slate-900 dark:text-white mt-0.5">{{ $user->shift?->name ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Karyawan</label>
                            <p class="text-sm font-medium text-slate-900 dark:text-white mt-0.5">
                                @if($user->status_karyawan)
                                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-bold
                                        {{ $user->status_karyawan === 'tetap' ? 'bg-sage/30 text-emerald-700' : ($user->status_karyawan === 'kontrak' ? 'bg-sky/30 text-blue-700' : 'bg-peach/30 text-orange-700') }}">
                                        {{ ucfirst($user->status_karyawan) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Masuk</label>
                            <p class="text-sm font-medium text-slate-900 dark:text-white mt-0.5">{{ $user->tanggal_masuk?->format('d M Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- SECTION 3: Data Pajak & BPJS (Read-Only, Masked) --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm">
                <div class="border-b border-slate-200 dark:border-slate-700 bg-lavender/10 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white">
                            <span class="material-icons-round text-purple-600 dark:text-purple-400">account_balance</span>
                            Pajak & BPJS
                        </h2>
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 dark:bg-slate-700 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <span class="material-icons-round text-[12px]">lock</span>
                            Hanya Baca
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <p class="text-xs text-slate-400 leading-relaxed mb-2">
                        Data sensitif ditampilkan sebagian. Hubungi HRD untuk perubahan.
                    </p>

                    <div class="space-y-3">
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NPWP</label>
                            <p class="text-sm font-medium font-mono text-slate-900 dark:text-white mt-0.5">
                                @if($user->npwp)
                                    {{ substr($user->npwp, 0, 4) }}••••••{{ substr($user->npwp, -4) }}
                                @else
                                    <span class="text-slate-400">Belum diisi</span>
                                @endif
                            </p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">BPJS Kesehatan</label>
                            <p class="text-sm font-medium font-mono text-slate-900 dark:text-white mt-0.5">
                                @if($user->no_bpjs_kesehatan)
                                    {{ substr($user->no_bpjs_kesehatan, 0, 4) }}••••{{ substr($user->no_bpjs_kesehatan, -4) }}
                                @else
                                    <span class="text-slate-400">Belum diisi</span>
                                @endif
                            </p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">BPJS Ketenagakerjaan</label>
                            <p class="text-sm font-medium font-mono text-slate-900 dark:text-white mt-0.5">
                                @if($user->no_bpjs_ketenagakerjaan)
                                    {{ substr($user->no_bpjs_ketenagakerjaan, 0, 4) }}••••{{ substr($user->no_bpjs_ketenagakerjaan, -4) }}
                                @else
                                    <span class="text-slate-400">Belum diisi</span>
                                @endif
                            </p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Pernikahan</label>
                            <p class="text-sm font-medium text-slate-900 dark:text-white mt-0.5">
                                @if($user->status_pernikahan)
                                    {{ $user->status_pernikahan === 'TK' ? 'Tidak Kawin' : 'Kawin' }} / {{ $user->jumlah_tanggungan ?? 0 }} tanggungan
                                @else
                                    <span class="text-slate-400">Belum diisi</span>
                                @endif
                            </p>
                        </div>
                        <div class="rounded-xl bg-slate-50 dark:bg-slate-700/50 p-3">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Gaji Pokok</label>
                            <p class="text-sm font-bold font-mono text-slate-900 dark:text-white mt-0.5">
                                @if($user->gaji_pokok && $user->gaji_pokok > 0)
                                    Rp {{ number_format($user->gaji_pokok, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400">Belum ditetapkan</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- SECTION 4: Data Pribadi (Editable by Employee) --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm">
            <div class="border-b border-slate-200 dark:border-slate-700 bg-peach/10 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white">
                        <span class="material-icons-round text-orange-600 dark:text-orange-400">edit_note</span>
                        Data Pribadi
                    </h2>
                    <span class="inline-flex items-center gap-1 rounded-lg bg-sage/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                        <span class="material-icons-round text-[12px]">edit</span>
                        Bisa Diubah
                    </span>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PATCH')

                <p class="text-xs text-slate-400 leading-relaxed">
                    Anda dapat memperbarui data pribadi berikut. Perubahan akan langsung tersimpan.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-sage focus:ring-sage text-sm px-4 py-3">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-sage focus:ring-sage text-sm px-4 py-3">
                        @error('email')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No. Telepon --}}
                    <div>
                        <label for="no_telepon" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">No. Telepon</label>
                        <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-sage focus:ring-sage text-sm px-4 py-3">
                        @error('no_telepon')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="sm:col-span-2">
                        <label for="alamat" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap Anda"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-sage focus:ring-sage text-sm px-4 py-3">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Kontak Darurat --}}
                <div class="border-t border-slate-100 dark:border-slate-700 pt-6">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                        <span class="material-icons-round text-rose-500 text-[18px]">emergency</span>
                        Kontak Darurat
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Kontak Darurat</label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}"
                                placeholder="Nama orang yang bisa dihubungi"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-sage focus:ring-sage text-sm px-4 py-3">
                            @error('emergency_contact_name')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">No. Telepon Darurat</label>
                            <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
                                value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-sage focus:ring-sage text-sm px-4 py-3">
                            @error('emergency_contact_phone')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-700 pt-6">
                    @if (session('status') === 'profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                            class="text-sm font-medium text-emerald-600 flex items-center gap-1">
                            <span class="material-icons-round text-[16px]">check_circle</span>
                            Data berhasil disimpan!
                        </p>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-900 dark:bg-white px-6 py-3 text-sm font-bold text-white dark:text-slate-900 shadow-sm transition hover:opacity-90">
                        <span class="material-icons-round text-[18px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- SECTION 5: Akun & Keamanan --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm">
            <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 px-6 py-4">
                <h2 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white">
                    <span class="material-icons-round text-slate-500">lock_person</span>
                    Keamanan Akun
                </h2>
            </div>

            <div class="p-6 space-y-3">
                <button x-data @click="$dispatch('open-modal', 'password-modal')"
                    class="flex w-full items-center justify-between rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-left transition hover:bg-slate-50 dark:hover:bg-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-emerald-100 dark:bg-emerald-900/30 p-2 text-emerald-700 dark:text-emerald-400">
                            <span class="material-icons-round text-base">lock</span>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-800 dark:text-white block">Ubah Password</span>
                            <span class="text-xs text-slate-400">Ganti password login Anda</span>
                        </div>
                    </div>
                    <span class="material-icons-round text-slate-400">chevron_right</span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-between rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-left transition hover:bg-rose-100 dark:hover:bg-rose-900/30">
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-white dark:bg-slate-800 p-2 text-rose-600">
                                <span class="material-icons-round text-base">logout</span>
                            </div>
                            <div>
                                <span class="font-semibold text-rose-700 dark:text-rose-400 block">Logout</span>
                                <span class="text-xs text-rose-400">Keluar dari akun Anda</span>
                            </div>
                        </div>
                    </button>
                </form>
            </div>
        </section>

        {{-- Password Modal --}}
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
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
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
