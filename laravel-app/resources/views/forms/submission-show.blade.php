@extends('layouts.absensi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('forms.submissions') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $submission->template->name ?? 'Submission' }}</h1>
            @switch($submission->status)
                @case('draft')
                    <span class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl">Draft</span>
                    @break
                @case('submitted')
                    <span class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider bg-sky/30 text-blue-700 dark:text-blue-300 rounded-xl">Submitted</span>
                    @break
                @case('approved')
                    <span class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider bg-primary/30 text-green-700 dark:text-green-300 rounded-xl">Approved</span>
                    @break
                @case('rejected')
                    <span class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider bg-peach/50 text-red-700 dark:text-red-300 rounded-xl">Rejected</span>
                    @break
                @case('cancelled')
                    <span class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-500 rounded-xl">Cancelled</span>
                    @break
            @endswitch
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Dikirim pada {{ $submission->created_at->format('d M Y H:i') }}</p>
    </div>

    @if($submission->status === 'rejected' && $submission->rejection_reason)
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4">
            <p class="text-sm font-bold text-red-700 dark:text-red-300 mb-1">Alasan Penolakan:</p>
            <p class="text-sm text-red-600 dark:text-red-400">{{ $submission->rejection_reason }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="font-bold text-slate-900 dark:text-white mb-4">Data yang Dikirim</h2>
        <div class="space-y-4">
            @foreach($submission->template->fields ?? [] as $field)
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 last:border-0 last:pb-0">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ $field['label'] ?? $field['name'] }}</p>
                    @if(($field['type'] ?? 'text') === 'file' && isset($submission->attachments[$field['name']]))
                        <a href="{{ asset('storage/' . $submission->attachments[$field['name']]) }}" target="_blank"
                            class="text-sm text-primary hover:underline mt-1 inline-block">
                            {{ $submission->data[$field['name']] ?? 'Lihat File' }}
                        </a>
                    @else
                        <p class="text-sm text-slate-900 dark:text-white mt-1">{{ $submission->data[$field['name']] ?? '-' }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if($submission->approver)
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
            <h2 class="font-bold text-slate-900 dark:text-white mb-2">Info Approval</h2>
            <div class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                <p>Diproses oleh: <span class="font-medium text-slate-900 dark:text-white">{{ $submission->approver->name }}</span></p>
                @if($submission->approved_at)
                    <p>Tanggal: {{ $submission->approved_at->format('d M Y H:i') }}</p>
                @endif
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="font-bold text-slate-900 dark:text-white mb-4">Komentar ({{ $submission->comments->count() }})</h2>

        @if($submission->comments->isNotEmpty())
            <div class="space-y-4 mb-6">
                @foreach($submission->comments as $comment)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300 flex-shrink-0">
                            {{ substr($comment->user->name ?? '?', 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $comment->user->name ?? '-' }}</span>
                                <span class="text-xs text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">{{ $comment->comment }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('forms.submission.comment', $submission) }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="comment" placeholder="Tulis komentar..."
                class="flex-1 rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm" required>
            <button type="submit"
                class="px-4 py-2 bg-primary hover:bg-primary/80 text-slate-900 font-bold rounded-xl transition text-sm">
                Kirim
            </button>
        </form>
    </div>
</div>
@endsection
