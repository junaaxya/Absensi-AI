@extends('layouts.admin')

@section('header-title', $project->name)
@section('header-subtitle', 'Kanban Board — Project Management')

@section('content')

<div x-data="kanbanBoard()" x-cloak>

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.projects.index') }}" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-icons-round text-slate-400">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-bold text-xl text-slate-900 dark:text-white">{{ $project->name }}</h1>
                    @php
                        $statusColor = match($project->status) {
                            'planning' => 'bg-slate-100 text-slate-700',
                            'active' => 'bg-sky-100 text-sky-700',
                            'on_hold' => 'bg-amber-100 text-amber-700',
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'cancelled' => 'bg-rose-100 text-rose-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide {{ $statusColor }}">
                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                    </span>
                </div>
                @if($project->description)
                    <p class="text-sm text-slate-500 mt-1 line-clamp-1">{{ $project->description }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.projects.edit', $project) }}"
                class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold text-sm hover:bg-slate-200 transition-all flex items-center gap-2">
                <span class="material-icons-round text-lg">edit</span>
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-4">
            <div class="flex items-center gap-2 text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">
                <span class="material-icons-round text-sm">task_alt</span>
                Total Task
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalTasks }}</p>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-4">
            <div class="flex items-center gap-2 text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">
                <span class="material-icons-round text-sm">check_circle</span>
                Selesai
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $completedTasks }}</p>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-4">
            <div class="flex items-center gap-2 text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">
                <span class="material-icons-round text-sm">trending_up</span>
                Progress
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $progressPercent }}%</p>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-4">
            <div class="flex items-center gap-2 text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">
                <span class="material-icons-round text-sm">group</span>
                Anggota
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $projectMembers->count() }}</p>
        </div>
    </div>

    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 mb-6">
        <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%; background-color: {{ $project->color ?? '#B8D4E3' }}"></div>
    </div>

    @php
        $columns = [
            'backlog' => ['label' => 'Backlog', 'color' => 'bg-stone-100 dark:bg-stone-900/30', 'border' => 'border-stone-200 dark:border-stone-800', 'badge' => 'bg-stone-200 text-stone-700', 'icon' => 'inventory_2'],
            'todo' => ['label' => 'To Do', 'color' => 'bg-sky-50 dark:bg-sky-900/20', 'border' => 'border-sky-200 dark:border-sky-800', 'badge' => 'bg-sky-200 text-sky-700', 'icon' => 'radio_button_unchecked'],
            'in_progress' => ['label' => 'In Progress', 'color' => 'bg-violet-50 dark:bg-violet-900/20', 'border' => 'border-violet-200 dark:border-violet-800', 'badge' => 'bg-violet-200 text-violet-700', 'icon' => 'pending'],
            'review' => ['label' => 'Review', 'color' => 'bg-orange-50 dark:bg-orange-900/20', 'border' => 'border-orange-200 dark:border-orange-800', 'badge' => 'bg-orange-200 text-orange-700', 'icon' => 'rate_review'],
            'done' => ['label' => 'Done', 'color' => 'bg-emerald-50 dark:bg-emerald-900/20', 'border' => 'border-emerald-200 dark:border-emerald-800', 'badge' => 'bg-emerald-200 text-emerald-700', 'icon' => 'check_circle'],
        ];
    @endphp

    <div class="flex gap-4 overflow-x-auto pb-4 -mx-2 px-2" style="min-height: 60vh;">
        @foreach($columns as $status => $col)
            <div class="flex-shrink-0 w-72 flex flex-col rounded-2xl {{ $col['color'] }} border {{ $col['border'] }}"
                @dragover.prevent="onDragOver($event, '{{ $status }}')"
                @drop.prevent="onDrop($event, '{{ $status }}')"
                @dragleave="onDragLeave($event)">

                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-icons-round text-lg text-slate-500">{{ $col['icon'] }}</span>
                        <h3 class="font-bold text-sm text-slate-700 dark:text-slate-300">{{ $col['label'] }}</h3>
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold {{ $col['badge'] }}">
                            {{ $tasksByStatus[$status]->count() }}
                        </span>
                    </div>
                    <button @click="openCreateTask('{{ $status }}')"
                        class="p-1 rounded-lg hover:bg-white/60 dark:hover:bg-slate-800/60 text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-icons-round text-lg">add</span>
                    </button>
                </div>

                <div class="flex-1 p-2 space-y-2 overflow-y-auto" data-status="{{ $status }}">
                    @foreach($tasksByStatus[$status] as $task)
                        <div class="bg-white dark:bg-card-dark rounded-xl border border-slate-200 dark:border-slate-800 p-3 cursor-grab active:cursor-grabbing hover:shadow-md transition-all group"
                            draggable="true"
                            @dragstart="onDragStart($event, {{ $task->id }})"
                            @dragend="onDragEnd($event)"
                            @click="openTaskDetail({{ $task->toJson() }})">

                            @php
                                $taskPriorityColor = match($task->priority) {
                                    'low' => 'bg-slate-100 text-slate-600',
                                    'medium' => 'bg-sky-100 text-sky-600',
                                    'high' => 'bg-amber-100 text-amber-600',
                                    'critical' => 'bg-rose-100 text-rose-600',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp

                            <div class="flex items-start justify-between mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide {{ $taskPriorityColor }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                <span class="material-icons-round text-sm text-slate-300 group-hover:text-slate-500 transition-colors">drag_indicator</span>
                            </div>

                            <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-2 line-clamp-2">{{ $task->title }}</h4>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($task->assignee)
                                        <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden" title="{{ $task->assignee->name }}">
                                            <img src="{{ $task->assignee->profile_photo_url }}" alt="{{ $task->assignee->name }}" class="w-full h-full object-cover" />
                                        </div>
                                    @endif
                                    @if($task->subtasks->count() > 0)
                                        <span class="text-[10px] text-slate-400 flex items-center gap-0.5">
                                            <span class="material-icons-round text-xs">account_tree</span>
                                            {{ $task->subtasks->where('status', 'done')->count() }}/{{ $task->subtasks->count() }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($task->comments->count() > 0)
                                        <span class="text-[10px] text-slate-400 flex items-center gap-0.5">
                                            <span class="material-icons-round text-xs">chat_bubble_outline</span>
                                            {{ $task->comments->count() }}
                                        </span>
                                    @endif
                                    @if($task->due_date)
                                        <span class="text-[10px] {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-rose-500 font-bold' : 'text-slate-400' }} flex items-center gap-0.5">
                                            <span class="material-icons-round text-xs">event</span>
                                            {{ $task->due_date->format('d M') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div x-show="showCreateModal" style="display: none;" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-lg shadow-2xl" @click.outside="showCreateModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Task Baru</h3>
                <button @click="showCreateModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    <span class="material-icons-round text-slate-400">close</span>
                </button>
            </div>

            <form action="{{ route('admin.tasks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                <input type="hidden" name="status" x-model="createTaskStatus" />

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Task <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white"
                            placeholder="Judul task..." />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white"
                            placeholder="Deskripsi task..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Prioritas</label>
                            <select name="priority"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Assign ke</label>
                            <select name="assigned_to"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">
                                <option value="">Belum ditentukan</option>
                                @foreach($projectMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Due Date</label>
                            <input type="date" name="due_date"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Estimasi (jam)</label>
                            <input type="number" name="estimated_hours" step="0.5" min="0"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white"
                                placeholder="0" />
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                            Simpan Task
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showDetailModal" style="display: none;" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto" @click.outside="showDetailModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white" x-text="selectedTask?.title"></h3>
                <button @click="showDetailModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    <span class="material-icons-round text-slate-400">close</span>
                </button>
            </div>

            <template x-if="selectedTask">
                <div class="space-y-6">
                    <form :action="`{{ url('admin/tasks') }}/${selectedTask.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul</label>
                                <input type="text" name="title" x-model="selectedTask.title" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                                <textarea name="description" rows="3" x-model="selectedTask.description"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white"></textarea>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Status</label>
                                    <select name="status" x-model="selectedTask.status"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">
                                        <option value="backlog">Backlog</option>
                                        <option value="todo">To Do</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="review">Review</option>
                                        <option value="done">Done</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Prioritas</label>
                                    <select name="priority" x-model="selectedTask.priority"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Assign ke</label>
                                    <select name="assigned_to" x-model="selectedTask.assigned_to"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">
                                        <option value="">Belum ditentukan</option>
                                        @foreach($projectMembers as $member)
                                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Due Date</label>
                                    <input type="date" name="due_date" x-model="selectedTask.due_date"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Estimasi (jam)</label>
                                    <input type="number" name="estimated_hours" step="0.5" min="0" x-model="selectedTask.estimated_hours"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-4 border-t border-slate-100 dark:border-slate-800">
                                <form :action="`{{ url('admin/tasks') }}/${selectedTask.id}`" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus task ini?')"
                                        class="px-4 py-2 bg-rose-50 dark:bg-rose-900/20 text-rose-500 rounded-xl font-bold text-sm hover:bg-rose-100 transition-all flex items-center gap-1">
                                        <span class="material-icons-round text-sm">delete</span>
                                        Hapus
                                    </button>
                                </form>
                                <button type="submit"
                                    class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                    Update Task
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-sm text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                <span class="material-icons-round text-lg">timer</span>
                                Time Tracking
                            </h4>
                            <form :action="`{{ url('admin/tasks') }}/${selectedTask.id}/timer/start`" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-xs hover:bg-emerald-200 transition-all flex items-center gap-1">
                                    <span class="material-icons-round text-sm">play_arrow</span>
                                    Mulai Timer
                                </button>
                            </form>
                        </div>

                        <template x-if="selectedTask.time_entries && selectedTask.time_entries.length > 0">
                            <div class="space-y-2">
                                <template x-for="entry in selectedTask.time_entries" :key="entry.id">
                                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                                <img :src="entry.user?.profile_photo_url || ''" :alt="entry.user?.name || ''" class="w-full h-full object-cover" />
                                            </div>
                                            <span class="font-medium text-slate-700 dark:text-slate-300" x-text="entry.user?.name"></span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <template x-if="entry.duration_minutes">
                                                <span class="text-slate-500 text-xs" x-text="Math.floor(entry.duration_minutes / 60) + 'j ' + (entry.duration_minutes % 60) + 'm'"></span>
                                            </template>
                                            <template x-if="!entry.ended_at">
                                                <form :action="`{{ url('admin/time-entries') }}/${entry.id}/stop`" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="px-2 py-1 bg-rose-100 text-rose-600 rounded-lg font-bold text-xs hover:bg-rose-200 transition-all flex items-center gap-1">
                                                        <span class="material-icons-round text-xs">stop</span>
                                                        Stop
                                                    </button>
                                                </form>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
                        <h4 class="font-bold text-sm text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                            <span class="material-icons-round text-lg">chat</span>
                            Komentar
                            <span class="text-xs font-normal text-slate-400" x-text="selectedTask.comments ? `(${selectedTask.comments.length})` : '(0)'"></span>
                        </h4>

                        <template x-if="selectedTask.comments && selectedTask.comments.length > 0">
                            <div class="space-y-3 mb-4">
                                <template x-for="comment in selectedTask.comments" :key="comment.id">
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden shrink-0">
                                            <img :src="comment.user?.profile_photo_url || ''" :alt="comment.user?.name || ''" class="w-full h-full object-cover" />
                                        </div>
                                        <div class="flex-1 bg-slate-50 dark:bg-slate-800 rounded-xl p-3">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-bold text-xs text-slate-700 dark:text-slate-300" x-text="comment.user?.name"></span>
                                                <span class="text-[10px] text-slate-400" x-text="new Date(comment.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })"></span>
                                            </div>
                                            <p class="text-sm text-slate-600 dark:text-slate-400" x-text="comment.comment"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <form :action="`{{ url('admin/tasks') }}/${selectedTask.id}/comments`" method="POST" class="flex gap-3">
                            @csrf
                            <input type="text" name="comment" required placeholder="Tulis komentar..."
                                class="flex-1 px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-sm text-slate-900 dark:text-white" />
                            <button type="submit"
                                class="px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all">
                                Kirim
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function kanbanBoard() {
    return {
        showCreateModal: false,
        showDetailModal: false,
        createTaskStatus: 'backlog',
        selectedTask: null,
        draggedTaskId: null,

        openCreateTask(status) {
            this.createTaskStatus = status;
            this.showCreateModal = true;
        },

        openTaskDetail(task) {
            if (task.due_date) {
                task.due_date = task.due_date.split('T')[0];
            }
            this.selectedTask = task;
            this.showDetailModal = true;
        },

        onDragStart(event, taskId) {
            this.draggedTaskId = taskId;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', taskId);
            event.target.classList.add('opacity-50', 'scale-95');
        },

        onDragEnd(event) {
            event.target.classList.remove('opacity-50', 'scale-95');
            this.draggedTaskId = null;
            document.querySelectorAll('[data-status]').forEach(el => {
                el.classList.remove('ring-2', 'ring-sky-300', 'bg-sky-50/50');
            });
        },

        onDragOver(event, status) {
            event.preventDefault();
            const column = event.currentTarget.querySelector('[data-status]');
            if (column) {
                column.classList.add('ring-2', 'ring-sky-300', 'bg-sky-50/50');
            }
        },

        onDragLeave(event) {
            const column = event.currentTarget.querySelector('[data-status]');
            if (column && !event.currentTarget.contains(event.relatedTarget)) {
                column.classList.remove('ring-2', 'ring-sky-300', 'bg-sky-50/50');
            }
        },

        async onDrop(event, newStatus) {
            event.preventDefault();
            const taskId = event.dataTransfer.getData('text/plain');

            document.querySelectorAll('[data-status]').forEach(el => {
                el.classList.remove('ring-2', 'ring-sky-300', 'bg-sky-50/50');
            });

            if (!taskId) return;

            try {
                const response = await fetch(`{{ url('admin/tasks') }}/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        order: 0,
                    }),
                });

                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                window.location.reload();
            }
        },
    };
}
</script>
@endpush
