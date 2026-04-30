@extends('layouts.absensi')

@section('content')
<div class="space-y-6" x-data="{ rejectModalOpen: false, rejectIzinId: null }">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Approval Izin Tim</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola pengajuan izin dan cuti anggota tim Anda</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-soft border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menunggu</p>
            <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $totalPending ?? 0 }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-soft border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Disetujui</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalApproved ?? 0 }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-soft border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ditolak</p>
            <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ $totalRejected ?? 0 }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-soft border border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $izins->total() }}</h3>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-center gap-3">
            <h3 class="font-bold text-slate-800 dark:text-white">Daftar Pengajuan</h3>
            <form method="GET" action="{{ route('team.izin-approval') }}">
                <select name="filter_status" onchange="this.form.submit()"
                    class="px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-sage">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('filter_status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('filter_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Karyawan</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jenis & Tanggal</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Alasan</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($izins as $izin)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        <img src="{{ $izin->user->profile_photo_url }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $izin->user->name }}</p>
                                        <p class="text-[11px] text-slate-500 truncate">{{ $izin->user->jabatan ?? $izin->user->getRoleNames()->first() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $jenisColors = [
                                        'sakit' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400',
                                        'izin' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                                        'cuti' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
                                        'dinas' => 'bg-teal-100 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400',
                                        'wfa' => 'bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400',
                                    ];
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $jenisColors[$izin->jenis] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $izin->jenis === 'wfa' ? 'WFA' : $izin->jenis }}
                                </span>
                                <p class="text-sm text-slate-700 dark:text-slate-300 mt-0.5">
                                    {{ $izin->tanggal_mulai?->format('d M') }} - {{ $izin->tanggal_selesai?->format('d M Y') }}
                                </p>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 max-w-xs">{{ $izin->alasan }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($izin->status == 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full text-[11px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($izin->status == 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full text-[11px] font-bold">
                                        <span class="material-icons-round text-[14px]">check</span>
                                        Disetujui
                                    </span>
                                @elseif($izin->status == 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 rounded-full text-[11px] font-bold">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @php
                                    $canApprove = $izin->status === 'pending' && (
                                        $izin->current_approver_id === auth()->id() ||
                                        auth()->user()->hasRole(['Direktur', 'Vice President'])
                                    );
                                @endphp
                                @if($canApprove)
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('team.izin.approve', $izin->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 hover:bg-emerald-200 flex items-center justify-center transition-colors" title="Setujui">
                                                <span class="material-icons-round text-lg">check</span>
                                            </button>
                                        </form>
                                        <button @click="rejectModalOpen = true; rejectIzinId = {{ $izin->id }}"
                                            class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-600 hover:bg-rose-200 flex items-center justify-center transition-colors" title="Tolak">
                                            <span class="material-icons-round text-lg">close</span>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mb-3">
                                        <span class="material-icons-round text-2xl text-slate-300 dark:text-slate-500">inbox</span>
                                    </div>
                                    <p class="text-slate-500 font-medium text-sm">Belum ada pengajuan dari tim Anda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-700">
            {{ $izins->links() }}
        </div>
    </div>

    {{-- Reject Modal --}}
    <div x-show="rejectModalOpen" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @keydown.escape.window="rejectModalOpen = false" x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4" @click.outside="rejectModalOpen = false">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tolak Pengajuan</h3>
            <form :action="'/team/izin/' + rejectIzinId + '/reject'" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Alasan Penolakan</label>
                    <textarea name="rejection_reason" rows="3" required
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 dark:text-white px-4 py-2 text-sm focus:ring-2 focus:ring-rose-200"
                        placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="rejectModalOpen = false"
                        class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition shadow-sm">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
