@extends('layouts.absensi')

@section('title', 'Riwayat Pelanggaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <span class="material-icons-round text-3xl" style="color: #F5D5CB;">gavel</span>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Riwayat Pelanggaran</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Catatan pelanggaran dan surat peringatan Anda</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Poin Tahun {{ $year }}</p>
            <div class="flex items-baseline gap-2">
                @php
                    $pointBadge = match(true) {
                        $yearlyTotal >= 10 => 'text-rose-600 dark:text-rose-400',
                        $yearlyTotal >= 3 => 'text-amber-600 dark:text-amber-400',
                        default => 'text-slate-900 dark:text-white',
                    };
                @endphp
                <span class="text-3xl font-bold {{ $pointBadge }}">{{ $yearlyTotal }}</span>
                <span class="text-sm text-slate-400">poin</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Surat Peringatan Aktif</p>
            @if($warningLetters->count() > 0)
                @php $latestSP = $warningLetters->first(); @endphp
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-rose-600 dark:text-rose-400">SP{{ $latestSP->type }}</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Diterbitkan {{ \Carbon\Carbon::parse($latestSP->issued_at)->format('d M Y') }}</p>
            @else
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">Tidak Ada</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Tidak ada SP aktif</p>
            @endif
        </div>
    </div>

    <!-- Filter -->
    <section class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-4 shadow-sm">
        <form method="GET" action="{{ route('violations.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Tahun</label>
                <select name="year" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm px-3 py-2 focus:ring-2 focus:ring-primary/50">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Bulan</label>
                <select name="month" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm px-3 py-2 focus:ring-2 focus:ring-primary/50">
                    <option value="">Semua</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-slate-900 dark:bg-white px-4 py-2 text-sm font-semibold text-white dark:text-slate-900 hover:bg-slate-700 dark:hover:bg-slate-200 transition">
                <span class="material-icons-round text-base">filter_list</span>
                Filter
            </button>
        </form>
    </section>

    <!-- Violations Table -->
    <section class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark shadow-sm overflow-hidden">
        @if($violations->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Jenis Pelanggaran</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Poin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($violations as $violation)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-4 py-3 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($violation->tanggal)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-slate-900 dark:text-white font-medium">
                            {{ $violation->violationType->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $badge = match(true) {
                                    $violation->points >= 10 => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                    $violation->points >= 3 => 'bg-peach text-slate-900',
                                    default => 'bg-sage text-slate-900',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $badge }}">
                                {{ $violation->points }}p
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                            {{ $violation->notes ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800">
            {{ $violations->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <span class="material-icons-round text-5xl text-slate-300 dark:text-slate-600">check_circle</span>
            <p class="mt-3 text-sm font-medium text-slate-500 dark:text-slate-400">Tidak ada pelanggaran ditemukan</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Pertahankan kedisiplinan Anda!</p>
        </div>
        @endif
    </section>

    <!-- Warning Letters Section -->
    @if($warningLetters->count() > 0)
    <section class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-6 shadow-sm">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
            <span class="material-icons-round text-rose-400">warning</span>
            Surat Peringatan
        </h2>
        <div class="space-y-3">
            @foreach($warningLetters as $sp)
            <div class="flex items-center justify-between rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    @php
                        $spColor = match($sp->type) {
                            1 => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                            2 => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400',
                            3 => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                            default => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300',
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $spColor }}">
                        SP{{ $sp->type }}
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Surat Peringatan {{ $sp->type }}</p>
                        <p class="text-xs text-slate-400">{{ $sp->reason ?? 'Akumulasi pelanggaran' }}</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($sp->issued_at)->format('d M Y') }}</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
