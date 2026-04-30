@extends('layouts.admin')

@section('header-title', 'Tambah Tarif BPJS')
@section('header-subtitle', 'Buat versi tarif BPJS baru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.rate-management.bpjs') }}"
       class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
        <span class="material-icons-round text-[18px]">arrow_back</span>
        Kembali ke Tarif BPJS
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <h3 class="font-bold text-slate-800 dark:text-white">Form Tarif BPJS Baru</h3>
            <p class="text-xs text-slate-500 mt-1">Versi sebelumnya akan otomatis ditutup saat tarif baru berlaku.</p>
        </div>

        <form method="POST" action="{{ route('admin.rate-management.bpjs.store') }}" class="p-6 space-y-5">
            @csrf

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Program BPJS</label>
                <select name="program" required
                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">
                    <option value="">Pilih Program</option>
                    @foreach($programs as $p)
                        <option value="{{ $p }}" {{ old('program') === $p ? 'selected' : '' }}>
                            {{ strtoupper(str_replace('_', ' ', $p)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Iuran Perusahaan</label>
                    <div class="relative">
                        <input type="number" name="employer_rate" step="0.00001" min="0" max="1" required
                               value="{{ old('employer_rate') }}"
                               placeholder="0.037"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm pr-8">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">desimal</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Contoh: 0.037 = 3.7%</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Iuran Karyawan</label>
                    <div class="relative">
                        <input type="number" name="employee_rate" step="0.00001" min="0" max="1" required
                               value="{{ old('employee_rate') }}"
                               placeholder="0.02"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm pr-8">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">desimal</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Contoh: 0.02 = 2%</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Maks. Dasar Gaji</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                        <input type="number" name="max_salary_basis" step="1" min="0"
                               value="{{ old('max_salary_basis') }}"
                               placeholder="10042300"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm pl-10">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Min. Dasar Gaji</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                        <input type="number" name="min_salary_basis" step="1" min="0"
                               value="{{ old('min_salary_basis') }}"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm pl-10">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Berlaku Dari</label>
                <input type="date" name="effective_from" required
                       value="{{ old('effective_from') }}"
                       class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Catatan</label>
                <textarea name="notes" rows="3"
                          placeholder="Catatan perubahan tarif..."
                          class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-primary text-slate-900 font-semibold text-sm hover:bg-primary/80 shadow-sm transition-colors">
                    Simpan Tarif
                </button>
                <a href="{{ route('admin.rate-management.bpjs') }}"
                   class="px-6 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 font-semibold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
