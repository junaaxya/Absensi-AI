@extends('layouts.admin')

@section('header-title', 'Tarif BPJS')
@section('header-subtitle', 'Kelola tarif iuran BPJS Ketenagakerjaan & Kesehatan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.rate-management.index') }}"
       class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
        <span class="material-icons-round text-[18px]">arrow_back</span>
        Kembali
    </a>
    <a href="{{ route('admin.rate-management.bpjs.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-slate-900 font-semibold text-sm hover:bg-primary/80 shadow-sm transition-colors">
        <span class="material-icons-round text-[18px]">add</span>
        Tambah Tarif Baru
    </a>
</div>

{{-- B3: Page Guide --}}
<div class="mb-6 bg-sky/10 dark:bg-sky/5 border border-sky/30 rounded-xl p-5">
    <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg bg-sky/30 flex items-center justify-center flex-shrink-0">
            <span class="material-icons-round text-blue-600 dark:text-blue-400 text-[18px]">school</span>
        </div>
        <div>
            <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-1.5">Tentang BPJS</h4>
            <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">BPJS adalah iuran jaminan sosial yang wajib dibayarkan perusahaan dan karyawan. Setiap program memiliki tarif iuran yang berbeda.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span class="text-[11px] text-slate-600 dark:text-slate-400"><strong>JHT</strong> — Tabungan pensiun (dicairkan saat berhenti kerja)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span class="text-[11px] text-slate-600 dark:text-slate-400"><strong>JKK</strong> — Perlindungan kecelakaan kerja</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span class="text-[11px] text-slate-600 dark:text-slate-400"><strong>JKM</strong> — Santunan kematian</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span class="text-[11px] text-slate-600 dark:text-slate-400"><strong>JP</strong> — Pensiun bulanan (usia 56+)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span class="text-[11px] text-slate-600 dark:text-slate-400"><strong>Kesehatan</strong> — Jaminan layanan kesehatan</span>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 italic">Setiap awal tahun, periksa batas atas gaji JP dan Kesehatan karena sering diperbarui pemerintah.</p>
        </div>
    </div>
</div>

@php
    $today = now()->toDateString();
    $programLabels = [
        'jht' => 'JHT (Jaminan Hari Tua)',
        'jkk' => 'JKK (Jaminan Kecelakaan Kerja)',
        'jkm' => 'JKM (Jaminan Kematian)',
        'jp' => 'JP (Jaminan Pensiun)',
        'bpjs_kesehatan' => 'BPJS Kesehatan',
    ];
@endphp

@forelse($rates as $program => $programRates)
<div class="mb-6 bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
        <h3 class="font-bold text-slate-800 dark:text-white">{{ $programLabels[$program] ?? strtoupper($program) }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <th class="px-6 py-3">
                        <span>Iuran Perusahaan</span>
                        <p class="text-[10px] font-normal normal-case tracking-normal text-slate-400 mt-0.5">Dibayar perusahaan, tidak dipotong dari gaji</p>
                    </th>
                    <th class="px-6 py-3">
                        <span>Iuran Karyawan</span>
                        <p class="text-[10px] font-normal normal-case tracking-normal text-slate-400 mt-0.5">Dipotong langsung dari gaji karyawan</p>
                    </th>
                    <th class="px-6 py-3">
                        <span>Maks. Dasar Gaji</span>
                        <p class="text-[10px] font-normal normal-case tracking-normal text-slate-400 mt-0.5">Batas atas gaji untuk hitung iuran</p>
                    </th>
                    <th class="px-6 py-3">Min. Dasar Gaji</th>
                    <th class="px-6 py-3">Berlaku Dari</th>
                    <th class="px-6 py-3">Berlaku Sampai</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($programRates as $rate)
                @php
                    $isActive = $rate->effective_from <= $today && ($rate->effective_until === null || $rate->effective_until >= $today);
                    $isFuture = $rate->effective_from > $today;
                    $rowClass = $isActive ? 'bg-sage/10' : ($isFuture ? 'bg-sky/10' : 'bg-slate-50 dark:bg-slate-800/30');
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="px-6 py-3 font-medium">{{ rtrim(rtrim(number_format($rate->employer_rate * 100, 4), '0'), '.') }}%</td>
                    <td class="px-6 py-3 font-medium">{{ rtrim(rtrim(number_format($rate->employee_rate * 100, 4), '0'), '.') }}%</td>
                    <td class="px-6 py-3">{{ $rate->max_salary_basis ? 'Rp ' . number_format($rate->max_salary_basis, 0, ',', '.') : '-' }}</td>
                    <td class="px-6 py-3">{{ $rate->min_salary_basis ? 'Rp ' . number_format($rate->min_salary_basis, 0, ',', '.') : '-' }}</td>
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
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="text-center py-12 text-slate-500">
    <span class="material-icons-round text-4xl mb-2">info</span>
    <p>Belum ada data tarif BPJS.</p>
</div>
@endforelse
@endsection
