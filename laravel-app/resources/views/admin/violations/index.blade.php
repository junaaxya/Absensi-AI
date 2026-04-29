@extends('layouts.admin')

@section('header-title', 'Poin Pelanggaran')
@section('header-subtitle', 'Kelola pelanggaran dan poin karyawan')

@section('content')
    <div class="space-y-6">
        <!-- HEADER ACTIONS -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Pelanggaran</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Riwayat pelanggaran seluruh karyawan.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.violations.monthly-report') }}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-lavender text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all">
                    <span class="material-icons-round text-base">assessment</span>
                    Laporan Bulanan
                </a>
                <a href="{{ route('admin.violations.create') }}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-sage text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all">
                    <span class="material-icons-round text-base">add</span>
                    Tambah Pelanggaran
                </a>
            </div>
        </div>

        <!-- FILTER -->
        <div class="bg-white dark:bg-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-800">
            <form method="GET" action="{{ route('admin.violations.index') }}" class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white" />
                </div>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage" />
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage" />
                <select name="violation_type"
                    class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage">
                    <option value="">Semua Jenis</option>
                    @foreach($violationTypes as $type)
                        <option value="{{ $type->id }}" {{ request('violation_type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                    class="px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all">
                    Filter
                </button>
                <a href="{{ route('admin.violations.index') }}"
                    class="px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-center">
                    Reset
                </a>
            </form>
        </div>

        <!-- TABLE -->
        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Karyawan</th>
                            <th class="px-6 py-4">Jenis Pelanggaran</th>
                            <th class="px-6 py-4">Poin</th>
                            <th class="px-6 py-4">Referensi</th>
                            <th class="px-6 py-4">Catatan</th>
                            <th class="px-6 py-4">Dibuat Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($violations as $violation)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                    {{ $violation->tanggal->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">
                                    {{ $violation->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                    {{ $violation->violationType->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClass = match(true) {
                                            $violation->points >= 10 => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                            $violation->points >= 3 => 'bg-peach text-slate-900',
                                            default => 'bg-sage text-slate-900',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $badgeClass }}">
                                        {{ $violation->points }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                    @if($violation->reference_type === 'App\\Models\\Attendance' && $violation->reference_id)
                                        <span class="inline-flex items-center gap-1 text-xs text-sky-600 dark:text-sky-400">
                                            <span class="material-icons-round text-sm">link</span>
                                            Absensi #{{ $violation->reference_id }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                    {{ $violation->notes ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($violation->created_by)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 dark:text-slate-400">
                                            <span class="material-icons-round text-sm">person</span>
                                            {{ $violation->creator->name ?? 'Manual' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-lavender">
                                            <span class="material-icons-round text-sm">smart_toy</span>
                                            Otomatis
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center italic text-slate-500 dark:text-slate-400">
                                    Belum ada data pelanggaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700 p-4">
                {{ $violations->links() }}
            </div>
        </div>
    </div>
@endsection
