@extends('layouts.absensi')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-lavender/30 flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Task Saya</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Daftar tugas yang ditugaskan kepada Anda.</p>
        </div>
    </div>

    <div class="mb-6">
        <form method="GET" action="{{ route('my-tasks.index') }}" class="flex gap-2 flex-wrap">
            <select name="status" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-lavender">
                <option value="">Semua Status</option>
                <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>To Do</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="review" {{ request('status') === 'review' ? 'selected' : '' }}>Review</option>
                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all">Filter</button>
            <a href="{{ route('my-tasks.index') }}" class="px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Reset</a>
        </form>
    </div>

    @if($tasks->count() > 0)
        <div class="space-y-6">
            @foreach($tasksByProject as $projectName => $projectTasks)
                <div x-data="{ open: true }" class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-lavender/30 dark:bg-lavender/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $projectName }}</span>
                            <span class="text-xs font-medium text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full">{{ $projectTasks->count() }}</span>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" x-collapse class="border-t border-slate-100 dark:border-slate-700">
                        <div class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($projectTasks as $task)
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
                                <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <a href="{{ route('my-tasks.show', $task) }}" class="flex-1 min-w-0">
                                        <p class="font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $task->title }}</p>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                                            @if($task->priority)
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $priorityClass }}">{{ ucfirst($task->priority) }}</span>
                                            @endif
                                            @if($task->due_date)
                                                <span class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    {{ $task->due_date->translatedFormat('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                    <form method="POST" action="{{ route('my-tasks.update-status', $task) }}" class="ml-4 flex-shrink-0" x-data>
                                        @csrf
                                        @method('PUT')
                                        <select name="status" @change="$el.form.submit()"
                                            class="text-xs px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-lavender">
                                            <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>To Do</option>
                                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="review" {{ $task->status === 'review' ? 'selected' : '' }}>Review</option>
                                            <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                                        </select>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $tasks->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="font-bold text-slate-600 dark:text-slate-400">Belum ada task</p>
                <p class="text-sm text-slate-500 dark:text-slate-500">Task yang ditugaskan kepada Anda akan muncul di sini.</p>
            </div>
        </div>
    @endif
</div>
@endsection
