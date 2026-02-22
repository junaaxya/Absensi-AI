@extends('layouts.employee-dashboard')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Ubah Password</h2>
            <p class="text-slate-500 dark:text-slate-400">Pastikan password Anda aman dan tidak mudah ditebak.</p>
        </div>
        <div class="flex items-center gap-4">
            <button onclick="document.documentElement.classList.toggle('dark')" class="w-10 h-10 rounded-full flex items-center justify-center bg-card-light dark:bg-card-dark border border-slate-200 dark:border-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-800">
                <span class="material-symbols-outlined">dark_mode</span>
            </button>
        </div>
    </div>

    <section class="bg-card-light dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden p-6 md:p-8">
        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
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

            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('profile.edit') }}" class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-primary hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">
                    Simpan Password
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
