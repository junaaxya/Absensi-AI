@extends('layouts.admin')

@section('header-title', 'Audit Log')
@section('header-subtitle', 'Riwayat Aktivitas Sistem')

@section('content')

    <!-- FILTERS -->
    <div class="bg-white dark:bg-card-dark rounded-2xl p-4 mb-6 shadow-sm border border-slate-200 dark:border-slate-800">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user, model, atau aksi..."
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white text-sm" />
            </div>
            <div class="w-full md:w-48">
                <select name="action" onchange="this.form.submit()"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white text-sm">
                    <option value="">Semua Aksi</option>
                    <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div class="w-full md:w-40">
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari tanggal"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white text-sm" />
            </div>
            <div class="w-full md:w-40">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai tanggal"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white text-sm" />
            </div>
            <button type="submit"
                class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg active:scale-95">
                Filter
            </button>
        </form>
    </div>

    <!-- AUDIT LOG TABLE -->
    <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">User</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Model / Target</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Perubahan</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="p-4 whitespace-nowrap">
                                <div class="font-bold text-slate-700 dark:text-slate-300 text-sm">
                                    {{ $log->created_at->format('d M Y') }}
                                </div>
                                <div class="text-xs text-slate-400 font-mono">
                                    {{ $log->created_at->format('H:i:s') }}
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 text-xs font-bold">
                                        {{ substr($log->user->name ?? 'System', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">
                                            {{ $log->user->name ?? 'System' }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ $log->user->role ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                @php
                                    $badgeColor = match($log->event) {
                                        'created' => 'bg-green-100 text-green-700 border-green-200',
                                        'updated' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'deleted' => 'bg-red-100 text-red-700 border-red-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border {{ $badgeColor }} uppercase tracking-wide">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="font-mono text-xs text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded w-fit">
                                    {{ class_basename($log->auditable_type) }}
                                </div>
                                <div class="text-xs text-slate-400 mt-1">
                                    ID: {{ $log->auditable_id }}
                                </div>
                            </td>
                            <td class="p-4" x-data="{ expanded: false }">
                                <div class="text-sm text-slate-600 dark:text-slate-400 max-w-xs">
                                    @if($log->changes)
                                        <div class="relative">
                                            <div :class="expanded ? '' : 'line-clamp-2'" class="font-mono text-xs bg-slate-50 dark:bg-slate-900 p-2 rounded border border-slate-100 dark:border-slate-800">
                                                @foreach($log->changes as $key => $change)
                                                    <div class="mb-1">
                                                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $key }}:</span>
                                                        <span class="text-red-500 line-through">{{ $change['old'] ?? 'null' }}</span>
                                                        <span class="text-slate-400">→</span>
                                                        <span class="text-green-600 font-bold">{{ $change['new'] ?? 'null' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button @click="expanded = !expanded" class="text-xs text-sage font-bold mt-1 hover:underline">
                                                <span x-text="expanded ? 'Tutup' : 'Lihat Detail'"></span>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic text-xs">Tidak ada detail perubahan</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-right">
                                <span class="font-mono text-xs text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">
                                    {{ $log->ip_address }}
                                </span>
                                @if($log->user_agent)
                                    <div class="text-[10px] text-slate-400 mt-1 max-w-[200px] truncate ml-auto" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                        <span class="material-icons-round text-slate-400 text-3xl">history</span>
                                    </div>
                                    <h3 class="text-slate-900 dark:text-white font-bold text-lg">Belum ada aktivitas</h3>
                                    <p class="text-slate-500 text-sm mt-1">Riwayat aktivitas sistem akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- PAGINATION -->
        @if($auditLogs->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>

@endsection
