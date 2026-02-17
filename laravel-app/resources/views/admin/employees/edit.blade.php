@extends('layouts.admin')

@section('header-title', 'Edit Data Karyawan')
@section('header-subtitle', 'Memperbarui Informasi Perangkat Desa')

@section('content')

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-card-dark rounded-2xl p-8 shadow-sm border border-slate-200 dark:border-slate-800">

            <div class="mb-8 pb-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white">Form Edit Karyawan</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Perbarui informasi karyawan di bawah ini.</p>
                </div>
                <button onclick="history.back()"
                    class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="material-icons-round">arrow_back</span>
                </button>
            </div>

            <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nama Lengkap -->
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('name') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Contoh: Budi Santoso" />
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jabatan -->
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $employee->jabatan) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('jabatan') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Contoh: Staff Administrasi" />
                        @error('jabatan')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departemen -->
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Departemen</label>
                        <select name="department_id"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('department_id') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $employee->email) }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('email') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="nama@email.com" />
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username', $employee->username) }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('username') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="username_karyawan" />
                        @error('username')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Role Pengguna</label>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Admin -->
                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="admin" class="peer sr-only" {{ old('role', $employee->role) == 'admin' ? 'checked' : '' }}>
                                <div
                                    class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-sage peer-checked:bg-sage/10 transition-all flex items-center justify-center gap-2">
                                    <div
                                        class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-sage peer-checked:bg-sage">
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Admin</span>
                                </div>
                            </label>
                            <!-- Manager -->
                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="manager" class="peer sr-only" {{ old('role', $employee->role) == 'manager' ? 'checked' : '' }}>
                                <div
                                    class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-sage peer-checked:bg-sage/10 transition-all flex items-center justify-center gap-2">
                                    <div
                                        class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-sage peer-checked:bg-sage">
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Manager</span>
                                </div>
                            </label>
                            <!-- Staf -->
                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="staf" class="peer sr-only" {{ old('role', $employee->role) == 'staf' ? 'checked' : '' }}>
                                <div
                                    class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-sage peer-checked:bg-sage/10 transition-all flex items-center justify-center gap-2">
                                    <div
                                        class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-sage peer-checked:bg-sage">
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Staf</span>
                                </div>
                            </label>
                            <!-- Karyawan -->
                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="karyawan" class="peer sr-only" {{ old('role', $employee->role) == 'karyawan' ? 'checked' : '' }}>
                                <div
                                    class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-sage peer-checked:bg-sage/10 transition-all flex items-center justify-center gap-2">
                                    <div
                                        class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-sage peer-checked:bg-sage">
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Karyawan</span>
                                </div>
                            </label>
                        </div>
                        @error('role')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div
                    class="mb-6 p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <h4 class="font-bold text-slate-900 dark:text-white mb-4">Ubah Password</h4>
                    <p class="text-xs text-slate-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password
                                Baru</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border {{ $errors->has('password') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                                placeholder="********" />
                            @error('password')
                                <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Konfirmasi
                                Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                                placeholder="********" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="history.back()"
                        class="px-6 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 dark:shadow-none active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection