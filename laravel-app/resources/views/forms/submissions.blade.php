@extends('layouts.absensi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Submission Saya</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Riwayat form yang telah Anda kirim.</p>
        </div>
        <a href="{{ route('forms.index') }}"
            class="px-4 py-2 bg-primary/20 hover:bg-primary/30 text-slate-700 dark:text-slate-200 font-medium rounded-xl transition text-sm">
            Isi Form Baru
        </a>
    </div>

    <div class="flex items-center gap-2" x-data>
        @foreach(['', 'submitted', 'approved', 'rejected'] as $status)
            <a href="{{ route('forms.submissions', ['status' => $status]) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition
                {{ request('status', '') === $status ? 'bg-primary text-slate-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                {{ $status === '' ? 'Semua' : ucfirst($status) }}
            </a>
        @endforeach
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Form</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-900 dark:text-white text-sm">{{ $submission->template->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $submission->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @switch($submission->status)
                                    @case('draft')
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg">Draft</span>
                                        @break
                                    @case('submitted')
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-sky/30 text-blue-700 dark:text-blue-300 rounded-lg">Submitted</span>
                                        @break
                                    @case('approved')
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-primary/30 text-green-700 dark:text-green-300 rounded-lg">Approved</span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-peach/50 text-red-700 dark:text-red-300 rounded-lg">Rejected</span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-500 rounded-lg">Cancelled</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('forms.submission.show', $submission) }}"
                                    class="text-sm font-medium text-primary hover:underline">Detail</a>
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
@endsection
