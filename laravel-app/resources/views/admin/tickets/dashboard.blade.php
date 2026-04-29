@extends('layouts.admin')

@section('header-title', 'Dashboard Tiket')
@section('header-subtitle', 'Ringkasan dan statistik tiket layanan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Dashboard Tiket</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Ringkasan performa layanan internal.</p>
        </div>
        <a href="{{ route('admin.tickets.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 bg-sage text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all">
            <span class="material-icons-round text-base">list</span>
            Lihat Semua Tiket
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-sky/30 flex items-center justify-center">
                    <span class="material-icons-round text-sky-600 text-xl">inbox</span>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $openCount }}</p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Open</p>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-lavender/30 flex items-center justify-center">
                    <span class="material-icons-round text-purple-600 text-xl">pending</span>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $inProgressCount }}</p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">In Progress</p>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-peach/30 flex items-center justify-center">
                    <span class="material-icons-round text-orange-600 text-xl">hourglass_top</span>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $waitingCount }}</p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Waiting</p>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-sage/30 flex items-center justify-center">
                    <span class="material-icons-round text-green-600 text-xl">check_circle</span>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $resolvedCount }}</p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Resolved</p>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                    <span class="material-icons-round text-slate-500 text-xl">archive</span>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $closedCount }}</p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Closed</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">SLA Response Compliance</h3>
            <div class="flex items-end gap-3">
                <p class="text-4xl font-bold {{ $slaResponseCompliance >= 80 ? 'text-green-600' : ($slaResponseCompliance >= 60 ? 'text-amber-500' : 'text-rose-500') }}">
                    {{ $slaResponseCompliance }}%
                </p>
            </div>
            <div class="mt-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                <div class="h-2 rounded-full {{ $slaResponseCompliance >= 80 ? 'bg-green-500' : ($slaResponseCompliance >= 60 ? 'bg-amber-400' : 'bg-rose-500') }}"
                    style="width: {{ $slaResponseCompliance }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">SLA Resolution Compliance</h3>
            <div class="flex items-end gap-3">
                <p class="text-4xl font-bold {{ $slaResolutionCompliance >= 80 ? 'text-green-600' : ($slaResolutionCompliance >= 60 ? 'text-amber-500' : 'text-rose-500') }}">
                    {{ $slaResolutionCompliance }}%
                </p>
            </div>
            <div class="mt-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                <div class="h-2 rounded-full {{ $slaResolutionCompliance >= 80 ? 'bg-green-500' : ($slaResolutionCompliance >= 60 ? 'bg-amber-400' : 'bg-rose-500') }}"
                    style="width: {{ $slaResolutionCompliance }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Rata-rata Waktu Resolusi</h3>
            <div class="flex items-end gap-2">
                <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ $avgResolutionHours }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">jam</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Tiket Aktif per Kategori</h3>
            <div class="space-y-3">
                @foreach($ticketsByCategory as $cat)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $cat->name }}</span>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-sky/20 text-sky-700 dark:text-sky-400">
                            {{ $cat->tickets_count }}
                        </span>
                    </div>
                @endforeach
                @if($ticketsByCategory->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-4">Tidak ada data.</p>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Tiket Terbaru</h3>
            <div class="space-y-3">
                @foreach($recentTickets as $ticket)
                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 -mx-2 px-2 py-2 rounded-xl transition">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $ticket->subject }}</p>
                            <p class="text-xs text-slate-400">{{ $ticket->ticket_number }} &middot; {{ $ticket->creator->name ?? '-' }}</p>
                        </div>
                        @php
                            $statusClass = match($ticket->status) {
                                'open' => 'bg-sky/30 text-sky-700',
                                'in_progress' => 'bg-lavender/50 text-purple-700',
                                'waiting' => 'bg-peach/50 text-orange-700',
                                'resolved' => 'bg-sage/50 text-green-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusClass }} ml-2 flex-shrink-0">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </a>
                @endforeach
                @if($recentTickets->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-4">Tidak ada tiket.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
