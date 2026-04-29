@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="mb-6">
        <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Tiket Saya
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
            <div>
                <p class="font-mono text-xs font-bold text-sky mb-1">{{ $ticket->ticket_number }}</p>
                <h1 class="text-xl font-bold text-slate-900">{{ $ticket->subject }}</h1>
            </div>
            <div class="flex gap-2 flex-wrap">
                @php
                    $priorityClass = match($ticket->priority) {
                        'critical' => 'bg-rose-100 text-rose-700',
                        'high' => 'bg-peach text-slate-900',
                        'medium' => 'bg-sky/30 text-slate-900',
                        'low' => 'bg-sage/50 text-slate-900',
                        default => 'bg-slate-100 text-slate-600',
                    };
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
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $priorityClass }}">
                    {{ ucfirst($ticket->priority) }}
                </span>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Kategori</p>
                <p class="text-slate-700 font-medium mt-0.5">{{ $ticket->category->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Dibuat</p>
                <p class="text-slate-700 font-medium mt-0.5">{{ $ticket->created_at->translatedFormat('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Ditangani Oleh</p>
                <p class="text-slate-700 font-medium mt-0.5">{{ $ticket->assignee->name ?? 'Belum di-assign' }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Diselesaikan</p>
                <p class="text-slate-700 font-medium mt-0.5">{{ $ticket->resolved_at ? $ticket->resolved_at->translatedFormat('d M Y H:i') : '-' }}</p>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-4">
            <p class="text-sm font-bold text-slate-700 mb-2">Deskripsi</p>
            <div class="text-sm text-slate-600 whitespace-pre-wrap">{{ $ticket->description }}</div>
        </div>

        @if($ticket->resolution)
            <div class="mt-4 p-4 bg-sage/20 rounded-xl border border-sage/30">
                <p class="text-sm font-bold text-green-800 mb-1">Resolusi</p>
                <div class="text-sm text-green-700 whitespace-pre-wrap">{{ $ticket->resolution }}</div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Komentar ({{ $comments->count() }})</h2>

        <div class="space-y-4 mb-6">
            @forelse($comments as $comment)
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-slate-600">{{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-bold text-slate-800">{{ $comment->user->name ?? '-' }}</span>
                            <span class="text-xs text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-sm text-slate-600 whitespace-pre-wrap bg-slate-50 rounded-xl p-3">{{ $comment->comment }}</div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 text-center py-4">Belum ada komentar.</p>
            @endforelse
        </div>

        @if(!in_array($ticket->status, ['closed', 'cancelled']))
            <form method="POST" action="{{ route('tickets.comment', $ticket) }}" class="border-t border-slate-100 pt-4">
                @csrf
                <textarea name="comment" rows="3" required maxlength="5000"
                    placeholder="Tulis komentar..."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-sage focus:border-sage resize-y mb-3"></textarea>
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-5 py-2.5 bg-sage text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all shadow-sm">
                        Kirim Komentar
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
