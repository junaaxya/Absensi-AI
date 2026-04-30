@extends('layouts.admin')

@section('header-title', 'Manajemen Tarif & Pajak')
@section('header-subtitle', 'Kelola tarif BPJS, TER PPh 21, dan PTKP')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- BPJS Card --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-sky/40 shadow-sm overflow-hidden">
        <div class="h-2 bg-sky"></div>
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-sky/20 flex items-center justify-center">
                    <span class="material-icons-round text-sky-600 text-2xl">health_and_safety</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">Tarif BPJS</h3>
                    <p class="text-xs text-slate-500">JHT, JKK, JKM, JP, Kesehatan</p>
                </div>
            </div>
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
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-lavender/20 flex items-center justify-center">
                    <span class="material-icons-round text-purple-600 text-2xl">calculate</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">Tarif TER PPh 21</h3>
                    <p class="text-xs text-slate-500">Kategori A, B, C</p>
                </div>
            </div>
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
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-peach/20 flex items-center justify-center">
                    <span class="material-icons-round text-orange-600 text-2xl">family_restroom</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">Tarif PTKP</h3>
                    <p class="text-xs text-slate-500">TK/0 s.d. K/3</p>
                </div>
            </div>
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
            <a href="{{ route('admin.rate-management.ptkp') }}"
               class="block w-full text-center py-2.5 rounded-xl bg-peach/20 text-orange-700 dark:text-orange-300 font-semibold text-sm hover:bg-peach/30 transition-colors">
                Kelola
            </a>
        </div>
    </div>

</div>
@endsection
