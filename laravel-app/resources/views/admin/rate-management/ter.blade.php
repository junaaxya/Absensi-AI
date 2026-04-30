@extends('layouts.admin')

@section('header-title', 'Tarif TER PPh 21')
@section('header-subtitle', 'Kelola tarif efektif rata-rata pajak penghasilan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.rate-management.index') }}"
       class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
        <span class="material-icons-round text-[18px]">arrow_back</span>
        Kembali
    </a>
    <a href="{{ route('admin.rate-management.ter.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-slate-900 font-semibold text-sm hover:bg-primary/80 shadow-sm transition-colors">
        <span class="material-icons-round text-[18px]">add</span>
        Tambah Regulasi Baru
    </a>
</div>

@php
    $today = now()->toDateString();
@endphp

<div x-data="{ openReg: null }" class="space-y-4">
@forelse($regulations as $code => $codeRates)
    @php
        $firstRate = $codeRates->first();
        $isActive = $firstRate->effective_from <= $today && ($firstRate->effective_until === null || $firstRate->effective_until >= $today);
        $grouped = $codeRates->groupBy('category');
    @endphp
    <div class="bg-white dark:bg-card-dark rounded-2xl border shadow-sm overflow-hidden {{ $isActive ? 'border-sage' : 'border-slate-200 dark:border-slate-700' }}">
        <button @click="openReg = openReg === '{{ $code }}' ? null : '{{ $code }}'"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
            <div class="flex items-center gap-3">
                <span class="material-icons-round text-[20px] {{ $isActive ? 'text-green-600' : 'text-slate-400' }}">
                    {{ $isActive ? 'verified' : 'history' }}
                </span>
                <div class="text-left">
                    <h3 class="font-bold text-slate-800 dark:text-white">{{ $code }}</h3>
                    <p class="text-xs text-slate-500">
                        Berlaku: {{ $firstRate->effective_from->format('d/m/Y') }}
                        {{ $firstRate->effective_until ? ' s.d. ' . $firstRate->effective_until->format('d/m/Y') : ' — sekarang' }}
                        &middot; {{ $codeRates->count() }} tarif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($isActive)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sage/30 text-green-800 dark:text-green-300">Aktif</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400">Kadaluarsa</span>
                @endif
                <span class="material-icons-round text-[20px] text-slate-400 transition-transform duration-200"
                      :class="openReg === '{{ $code }}' ? 'rotate-180' : ''">expand_more</span>
            </div>
        </button>

        <div x-show="openReg === '{{ $code }}'" x-collapse x-cloak>
            <div class="border-t border-slate-100 dark:border-slate-800" x-data="{ activeTab: 'A' }">
                <div class="px-6 pt-4 flex gap-2">
                    @foreach(['A', 'B', 'C'] as $cat)
                    <button @click="activeTab = '{{ $cat }}'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                            :class="activeTab === '{{ $cat }}' ? 'bg-lavender/30 text-purple-800 dark:text-purple-300' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'">
                        Kategori {{ $cat }}
                        <span class="ml-1 text-xs text-slate-400">({{ isset($grouped[$cat]) ? $grouped[$cat]->count() : 0 }})</span>
                    </button>
                    @endforeach
                </div>

                @foreach(['A', 'B', 'C'] as $cat)
                <div x-show="activeTab === '{{ $cat }}'" x-cloak class="p-4">
                    @if(isset($grouped[$cat]))
                    <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-slate-500 uppercase tracking-wider bg-slate-50 dark:bg-slate-800/50">
                                    <th class="px-4 py-2.5">Penghasilan Min</th>
                                    <th class="px-4 py-2.5">Penghasilan Maks</th>
                                    <th class="px-4 py-2.5">Tarif</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($grouped[$cat] as $rate)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                    <td class="px-4 py-2">Rp {{ number_format($rate->min_income, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2">{{ $rate->max_income ? 'Rp ' . number_format($rate->max_income, 0, ',', '.') : '∞' }}</td>
                                    <td class="px-4 py-2 font-semibold">{{ rtrim(rtrim(number_format($rate->rate * 100, 4), '0'), '.') }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-sm text-slate-500 text-center py-4">Tidak ada data untuk kategori {{ $cat }}.</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
@empty
<div class="text-center py-12 text-slate-500">
    <span class="material-icons-round text-4xl mb-2">info</span>
    <p>Belum ada data tarif TER.</p>
</div>
@endforelse
</div>
@endsection
