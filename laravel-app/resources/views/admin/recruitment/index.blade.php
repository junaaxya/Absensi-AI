@extends('layouts.admin')

@section('header-title', 'Rekrutmen')
@section('header-subtitle', 'Dashboard pipeline rekrutmen')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Open Positions --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-sky/30 flex items-center justify-center">
                <span class="material-icons-round text-sky-700 dark:text-sky-300 text-[24px]">work</span>
            </div>
            <a href="{{ route('admin.recruitment.positions', ['status' => 'open']) }}"
                class="text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">Lihat &rarr;</a>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $openPositions }}</p>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Posisi Terbuka</p>
    </div>

    {{-- Active Candidates --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-lavender/30 flex items-center justify-center">
                <span class="material-icons-round text-purple-700 dark:text-purple-300 text-[24px]">people</span>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $activeCandidates }}</p>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kandidat Aktif</p>
    </div>

    {{-- Interviews This Week --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-peach/30 flex items-center justify-center">
                <span class="material-icons-round text-orange-700 dark:text-orange-300 text-[24px]">event</span>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $upcomingInterviews->count() }}</p>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Interview Minggu Ini</p>
    </div>

    {{-- Hired This Month --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-2xl bg-primary/30 flex items-center justify-center">
                <span class="material-icons-round text-green-700 dark:text-green-300 text-[24px]">how_to_reg</span>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $hiredThisMonth }}</p>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Diterima Bulan Ini</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Recent Candidates --}}
    <div class="lg:col-span-2 bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 dark:text-white">Kandidat Terbaru</h3>
            <a href="{{ route('admin.recruitment.positions') }}"
                class="text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">Semua Posisi &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <th class="px-6 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Posisi</th>
                        <th class="px-6 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentCandidates as $candidate)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.recruitment.candidates.show', $candidate) }}"
                                class="font-bold text-slate-900 dark:text-white hover:text-sky-600 transition-colors">
                                {{ $candidate->name }}
                            </a>
                        </td>
                        <td class="px-6 py-3 text-slate-600 dark:text-slate-400">
                            {{ $candidate->jobPosition->title ?? '-' }}
                        </td>
                        <td class="px-6 py-3">
                            @php
                                $statusColors = [
                                    'applied' => 'bg-neutral-stone/60 text-slate-700 dark:text-slate-300',
                                    'screening' => 'bg-sky/40 text-sky-800 dark:text-sky-200',
                                    'interview' => 'bg-lavender/40 text-purple-800 dark:text-purple-200',
                                    'assessment' => 'bg-peach/40 text-orange-800 dark:text-orange-200',
                                    'offered' => 'bg-green-100 text-green-800 dark:text-green-200',
                                    'hired' => 'bg-primary/40 text-green-800 dark:text-green-200',
                                    'rejected' => 'bg-pastel-rose/40 text-rose-800 dark:text-rose-200',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusColors[$candidate->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($candidate->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-slate-500 dark:text-slate-400 text-xs">
                            {{ $candidate->applied_at->format('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                            <span class="material-icons-round text-[40px] mb-2 block">person_off</span>
                            Belum ada kandidat
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upcoming Interviews --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-slate-900 dark:text-white">Interview Mendatang</h3>
        </div>
        <div class="p-4 space-y-3">
            @forelse($upcomingInterviews as $interview)
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $interview->candidate->name }}</p>
                        <p class="text-xs text-slate-500">{{ $interview->candidate->jobPosition->title ?? '' }}</p>
                    </div>
                    @php
                        $typeColors = [
                            'phone' => 'bg-sky/40 text-sky-800',
                            'video' => 'bg-lavender/40 text-purple-800',
                            'onsite' => 'bg-primary/40 text-green-800',
                            'technical' => 'bg-peach/40 text-orange-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $typeColors[$interview->type] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($interview->type) }}
                    </span>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1">
                        <span class="material-icons-round text-[14px]">schedule</span>
                        {{ $interview->scheduled_at->format('d M, H:i') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-icons-round text-[14px]">person</span>
                        {{ $interview->interviewer->name }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-slate-400">
                <span class="material-icons-round text-[40px] mb-2 block">event_busy</span>
                <p class="text-sm">Tidak ada interview minggu ini</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Pipeline Summary --}}
<div class="mt-8 bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <h3 class="font-bold text-slate-900 dark:text-white mb-4">Ringkasan Pipeline</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4">
        @php
            $stages = [
                'applied' => ['label' => 'Applied', 'color' => 'bg-neutral-stone/60 text-slate-700', 'icon' => 'inbox'],
                'screening' => ['label' => 'Screening', 'color' => 'bg-sky/40 text-sky-800', 'icon' => 'search'],
                'interview' => ['label' => 'Interview', 'color' => 'bg-lavender/40 text-purple-800', 'icon' => 'mic'],
                'assessment' => ['label' => 'Assessment', 'color' => 'bg-peach/40 text-orange-800', 'icon' => 'quiz'],
                'offered' => ['label' => 'Offered', 'color' => 'bg-green-100 text-green-800', 'icon' => 'local_offer'],
                'hired' => ['label' => 'Hired', 'color' => 'bg-primary/40 text-green-800', 'icon' => 'how_to_reg'],
                'rejected' => ['label' => 'Rejected', 'color' => 'bg-pastel-rose/40 text-rose-800', 'icon' => 'block'],
            ];
        @endphp
        @foreach($stages as $key => $stage)
        <div class="text-center p-4 rounded-xl {{ $stage['color'] }}">
            <span class="material-icons-round text-[24px] mb-1 block">{{ $stage['icon'] }}</span>
            <p class="text-2xl font-bold">{{ $pipelineSummary[$key] ?? 0 }}</p>
            <p class="text-xs font-medium mt-1">{{ $stage['label'] }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection
