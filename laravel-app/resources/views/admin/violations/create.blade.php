@extends('layouts.admin')

@section('header-title', 'Tambah Pelanggaran')
@section('header-subtitle', 'Catat pelanggaran manual untuk karyawan')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.violations.index') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all">
                <span class="material-icons-round text-base">arrow_back</span>
                Kembali ke Daftar Pelanggaran
            </a>
        </div>

        <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Pelanggaran Manual</h3>
                <div class="w-10 h-10 rounded-full bg-peach/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                    <span class="material-icons-round">gavel</span>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.violations.store') }}" method="POST"
                x-data="{
                    selectedType: '',
                    types: @js($violationTypes->keyBy('id')->map(fn($t) => $t->points)),
                    updatePoints() {
                        if (this.selectedType && this.types[this.selectedType] !== undefined) {
                            this.$refs.pointsInput.value = this.types[this.selectedType];
                        }
                    }
                }">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Karyawan</label>
                        <select name="user_id" required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold">
                            <option value="">Pilih Karyawan</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} — {{ $user->jabatan ?? 'Tanpa Jabatan' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Pelanggaran</label>
                        <select name="violation_type_id" required
                            x-model="selectedType"
                            @change="updatePoints()"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold">
                            <option value="">Pilih Jenis Pelanggaran</option>
                            @foreach($violationTypes as $type)
                                <option value="{{ $type->id }}" {{ old('violation_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->points }} poin)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Poin</label>
                            <input type="number" name="points" x-ref="pointsInput" value="{{ old('points', 0) }}" min="0" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                            <p class="text-xs text-slate-400 mt-1">Otomatis terisi dari jenis, bisa diedit untuk MANUAL.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan</label>
                        <textarea name="notes" rows="3" placeholder="Keterangan pelanggaran..."
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white resize-none">{{ old('notes') }}</textarea>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <a href="{{ route('admin.violations.index') }}"
                            class="flex-1 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-center text-sm">
                            Batal
                        </a>
                        <button type="submit"
                            class="flex-1 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95 text-sm">
                            Simpan Pelanggaran
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
