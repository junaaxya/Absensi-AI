@extends('layouts.admin')

@section('header-title', 'Submissions: ' . $template->name)
@section('header-subtitle', 'Form Internal')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.forms.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1 mb-1">
                <span class="material-icons-round text-[16px]">arrow_back</span> Kembali
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $template->name }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $submissions->total() }} submission</p>
        </div>
        <a href="{{ route('admin.forms.export', $template) }}"
            class="px-4 py-2 bg-primary/20 hover:bg-primary/30 text-slate-700 dark:text-slate-200 font-medium rounded-xl transition text-sm flex items-center gap-2">
            <span class="material-icons-round text-[18px]">download</span>
            Export CSV
        </a>
    </div>

    <div class="flex items-center gap-2">
        @foreach(['' => 'Semua', 'submitted' => 'Submitted', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
            <a href="{{ route('admin.forms.submissions', array_merge(['template' => $template->id], $value ? ['status' => $value] : [])) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition
                {{ request('status', '') === $value ? 'bg-primary text-slate-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pengirim</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-900 dark:text-white text-sm">{{ $submission->submitter->name ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $submission->submitter->getRoleNames()->first() ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $submission->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @switch($submission->status)
                                    @case('submitted')
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-sky/30 text-blue-700 dark:text-blue-300 rounded-lg">Submitted</span>
                                        @break
                                    @case('approved')
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-primary/30 text-green-700 dark:text-green-300 rounded-lg">Approved</span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-peach/50 text-red-700 dark:text-red-300 rounded-lg">Rejected</span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-500 rounded-lg">{{ ucfirst($submission->status) }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.forms.submission.show', $submission) }}"
                                        class="p-1.5 rounded-lg hover:bg-sky/20 text-slate-500 hover:text-blue-600 transition" title="Detail">
                                        <span class="material-icons-round text-[18px]">visibility</span>
                                    </a>
                                    @if($submission->status === 'submitted' && $template->requires_approval)
                                        <form action="{{ route('admin.forms.submission.approve', $submission) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="p-1.5 rounded-lg hover:bg-primary/20 text-slate-500 hover:text-green-600 transition" title="Approve">
                                                <span class="material-icons-round text-[18px]">check_circle</span>
                                            </button>
                                        </form>
                                        <button type="button" x-data @click="$dispatch('open-reject', {{ $submission->id }})"
                                            class="p-1.5 rounded-lg hover:bg-peach/20 text-slate-500 hover:text-red-600 transition" title="Reject">
                                            <span class="material-icons-round text-[18px]">cancel</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                Belum ada submission.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
</div>

{{-- Reject Modal --}}
<div x-data="{ open: false, submissionId: null }"
    @open-reject.window="open = true; submissionId = $event.detail"
    x-show="open" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-6 w-full max-w-md relative z-10 shadow-xl">
        <h3 class="font-bold text-slate-900 dark:text-white mb-4">Tolak Submission</h3>
        <form :action="'/admin/forms/submissions/' + submissionId + '/reject'" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Penolakan *</label>
                <textarea name="rejection_reason" rows="3" required
                    class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="open = false"
                    class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition text-sm">Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection
