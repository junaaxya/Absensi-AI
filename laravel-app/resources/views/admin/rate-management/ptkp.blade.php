@extends('layouts.admin')

@section('header-title', 'Tarif PTKP')
@section('header-subtitle', 'Kelola Penghasilan Tidak Kena Pajak')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.rate-management.index') }}"
       class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
        <span class="material-icons-round text-[18px]">arrow_back</span>
        Kembali
    </a>
    <a href="{{ route('admin.rate-management.ptkp.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-slate-900 font-semibold text-sm hover:bg-primary/80 shadow-sm transition-colors">
        <span class="material-icons-round text-[18px]">add</span>
        Tambah Tarif PTKP Baru
    </a>
</div>

{{-- B7: Status Explanation --}}
<div class="mb-6 bg-peach/10 dark:bg-peach/5 border border-peach/30 rounded-xl p-5">
    <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg bg-peach/30 flex items-center justify-center flex-shrink-0">
            <span class="material-icons-round text-orange-600 dark:text-orange-400 text-[18px]">school</span>
        </div>
        <div>
            <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-1.5">Tentang PTKP</h4>
            <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">PTKP (Penghasilan Tidak Kena Pajak) adalah batas penghasilan yang tidak dikenakan pajak. Besarannya tergantung status pernikahan dan jumlah tanggungan karyawan.</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
                <div class="bg-white dark:bg-slate-800/50 rounded-lg p-2 border border-peach/20 text-center">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">TK/0</p>
                    <p class="text-[10px] text-slate-500">Belum kawin, tanpa tanggungan</p>
                </div>
                <div class="bg-white dark:bg-slate-800/50 rounded-lg p-2 border border-peach/20 text-center">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">TK/1–3</p>
                    <p class="text-[10px] text-slate-500">Belum kawin, 1–3 tanggungan</p>
                </div>
                <div class="bg-white dark:bg-slate-800/50 rounded-lg p-2 border border-peach/20 text-center">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">K/0</p>
                    <p class="text-[10px] text-slate-500">Kawin, tanpa tanggungan</p>
                </div>
                <div class="bg-white dark:bg-slate-800/50 rounded-lg p-2 border border-peach/20 text-center">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">K/1–3</p>
                    <p class="text-[10px] text-slate-500">Kawin, 1–3 tanggungan</p>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 italic">PTKP terakhir diubah melalui PMK 101/2016. Selama belum ada peraturan baru, Anda tidak perlu mengubah data ini.</p>
        </div>
    </div>
</div>

@php
    $today = now()->toDateString();
@endphp

<div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Jumlah PTKP</th>
                    <th class="px-6 py-3">Berlaku Dari</th>
                    <th class="px-6 py-3">Berlaku Sampai</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($rates as $rate)
                @php
                    $isActive = $rate->effective_from <= $today && ($rate->effective_until === null || $rate->effective_until >= $today);
                    $isFuture = $rate->effective_from > $today;
                    $rowClass = $isActive ? 'bg-sage/10' : ($isFuture ? 'bg-sky/10' : '');
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="px-6 py-3">
                        <span class="font-semibold text-slate-800 dark:text-white">{{ $rate->status }}</span>
                    </td>
                    <td class="px-6 py-3 font-medium">Rp {{ number_format($rate->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-3">{{ $rate->effective_from->format('d/m/Y') }}</td>
                    <td class="px-6 py-3">{{ $rate->effective_until ? $rate->effective_until->format('d/m/Y') : '-' }}</td>
                    <td class="px-6 py-3">
                        @if($isActive)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sage/30 text-green-800 dark:text-green-300">Aktif</span>
                        @elseif($isFuture)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky/30 text-blue-800 dark:text-blue-300">Akan Datang</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400">Kadaluarsa</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <span class="material-icons-round text-4xl mb-2">info</span>
                        <p>Belum ada data tarif PTKP.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
