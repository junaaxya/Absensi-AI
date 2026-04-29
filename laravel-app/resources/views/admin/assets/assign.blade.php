@extends('layouts.admin')

@section('header-title', 'Tugaskan Aset')
@section('header-subtitle', 'Tugaskan ' . $asset->name . ' ke karyawan')

@section('content')

    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.assets.show', $asset) }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium">
                <span class="material-icons-round text-lg">arrow_back</span>
                Kembali ke Detail Aset
            </a>
        </div>

        {{-- Asset Summary --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6 mb-6">
            <div class="flex items-center gap-4">
                @if($asset->photo)
                    <img src="{{ asset('storage/' . $asset->photo) }}" alt="{{ $asset->name }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                @else
                    <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <span class="material-icons-round text-2xl text-slate-400">inventory_2</span>
                    </div>
                @endif
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg">{{ $asset->name }}</h3>
                    <p class="text-sm text-slate-500">{{ $asset->asset_code }} &middot; {{ $asset->category->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Assignment Form --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-icons-round text-sky-500">person_add</span>
                Form Penugasan
            </h3>

            @if(session('error'))
                <div class="mb-4 bg-rose-100 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <span class="material-icons-round text-rose-500">error</span>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.assets.assign.store', $asset) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Karyawan <span class="text-rose-500">*</span></label>
                        <select name="user_id" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">
                            <option value="">Pilih Karyawan</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->nip ?? $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan</label>
                        <textarea name="notes" rows="3" placeholder="Catatan penugasan..."
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('admin.assets.show', $asset) }}"
                        class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-sky-500 text-white font-bold text-sm hover:bg-sky-600 transition-all shadow-lg shadow-sky-500/20 active:scale-95">
                        Tugaskan Aset
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
