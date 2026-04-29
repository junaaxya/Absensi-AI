@extends('layouts.admin')

@section('header-title', 'Manajemen Ketidakhadiran')
@section('header-subtitle', 'Kelola Pengajuan Izin dan Sakit Karyawan')

@section('content')

    <div x-data="{ rejectModalOpen: false, rejectIzinId: null }">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menunggu Persetujuan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalPending ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-500">
                    <span class="material-icons-round text-2xl">hourglass_empty</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Disetujui Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalApproved ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-500">
                    <span class="material-icons-round text-2xl">check_circle</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ditolak Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalRejected ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-500">
                    <span class="material-icons-round text-2xl">cancel</span>
                </div>
            </div>

             <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pengajuan</p>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $izins->total() }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-500">
                    <span class="material-icons-round text-2xl">folder_shared</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white">Daftar Pengajuan</h3>

                 <form method="GET" action="{{ route('admin.absence.index') }}" class="flex items-center gap-2">
                    <select name="filter_status" onchange="this.form.submit()" class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-lavender">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('filter_status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('filter_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis & Tanggal</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Alasan</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Lampiran</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Level Approval</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($izins as $izin)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                             <img src="{{ $izin->user->profile_photo_url }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $izin->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $izin->user->jabatan }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $jenisColors = [
                                            'sakit' => 'bg-orange-100 text-orange-600',
                                            'izin' => 'bg-blue-100 text-blue-600',
                                            'cuti' => 'bg-purple-100 text-purple-600',
                                            'dinas' => 'bg-teal-100 text-teal-600',
                                            'wfa' => 'bg-sky-100 text-sky-600',
                                        ];
                                    @endphp
                                    <span class="inline-block px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider mb-1 {{ $jenisColors[$izin->jenis] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $izin->jenis === 'wfa' ? 'WFA' : $izin->jenis }}
                                    </span>
                                    @if($izin->jenis === 'wfa' && $izin->wfa_location)
                                        <span class="block text-[10px] text-gray-400">{{ $izin->wfa_location }}</span>
                                    @endif
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $izin->tanggal_mulai?->format('d M') }} - {{ $izin->tanggal_selesai?->format('d M Y') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 max-w-xs">{{ $izin->alasan }}</p>
                                </td>
                                <td class="px-6 py-4">
                                     @if($izin->dokumen)
                                        <a href="{{ asset('storage/' . $izin->dokumen) }}" target="_blank" class="flex items-center gap-2 text-indigo-500 hover:text-indigo-600 text-sm font-medium">
                                            <span class="material-icons-round text-base">attach_file</span>
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $approvalSteps = ['pending' => 'P', 'approved_l1' => 'L1', 'approved_l2' => 'L2', 'approved_final' => 'OK'];
                                        $statusOrder = ['pending', 'approved_l1', 'approved_l2', 'approved_final'];
                                        $currentIdx = array_search($izin->approval_status, $statusOrder);
                                        $isRejected = $izin->approval_status === 'rejected';
                                    @endphp
                                    @if($isRejected)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-600 rounded-full text-[10px] font-bold">Ditolak</span>
                                    @else
                                        <div class="flex items-center justify-center gap-0.5">
                                            @foreach($statusOrder as $idx => $step)
                                                <span class="w-5 h-5 rounded-full text-[9px] font-bold flex items-center justify-center
                                                    {{ $idx <= $currentIdx ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-400' }}">
                                                    {{ $approvalSteps[$step] }}
                                                </span>
                                                @if(!$loop->last)
                                                    <span class="w-2 h-0.5 {{ $idx < $currentIdx ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600' }}"></span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($izin->currentApprover && !$isRejected && $izin->approval_status !== 'approved_final')
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $izin->currentApprover->name }}</p>
                                    @endif

                                    @if($izin->approved_by && count($izin->approved_by) > 0)
                                        <div class="mt-1 space-y-0.5">
                                            @foreach($izin->approved_by as $log)
                                                <p class="text-[9px] text-gray-400">
                                                    {{ $log['role'] ?? '' }}: {{ $log['action'] === 'approved' ? 'Setuju' : 'Tolak' }}
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($izin->status == 'pending')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                                            <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                                            Pending
                                        </span>
                                    @elseif($izin->status == 'approved')
                                         <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                            <span class="material-icons-round text-sm">check</span>
                                            Disetujui
                                        </span>
                                    @elseif($izin->status == 'rejected')
                                         <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $canApprove = $izin->status === 'pending' && (
                                            $izin->current_approver_id === auth()->id() ||
                                            auth()->user()->hasRole(['Direktur', 'Vice President'])
                                        );
                                    @endphp
                                    @if($canApprove)
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.izin.approve', $izin->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center transition-colors" title="Setujui">
                                                    <span class="material-icons-round text-lg">check</span>
                                                </button>
                                            </form>
                                            <button @click="rejectModalOpen = true; rejectIzinId = {{ $izin->id }}"
                                                class="w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition-colors" title="Tolak">
                                                <span class="material-icons-round text-lg">close</span>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                            <span class="material-icons-round text-3xl">inbox</span>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada pengajuan izin/sakit.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $izins->links() }}
            </div>
        </div>

        <div x-show="rejectModalOpen" x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            @keydown.escape.window="rejectModalOpen = false">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4" @click.outside="rejectModalOpen = false">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tolak Pengajuan</h3>
                <form :action="'/admin/izin/' + rejectIzinId + '/reject'" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Alasan Penolakan</label>
                        <textarea name="rejection_reason" rows="3" required
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 dark:text-white px-4 py-2 text-sm focus:ring-2 focus:ring-red-200"
                            placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="rejectModalOpen = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-bold text-white bg-red-500 hover:bg-red-600 rounded-xl transition shadow-sm">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
