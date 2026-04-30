@extends('layouts.admin')

@section('header-title', 'Tambah Tarif PTKP')
@section('header-subtitle', 'Publikasikan tarif PTKP baru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.rate-management.ptkp') }}"
       class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
        <span class="material-icons-round text-[18px]">arrow_back</span>
        Kembali ke Tarif PTKP
    </a>
</div>

{{-- B8: Form Help --}}
<div class="max-w-2xl mb-6">
    <div class="bg-sky/10 dark:bg-sky/5 border border-sky/30 rounded-xl p-4">
        <div class="flex items-start gap-2.5">
            <span class="material-icons-round text-blue-600 dark:text-blue-400 text-[18px] mt-0.5">info</span>
            <div>
                <p class="text-xs font-bold text-blue-700 dark:text-blue-400 mb-1">Kapan perlu membuat tarif PTKP baru?</p>
                <p class="text-xs text-slate-600 dark:text-slate-400 mb-1">Anda hanya perlu membuat tarif PTKP baru jika pemerintah menerbitkan PMK (Peraturan Menteri Keuangan) baru yang mengubah besaran PTKP.</p>
                <p class="text-xs text-slate-500 italic">Masukkan jumlah PTKP setahun. Nilai ini akan dibagi 12 oleh sistem saat menghitung pajak bulanan.</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <h3 class="font-bold text-slate-800 dark:text-white">Form Tarif PTKP Baru</h3>
            <p class="text-xs text-slate-500 mt-1">Versi sebelumnya akan otomatis ditutup saat tarif baru berlaku.</p>
        </div>

        <form method="POST" action="{{ route('admin.rate-management.ptkp.store') }}" class="p-6 space-y-5">
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
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Berlaku Dari</label>
                <input type="date" name="effective_from" required
                       value="{{ old('effective_from') }}"
                       class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Jumlah PTKP per Status</label>
                <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-slate-500 uppercase tracking-wider bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-4 py-2.5">Status PTKP</th>
                                <th class="px-4 py-2.5">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($statuses as $idx => $status)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-800 dark:text-white">{{ $status }}</span>
                                    <input type="hidden" name="rates[{{ $idx }}][status]" value="{{ $status }}">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                                        <input type="number" name="rates[{{ $idx }}][amount]" step="1" min="0" required
                                               value="{{ old("rates.{$idx}.amount", $currentRates[$status] ?? '') }}"
                                               class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-sm py-2 pl-10 pr-3 focus:border-primary focus:ring-primary">
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-primary text-slate-900 font-semibold text-sm hover:bg-primary/80 shadow-sm transition-colors">
                    Publikasikan Tarif PTKP
                </button>
                <a href="{{ route('admin.rate-management.ptkp') }}"
                   class="px-6 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 font-semibold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
