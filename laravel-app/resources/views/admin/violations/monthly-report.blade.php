@extends('layouts.admin')

@section('header-title', 'Laporan Bulanan Pelanggaran')
@section('header-subtitle', 'Ringkasan poin pelanggaran per karyawan')

@section('content')
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('admin.violations.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all mb-2">
                    <span class="material-icons-round text-base">arrow_back</span>
                    Kembali ke Daftar Pelanggaran
                </a>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Laporan Bulanan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Ringkasan pelanggaran per karyawan untuk periode tertentu.</p>
            </div>

            <form method="GET" action="{{ route('admin.violations.monthly-report') }}" class="flex items-center gap-3">
                <input type="month" name="month" value="{{ $month }}"
                    class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-sage" />
                <button type="submit"
                    class="px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all">
                    Tampilkan
                </button>
            </form>
        </div>

        <!-- SUMMARY TABLE -->
        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                <h3 class="font-bold text-slate-900 dark:text-white">
                    Periode: {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Karyawan</th>
                            <th class="px-6 py-4">Total Poin</th>
                            <th class="px-6 py-4">Jumlah Pelanggaran</th>
                            <th class="px-6 py-4">Status SP</th>
                            <th class="px-6 py-4">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" x-data="{ expandedUser: null }">
                        @forelse($usersWithViolations as $user)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer"
                                @click="expandedUser = expandedUser === {{ $user->id }} ? null : {{ $user->id }}">
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClass = match(true) {
                                            $user->monthly_total_points >= 10 => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                            $user->monthly_total_points >= 3 => 'bg-peach text-slate-900',
                                            default => 'bg-sage text-slate-900',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $badgeClass }}">
                                        {{ $user->monthly_total_points }} poin
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                    {{ $user->monthly_violation_count }}x
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->active_sp)
                                        @php
                                            $spBadge = match($user->active_sp->type) {
                                                'SP1' => 'bg-peach text-slate-900',
                                                'SP2' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                                'SP3' => 'bg-red-500 text-white',
                                                default => 'bg-slate-100 text-slate-600',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $spBadge }}">
                                            {{ $user->active_sp->type }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-sage px-3 py-1 text-xs font-bold text-slate-900">
                                            Tidak ada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="material-icons-round text-slate-400 text-base transition-transform"
                                        :class="expandedUser === {{ $user->id }} ? 'rotate-180' : ''">
                                        expand_more
                                    </span>
                                </td>
                            </tr>
                            <tr x-show="expandedUser === {{ $user->id }}" x-transition x-cloak>
                                <td colspan="5" class="px-6 py-4 bg-slate-50/50 dark:bg-slate-800/30">
                                    <div class="space-y-2">
                                        @foreach($user->violations as $v)
                                            <div class="flex items-center justify-between p-3 bg-white dark:bg-card-dark rounded-xl border border-slate-100 dark:border-slate-700">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                                        {{ $v->tanggal->format('d M') }}
                                                    </span>
                                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                        {{ $v->violationType->name ?? '-' }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs text-slate-400">{{ $v->notes }}</span>
                                                    @php
                                                        $detailBadge = match(true) {
                                                            $v->points >= 10 => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                                            $v->points >= 3 => 'bg-peach text-slate-900',
                                                            default => 'bg-sage text-slate-900',
                                                        };
                                                    @endphp
                                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold {{ $detailBadge }}">
                                                        {{ $v->points }}p
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center italic text-slate-500 dark:text-slate-400">
                                    Tidak ada pelanggaran pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
