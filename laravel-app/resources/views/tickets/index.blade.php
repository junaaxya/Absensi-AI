@extends('layouts.absensi')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tiket Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar tiket layanan yang Anda ajukan.</p>
        </div>
        <a href="{{ route('tickets.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-sage text-slate-900 rounded-2xl font-bold text-sm hover:brightness-95 transition-all shadow-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Tiket Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-4">
        <form method="GET" action="{{ route('tickets.index') }}" class="flex gap-2 flex-wrap">
            <select name="status" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-sage">
                <option value="">Semua Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl font-bold text-sm hover:opacity-90 transition-all">Filter</button>
            <a href="{{ route('tickets.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-widest text-slate-500">
                    <tr>
                        <th class="px-6 py-4">No. Tiket</th>
                        <th class="px-6 py-4">Subjek</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Prioritas</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        <tr class="transition hover:bg-slate-50 cursor-pointer" onclick="window.location='{{ route('tickets.show', $ticket) }}'">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-bold text-sky">
                                {{ $ticket->ticket_number }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800 max-w-xs truncate">
                                {{ $ticket->subject }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $ticket->category->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $priorityClass = match($ticket->priority) {
                                        'critical' => 'bg-rose-100 text-rose-700',
                                        'high' => 'bg-peach text-slate-900',
                                        'medium' => 'bg-sky/30 text-slate-900',
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
                                        'open' => 'bg-sky/30 text-sky-700',
                                        'in_progress' => 'bg-lavender/50 text-purple-700',
                                        'waiting' => 'bg-peach/50 text-orange-700',
                                        'resolved' => 'bg-sage/50 text-green-700',
                                        'closed' => 'bg-slate-100 text-slate-600',
                                        'cancelled' => 'bg-rose-100 text-rose-600',
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
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">
                                {{ $ticket->created_at->translatedFormat('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="font-medium">Belum ada tiket</p>
                                    <p class="text-sm">Buat tiket baru untuk mengajukan permintaan layanan.</p>
                                </div>
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
