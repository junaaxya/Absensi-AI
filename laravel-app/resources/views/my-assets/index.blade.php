@extends('layouts.absensi')

@section('title', 'Aset Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <span class="material-icons-round text-3xl" style="color: #B8D4E3;">inventory_2</span>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Aset Saya</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Daftar aset perusahaan yang sedang Anda pinjam</p>
        </div>
    </div>

    <!-- Section 1: Aset yang Sedang Dipinjam -->
    <section class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-6 shadow-sm">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
            <span class="material-icons-round text-sky-500">devices</span>
            Aset yang Sedang Dipinjam
        </h2>

        @if($assignments->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($assignments as $assignment)
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-icons-round text-slate-400">inventory</span>
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            {{ $assignment->asset->category->name ?? 'Umum' }}
                        </span>
                    </div>
                    @php
                        $conditionBadge = match($assignment->condition_on_assign ?? 'baik') {
                            'baik' => 'bg-sage text-slate-900',
                            'cukup' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                            default => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $conditionBadge }}">
                        {{ ucfirst($assignment->condition_on_assign ?? 'Baik') }}
                    </span>
                </div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">{{ $assignment->asset->name ?? '-' }}</h3>
                @if($assignment->asset->serial_number)
                <p class="text-xs text-slate-400 mb-2 font-mono">SN: {{ $assignment->asset->serial_number }}</p>
                @endif
                <div class="flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
                    <span class="material-icons-round text-sm">calendar_today</span>
                    Dipinjam sejak {{ \Carbon\Carbon::parse($assignment->assigned_at)->format('d M Y') }}
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-8 text-center">
            <span class="material-icons-round text-5xl text-slate-300 dark:text-slate-600">inventory_2</span>
            <p class="mt-3 text-sm font-medium text-slate-500 dark:text-slate-400">Tidak ada aset yang sedang dipinjam</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Anda belum memiliki aset perusahaan yang dipinjam saat ini</p>
        </div>
        @endif
    </section>

    <!-- Section 2: Riwayat Pengembalian -->
    @if($history->count() > 0)
    <section class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark shadow-sm overflow-hidden">
        <div class="p-6 pb-0">
            <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
                <span class="material-icons-round text-slate-400">history</span>
                Riwayat Pengembalian
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Aset</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tanggal Pinjam</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tanggal Kembali</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($history as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-4 py-3 text-slate-900 dark:text-white font-medium">
                            {{ $item->asset->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($item->assigned_at)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($item->returned_at)->format('d M Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif
</div>
@endsection
