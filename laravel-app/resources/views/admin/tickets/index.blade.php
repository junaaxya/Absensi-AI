@extends('layouts.admin')

@section('header-title', 'Tiket Layanan')
@section('header-subtitle', 'Kelola tiket layanan internal')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Semua Tiket</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola dan pantau tiket layanan dari seluruh karyawan.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.tickets.dashboard') }}"
                class="flex items-center gap-2 px-4 py-2.5 bg-lavender text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all">
                <span class="material-icons-round text-base">dashboard</span>
                Dashboard
            </a>
            <a href="{{ route('admin.tickets.categories') }}"
                class="flex items-center gap-2 px-4 py-2.5 bg-sky text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all">
                <span class="material-icons-round text-base">category</span>
                Kategori
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-800">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tiket, subjek, atau nama..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white" />
            </div>
            <select name="status" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage">
                <option value="">Semua Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <select name="priority" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage">
                <option value="">Semua Prioritas</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
            </select>
            <select name="category" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white cursor-pointer">
                <input type="checkbox" name="sla_breach" value="1" {{ request('sla_breach') ? 'checked' : '' }} class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                <span class="text-xs font-bold">SLA Breach</span>
            </label>
            <button type="submit" class="px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all">Filter</button>
            <a href="{{ route('admin.tickets.index') }}" class="px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-center">Reset</a>
        </form>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">No. Tiket</th>
                        <th class="px-6 py-4">Subjek</th>
                        <th class="px-6 py-4">Pemohon</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Prioritas</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">SLA</th>
                        <th class="px-6 py-4">Ditangani</th>
                        <th class="px-6 py-4">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($tickets as $ticket)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer" onclick="window.location='{{ route('admin.tickets.show', $ticket) }}'">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-bold text-sky">
                                {{ $ticket->ticket_number }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white max-w-xs truncate">
                                {{ $ticket->subject }}
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
                                {{ $ticket->creator->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $ticket->category->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $priorityClass = match($ticket->priority) {
                                        'critical' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                        'high' => 'bg-peach text-slate-900',
                                        'medium' => 'bg-sky/30 text-slate-900 dark:text-sky',
                                        'low' => 'bg-sage/50 text-slate-900',
                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $priorityClass }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClass = match($ticket->status) {
                                        'open' => 'bg-sky/30 text-sky-700 dark:text-sky-400',
                                        'in_progress' => 'bg-lavender/50 text-purple-700 dark:text-purple-400',
                                        'waiting' => 'bg-peach/50 text-orange-700 dark:text-orange-400',
                                        'resolved' => 'bg-sage/50 text-green-700 dark:text-green-400',
                                        'closed' => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400',
                                        'cancelled' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                    $statusLabel = match($ticket->status) {
                                        'open' => 'Open',
                                        'in_progress' => 'In Progress',
                                        'waiting' => 'Waiting',
                                        'resolved' => 'Resolved',
                                        'closed' => 'Closed',
                                        'cancelled' => 'Cancelled',
                                        default => $ticket->status,
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $slaResponse = $ticket->sla_response_status;
                                    $slaResolution = $ticket->sla_resolution_status;
                                    $slaColor = match(true) {
                                        $slaResponse === 'red' || $slaResolution === 'red' => 'bg-rose-500',
                                        $slaResponse === 'yellow' || $slaResolution === 'yellow' => 'bg-amber-400',
                                        default => 'bg-green-500',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $slaColor }}"></span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        @if($slaResponse === 'red' || $slaResolution === 'red')
                                            Breach
                                        @elseif($slaResponse === 'yellow' || $slaResolution === 'yellow')
                                            Warning
                                        @else
                                            On Track
                                        @endif
                                    </span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 text-xs">
                                {{ $ticket->assignee->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400 text-xs">
                                {{ $ticket->created_at->translatedFormat('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <span class="material-icons-round text-4xl mb-2 block">confirmation_number</span>
                                <p class="font-medium">Belum ada tiket</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
