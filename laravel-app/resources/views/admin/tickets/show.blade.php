@extends('layouts.admin')

@section('header-title', 'Detail Tiket')
@section('header-subtitle', $ticket->ticket_number)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('admin.tickets.index') }}" class="flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white transition">
            <span class="material-icons-round text-base">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
                    <div>
                        <p class="font-mono text-xs font-bold text-sky mb-1">{{ $ticket->ticket_number }}</p>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $ticket->subject }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Oleh <span class="font-semibold">{{ $ticket->creator->name ?? '-' }}</span>
                            &middot; {{ $ticket->created_at->translatedFormat('d M Y H:i') }}
                        </p>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        @php
                            $priorityClass = match($ticket->priority) {
                                'critical' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                'high' => 'bg-peach text-slate-900',
                                'medium' => 'bg-sky/30 text-slate-900 dark:text-sky',
                                'low' => 'bg-sage/50 text-slate-900',
                                default => 'bg-slate-100 text-slate-600',
                            };
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
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $priorityClass }}">{{ ucfirst($ticket->priority) }}</span>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</p>
                    <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-wrap">{{ $ticket->description }}</div>
                </div>

                @if($ticket->resolution)
                    <div class="mt-4 p-4 bg-sage/20 dark:bg-sage/10 rounded-xl border border-sage/30">
                        <p class="text-sm font-bold text-green-800 dark:text-green-400 mb-1">Resolusi</p>
                        <div class="text-sm text-green-700 dark:text-green-300 whitespace-pre-wrap">{{ $ticket->resolution }}</div>
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Komentar ({{ $comments->count() }})</h3>

                <div class="space-y-4 mb-6">
                    @forelse($comments as $comment)
                        <div class="flex gap-3 {{ $comment->is_internal ? 'bg-amber-50 dark:bg-amber-900/10 -mx-2 px-2 py-2 rounded-xl border border-amber-200 dark:border-amber-800/30' : '' }}">
                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $comment->user->name ?? '-' }}</span>
                                    @if($comment->is_internal)
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">Internal</span>
                                    @endif
                                    <span class="text-xs text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-wrap bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3">{{ $comment->comment }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-4">Belum ada komentar.</p>
                    @endforelse
                </div>

                @if(!in_array($ticket->status, ['closed', 'cancelled']))
                    <form method="POST" action="{{ url('/admin/tickets/' . $ticket->id . '/comment') }}" class="border-t border-slate-100 dark:border-slate-700 pt-4">
                        @csrf
                        <textarea name="comment" rows="3" required maxlength="5000"
                            placeholder="Tulis komentar..."
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage focus:border-sage resize-y mb-3"></textarea>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-slate-300 dark:border-slate-600 text-amber-500 focus:ring-amber-500">
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Catatan Internal (tidak terlihat oleh pemohon)</span>
                            </label>
                            <button type="submit"
                                class="px-5 py-2.5 bg-sage text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all shadow-sm">
                                Kirim
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Assign Tiket</h3>
                <form method="POST" action="{{ url('/admin/tickets/' . $ticket->id . '/assign') }}">
                    @csrf
                    <select name="assigned_to" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage mb-3">
                        <option value="">Pilih Staff</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ $ticket->assigned_to == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full px-4 py-2.5 bg-lavender text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all">
                        Assign
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Ubah Status</h3>
                <form method="POST" action="{{ url('/admin/tickets/' . $ticket->id . '/status') }}">
                    @csrf
                    @method('PUT')
                    <select name="status" id="admin-status-select" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage mb-3">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="waiting" {{ $ticket->status === 'waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="cancelled" {{ $ticket->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <div id="resolution-field" class="mb-3 hidden">
                        <textarea name="resolution" rows="3" maxlength="5000"
                            placeholder="Tuliskan resolusi..."
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage resize-y">{{ $ticket->resolution }}</textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 bg-sage text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all">
                        Perbarui Status
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">SLA Info</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Kategori</p>
                        <p class="text-slate-700 dark:text-slate-300 font-medium">{{ $ticket->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Response Deadline</p>
                        <div class="flex items-center gap-2">
                            @php
                                $slaResponseColor = match($ticket->sla_response_status) {
                                    'green' => 'bg-green-500',
                                    'yellow' => 'bg-amber-400',
                                    'red' => 'bg-rose-500',
                                    default => 'bg-slate-300',
                                };
                            @endphp
                            <span class="w-2 h-2 rounded-full {{ $slaResponseColor }}"></span>
                            <p class="text-slate-700 dark:text-slate-300 font-medium">
                                {{ $ticket->sla_response_deadline ? $ticket->sla_response_deadline->translatedFormat('d M Y H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Resolution Deadline</p>
                        <div class="flex items-center gap-2">
                            @php
                                $slaResolutionColor = match($ticket->sla_resolution_status) {
                                    'green' => 'bg-green-500',
                                    'yellow' => 'bg-amber-400',
                                    'red' => 'bg-rose-500',
                                    default => 'bg-slate-300',
                                };
                            @endphp
                            <span class="w-2 h-2 rounded-full {{ $slaResolutionColor }}"></span>
                            <p class="text-slate-700 dark:text-slate-300 font-medium">
                                {{ $ticket->sla_resolution_deadline ? $ticket->sla_resolution_deadline->translatedFormat('d M Y H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Response Met</p>
                        <p class="text-slate-700 dark:text-slate-300 font-medium">
                            @if(is_null($ticket->sla_response_met)) Pending
                            @elseif($ticket->sla_response_met) <span class="text-green-600">Ya</span>
                            @else <span class="text-rose-600">Tidak</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Resolution Met</p>
                        <p class="text-slate-700 dark:text-slate-300 font-medium">
                            @if(is_null($ticket->sla_resolution_met)) Pending
                            @elseif($ticket->sla_resolution_met) <span class="text-green-600">Ya</span>
                            @else <span class="text-rose-600">Tidak</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const statusSelect = document.getElementById('admin-status-select');
    const resolutionField = document.getElementById('resolution-field');
    function toggleResolution() {
        resolutionField.classList.toggle('hidden', statusSelect.value !== 'resolved');
    }
    statusSelect?.addEventListener('change', toggleResolution);
    toggleResolution();
</script>
@endpush
