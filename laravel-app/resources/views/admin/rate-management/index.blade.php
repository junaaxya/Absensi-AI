@extends('layouts.admin')

@section('header-title', 'Manajemen Tarif & Pajak')
@section('header-subtitle', 'Kelola tarif BPJS, TER PPh 21, dan PTKP')

@section('content')

{{-- B1: Page Description --}}
<div class="mb-6">
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Halaman ini berisi tarif-tarif resmi yang digunakan sistem untuk menghitung potongan gaji karyawan secara otomatis.</p>
    <div class="bg-sky/10 dark:bg-sky/5 border border-sky/30 rounded-xl p-4">
        <div class="flex items-start gap-2.5">
            <span class="material-icons-round text-blue-600 dark:text-blue-400 text-[18px] mt-0.5">info</span>
            <div>
                <p class="text-xs font-bold text-blue-700 dark:text-blue-400 mb-1">Kapan harus memperbarui tarif?</p>
                <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-0.5">
                    <li><strong>BPJS</strong> — Periksa setiap Januari (batas atas gaji sering berubah)</li>
                    <li><strong>TER PPh 21</strong> — Jika ada Peraturan Pemerintah baru tentang tarif pajak</li>
                    <li><strong>PTKP</strong> — Terakhir berubah tahun 2016, periksa jika ada PMK baru</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- B11: Status Legend --}}
<div class="mb-6 flex items-center gap-4 flex-wrap">
    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Legenda Status:</span>
    <div class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-sage/50"></span>
        <span class="text-xs text-slate-600 dark:text-slate-400">Aktif</span>
    </div>
    <div class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-sky/50"></span>
        <span class="text-xs text-slate-600 dark:text-slate-400">Akan datang</span>
    </div>
    <div class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full bg-slate-300 dark:bg-slate-600"></span>
        <span class="text-xs text-slate-600 dark:text-slate-400">Kadaluarsa</span>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- BPJS Card --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-sky/40 shadow-sm overflow-hidden">
        <div class="h-2 bg-sky"></div>
        <div class="p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-sky/20 flex items-center justify-center">
                    <span class="material-icons-round text-sky-600 text-2xl">health_and_safety</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">Tarif BPJS</h3>
                    <p class="text-xs text-slate-500">JHT, JKK, JKM, JP, Kesehatan</p>
                </div>
            </div>
            {{-- B2: Card Description --}}
            <p class="text-xs text-slate-500 mb-4 ml-15">Iuran jaminan sosial ketenagakerjaan & kesehatan</p>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $bpjsActive }}</p>
                    <p class="text-xs text-slate-500">Tarif aktif</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">Terakhir diperbarui</p>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ $bpjsLastUpdated ? \Carbon\Carbon::parse($bpjsLastUpdated)->translatedFormat('d M Y') : '-' }}
                    </p>
                </div>
            </div>
            {{-- B12: Staleness Warning --}}
            @if($bpjsLastUpdated && \Carbon\Carbon::parse($bpjsLastUpdated)->lt(now()->subYear()))
            <div class="bg-peach/10 dark:bg-peach/5 border border-peach/30 rounded-lg p-2.5 mb-3">
                <div class="flex items-center gap-1.5">
                    <span class="material-icons-round text-orange-500 text-[14px]">warning</span>
                    <p class="text-[11px] text-orange-700 dark:text-orange-400 font-medium">Tarif belum diperbarui lebih dari 1 tahun</p>
                </div>
            </div>
            @endif
            <a href="{{ route('admin.rate-management.bpjs') }}"
               class="block w-full text-center py-2.5 rounded-xl bg-sky/20 text-sky-700 dark:text-sky-300 font-semibold text-sm hover:bg-sky/30 transition-colors">
                Kelola
            </a>
        </div>
    </div>

    {{-- TER Card --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-lavender/40 shadow-sm overflow-hidden">
        <div class="h-2 bg-lavender"></div>
        <div class="p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-lavender/20 flex items-center justify-center">
                    <span class="material-icons-round text-purple-600 text-2xl">calculate</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">Tarif TER PPh 21</h3>
                    <p class="text-xs text-slate-500">Kategori A, B, C</p>
                </div>
            </div>
            {{-- B2: Card Description --}}
            <p class="text-xs text-slate-500 mb-4 ml-15">Tarif pemotongan pajak bulanan karyawan</p>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $terActive }}</p>
                    <p class="text-xs text-slate-500">Tarif aktif</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">Terakhir diperbarui</p>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ $terLastUpdated ? \Carbon\Carbon::parse($terLastUpdated)->translatedFormat('d M Y') : '-' }}
                    </p>
                </div>
            </div>
            {{-- B12: Staleness Warning --}}
            @if($terLastUpdated && \Carbon\Carbon::parse($terLastUpdated)->lt(now()->subYear()))
            <div class="bg-peach/10 dark:bg-peach/5 border border-peach/30 rounded-lg p-2.5 mb-3">
                <div class="flex items-center gap-1.5">
                    <span class="material-icons-round text-orange-500 text-[14px]">warning</span>
                    <p class="text-[11px] text-orange-700 dark:text-orange-400 font-medium">Tarif belum diperbarui lebih dari 1 tahun</p>
                </div>
            </div>
            @endif
            <a href="{{ route('admin.rate-management.ter') }}"
               class="block w-full text-center py-2.5 rounded-xl bg-lavender/20 text-purple-700 dark:text-purple-300 font-semibold text-sm hover:bg-lavender/30 transition-colors">
                Kelola
            </a>
        </div>
    </div>

    {{-- PTKP Card --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-peach/40 shadow-sm overflow-hidden">
        <div class="h-2 bg-peach"></div>
        <div class="p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-peach/20 flex items-center justify-center">
                    <span class="material-icons-round text-orange-600 text-2xl">family_restroom</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">Tarif PTKP</h3>
                    <p class="text-xs text-slate-500">TK/0 s.d. K/3</p>
                </div>
            </div>
            {{-- B2: Card Description --}}
            <p class="text-xs text-slate-500 mb-4 ml-15">Batas penghasilan bebas pajak berdasarkan status keluarga</p>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $ptkpActive }}</p>
                    <p class="text-xs text-slate-500">Tarif aktif</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">Terakhir diperbarui</p>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        {{ $ptkpLastUpdated ? \Carbon\Carbon::parse($ptkpLastUpdated)->translatedFormat('d M Y') : '-' }}
                    </p>
                </div>
            </div>
            {{-- B12: Staleness Warning --}}
            @if($ptkpLastUpdated && \Carbon\Carbon::parse($ptkpLastUpdated)->lt(now()->subYear()))
            <div class="bg-peach/10 dark:bg-peach/5 border border-peach/30 rounded-lg p-2.5 mb-3">
                <div class="flex items-center gap-1.5">
                    <span class="material-icons-round text-orange-500 text-[14px]">warning</span>
                    <p class="text-[11px] text-orange-700 dark:text-orange-400 font-medium">Tarif belum diperbarui lebih dari 1 tahun</p>
                </div>
            </div>
            @endif
            <a href="{{ route('admin.rate-management.ptkp') }}"
               class="block w-full text-center py-2.5 rounded-xl bg-peach/20 text-orange-700 dark:text-orange-300 font-semibold text-sm hover:bg-peach/30 transition-colors">
                Kelola
            </a>
        </div>
    </div>

</div>
@endsection
