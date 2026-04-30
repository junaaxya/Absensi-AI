@extends('layouts.absensi')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <a href="{{ route('my-tasks.index') }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Task Saya
    </a>

    @php
        $statusClass = match($task->status) {
            'todo' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
            'in_progress' => 'bg-sky/30 text-sky-700 dark:bg-sky/20 dark:text-sky-400',
            'review' => 'bg-lavender/50 text-purple-700 dark:bg-lavender/20 dark:text-purple-400',
            'done' => 'bg-sage/50 text-green-700 dark:bg-sage/20 dark:text-green-400',
            default => 'bg-slate-100 text-slate-600',
        };
        $statusLabel = match($task->status) {
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'review' => 'Review',
            'done' => 'Done',
            default => $task->status,
        };
        $priorityClass = match($task->priority) {
            'critical' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
            'high' => 'bg-peach text-slate-900 dark:bg-peach/30 dark:text-orange-400',
            'medium' => 'bg-sky/30 text-slate-900 dark:bg-sky/20 dark:text-sky-400',
            'low' => 'bg-sage/50 text-slate-900 dark:bg-sage/20 dark:text-green-400',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $task->title }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Proyek: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $task->project->name ?? '-' }}</span>
                </p>
                <div class="flex items-center gap-2 mt-3">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                    @if($task->priority)
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $priorityClass }}">{{ ucfirst($task->priority) }}</span>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('my-tasks.update-status', $task) }}">
                @csrf
                @method('PUT')
                <div class="flex items-center gap-2">
                    <select name="status" class="text-sm px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-lavender">
                        <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>To Do</option>
                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="review" {{ $task->status === 'review' ? 'selected' : '' }}>Review</option>
                        <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-lavender text-slate-900 dark:text-white rounded-xl font-bold text-sm hover:brightness-95 transition-all">
                        Update
                    </button>
                </div>
            </form>
        </div>

        @if($task->description)
            <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</h3>
                <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed prose prose-sm dark:prose-invert max-w-none">
                    {!! nl2br(e($task->description)) !!}
                </div>
            </div>
        @endif

        <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-medium">Tenggat</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                    {{ $task->due_date ? $task->due_date->translatedFormat('d M Y') : '-' }}
                </p>
            </div>
            <div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-medium">Dibuat</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                    {{ $task->created_at->translatedFormat('d M Y') }}
                </p>
            </div>
            <div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-medium">Estimasi</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                    {{ $task->estimated_hours ? $task->estimated_hours . ' jam' : '-' }}
                </p>
            </div>
            <div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-medium">Total Waktu</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                    @if($totalMinutes > 0)
                        {{ floor($totalMinutes / 60) }}j {{ $totalMinutes % 60 }}m
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 mb-6">
        <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Time Tracking
        </h3>

        @if($activeTimer)
            <div class="flex items-center justify-between bg-sky/10 dark:bg-sky/5 rounded-xl p-4 border border-sky/30 dark:border-sky/20">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-sky-500 animate-pulse"></div>
                    <div>
                        <p class="text-sm font-bold text-sky-700 dark:text-sky-400">Timer Berjalan</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Dimulai {{ $activeTimer->started_at->diffForHumans() }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('my-tasks.stop-timer', $task) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-500 text-white rounded-xl font-bold text-sm hover:bg-rose-600 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                        Stop
                    </button>
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('my-tasks.start-timer', $task) }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky/20 text-sky-700 dark:bg-sky/10 dark:text-sky-400 rounded-xl font-bold text-sm hover:bg-sky/30 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Mulai Timer
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-lavender" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            Komentar
        </h3>

        @if($task->comments->count() > 0)
            <div class="space-y-4 mb-6">
                @foreach($task->comments as $comment)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-lavender/30 dark:bg-lavender/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-purple-700 dark:text-purple-400">{{ substr($comment->user->name ?? '?', 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $comment->user->name ?? 'Unknown' }}</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">{{ $comment->comment }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-slate-400 dark:text-slate-500 mb-6">Belum ada komentar.</p>
        @endif

        <form method="POST" action="{{ route('my-tasks.comment', $task) }}">
            @csrf
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-primary/30 dark:bg-primary/20 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-slate-900 dark:text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1">
                    <textarea name="comment" rows="3" required placeholder="Tulis komentar..."
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-lavender focus:border-transparent resize-none"></textarea>
                    @error('comment')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-lavender text-slate-900 dark:text-white rounded-xl font-bold text-sm hover:brightness-95 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Kirim
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
