@extends('layouts.admin')

@section('header-title', 'Rekrutmen')
@section('header-subtitle', 'Kelola lowongan, kandidat, dan proses rekrutmen')

@section('content')

<div x-data="{ activeTab: '{{ $tab }}' }" class="space-y-6">

    {{-- Header: Title + Primary Actions --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Rekrutmen</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola lowongan, kandidat, dan proses seleksi</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.recruitment.positions.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg">
                <span class="material-icons-round text-[18px]">add</span>
                Buka Lowongan Baru
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-sky/30 flex items-center justify-center">
                    <span class="material-icons-round text-sky-700 dark:text-sky-300 text-[20px]">work</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $openPositions }}</p>
                    <p class="text-xs text-slate-500">Posisi Terbuka</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-lavender/30 flex items-center justify-center">
                    <span class="material-icons-round text-purple-700 dark:text-purple-300 text-[20px]">people</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $activeCandidates }}</p>
                    <p class="text-xs text-slate-500">Kandidat Aktif</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-peach/30 flex items-center justify-center">
                    <span class="material-icons-round text-orange-700 dark:text-orange-300 text-[20px]">event</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $upcomingInterviews->count() }}</p>
                    <p class="text-xs text-slate-500">Interview Minggu Ini</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-sage/30 flex items-center justify-center">
                    <span class="material-icons-round text-emerald-700 dark:text-emerald-300 text-[20px]">how_to_reg</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $hiredThisMonth }}</p>
                    <p class="text-xs text-slate-500">Diterima Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex items-center gap-1 bg-white dark:bg-gray-800 rounded-2xl p-1.5 shadow-sm border border-slate-200 dark:border-slate-700 w-fit">
        <button @click="activeTab = 'dashboard'"
            :class="activeTab === 'dashboard' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            <span class="material-icons-round text-[18px]">dashboard</span>
            Overview
        </button>
        <button @click="activeTab = 'positions'"
            :class="activeTab === 'positions' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            <span class="material-icons-round text-[18px]">work</span>
            Posisi & Lowongan
            <span class="min-w-[20px] h-5 px-1.5 flex items-center justify-center rounded-full text-[10px] font-bold"
                :class="activeTab === 'positions' ? 'bg-white/20 text-white dark:bg-slate-900/20 dark:text-slate-900' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400'">
                {{ $positions->total() }}
            </span>
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-sage/20 border border-sage/40 rounded-xl px-4 py-3 flex items-center gap-2">
            <span class="material-icons-round text-emerald-600 text-[18px]">check_circle</span>
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB 1: DASHBOARD OVERVIEW --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'dashboard'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Pipeline Summary --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 mb-6">
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Ringkasan Pipeline</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                @php
                    $stages = [
                        'applied' => ['label' => 'Applied', 'color' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300', 'icon' => 'inbox'],
                        'screening' => ['label' => 'Screening', 'color' => 'bg-sky/20 text-sky-800 dark:text-sky-200', 'icon' => 'search'],
                        'interview' => ['label' => 'Interview', 'color' => 'bg-lavender/20 text-purple-800 dark:text-purple-200', 'icon' => 'mic'],
                        'assessment' => ['label' => 'Assessment', 'color' => 'bg-peach/20 text-orange-800 dark:text-orange-200', 'icon' => 'quiz'],
                        'offered' => ['label' => 'Offered', 'color' => 'bg-amber-100 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200', 'icon' => 'local_offer'],
                        'hired' => ['label' => 'Hired', 'color' => 'bg-sage/20 text-emerald-800 dark:text-emerald-200', 'icon' => 'how_to_reg'],
                        'rejected' => ['label' => 'Rejected', 'color' => 'bg-rose-100 dark:bg-rose-900/20 text-rose-800 dark:text-rose-200', 'icon' => 'block'],
                    ];
                @endphp
                @foreach($stages as $key => $stage)
                <div class="text-center p-3 rounded-xl {{ $stage['color'] }}">
                    <span class="material-icons-round text-[20px] mb-1 block">{{ $stage['icon'] }}</span>
                    <p class="text-xl font-bold">{{ $pipelineSummary[$key] ?? 0 }}</p>
                    <p class="text-[10px] font-medium mt-0.5">{{ $stage['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent Candidates --}}
            <div class="lg:col-span-2 bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 dark:text-white">Kandidat Terbaru</h3>
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
                                            'applied' => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300',
                                            'screening' => 'bg-sky/30 text-sky-800 dark:text-sky-200',
                                            'interview' => 'bg-lavender/30 text-purple-800 dark:text-purple-200',
                                            'assessment' => 'bg-peach/30 text-orange-800 dark:text-orange-200',
                                            'offered' => 'bg-amber-100 text-amber-800 dark:text-amber-200',
                                            'hired' => 'bg-sage/30 text-emerald-800 dark:text-emerald-200',
                                            'rejected' => 'bg-rose-100 text-rose-800 dark:text-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusColors[$candidate->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($candidate->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-slate-500 dark:text-slate-400 text-xs">
                                    {{ $candidate->applied_at?->format('d M Y') ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                    <span class="material-icons-round text-[40px] mb-2 block">person_off</span>
                                    <p class="text-sm mb-3">Belum ada kandidat</p>
                                    <a href="{{ route('admin.recruitment.positions.create') }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-xs font-bold hover:opacity-90 transition-all">
                                        <span class="material-icons-round text-[14px]">add</span>
                                        Buka Lowongan Pertama
                                    </a>
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
                                    'phone' => 'bg-sky/30 text-sky-800',
                                    'video' => 'bg-lavender/30 text-purple-800',
                                    'onsite' => 'bg-sage/30 text-emerald-800',
                                    'technical' => 'bg-peach/30 text-orange-800',
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
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB 2: POSISI & LOWONGAN --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'positions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Filters --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('admin.recruitment.index') }}" class="flex flex-wrap items-end gap-4">
                <input type="hidden" name="tab" value="positions">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Status</label>
                    <select name="status"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-sage focus:border-sage dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Departemen</label>
                    <select name="department_id"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-sage focus:border-sage dark:text-white">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                    <span class="material-icons-round text-[16px]">filter_list</span>
                    Filter
                </button>
            </form>
        </div>

        {{-- Position Cards Grid --}}
        @if($positions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($positions as $position)
            <a href="{{ route('admin.recruitment.positions.show', $position) }}"
                class="block bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-soft transition-all group">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-sky-600 transition-colors leading-tight">
                            {{ $position->title }}
                        </h3>
                        @php
                            $posStatusColors = [
                                'draft' => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                                'open' => 'bg-sage/30 text-emerald-800 dark:text-emerald-200',
                                'closed' => 'bg-rose-100 dark:bg-rose-900/20 text-rose-800 dark:text-rose-200',
                                'on_hold' => 'bg-amber-100 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 {{ $posStatusColors[$position->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst(str_replace('_', ' ', $position->status)) }}
                        </span>
                    </div>

                    <div class="space-y-2 text-xs text-slate-500 dark:text-slate-400">
                        @if($position->department)
                        <div class="flex items-center gap-1.5">
                            <span class="material-icons-round text-[14px]">business</span>
                            {{ $position->department->name }}
                        </div>
                        @endif
                        <div class="flex items-center gap-1.5">
                            <span class="material-icons-round text-[14px]">badge</span>
                            {{ ucfirst(str_replace('_', ' ', $position->employment_type)) }}
                        </div>
                        @if($position->salary_range_min || $position->salary_range_max)
                        <div class="flex items-center gap-1.5">
                            <span class="material-icons-round text-[14px]">payments</span>
                            @if($position->salary_range_min && $position->salary_range_max)
                                Rp {{ number_format($position->salary_range_min, 0, ',', '.') }} - {{ number_format($position->salary_range_max, 0, ',', '.') }}
                            @elseif($position->salary_range_min)
                                Mulai Rp {{ number_format($position->salary_range_min, 0, ',', '.') }}
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="material-icons-round text-[14px]">people</span>
                            {{ $position->candidates_count }} kandidat
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="material-icons-round text-[14px]">event_seat</span>
                            {{ $position->openings }} lowongan
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $positions->links() }}
        </div>
        @else
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky/20 flex items-center justify-center mx-auto mb-4">
                <span class="material-icons-round text-sky-600 text-[32px]">work_outline</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Belum Ada Posisi</h3>
            <p class="text-sm text-slate-500 mb-6 max-w-md mx-auto">
                Mulai proses rekrutmen dengan membuka posisi lowongan baru. Kandidat akan bisa ditambahkan setelah posisi dibuat.
            </p>
            <a href="{{ route('admin.recruitment.positions.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg">
                <span class="material-icons-round text-[18px]">add</span>
                Buka Lowongan Pertama
            </a>
        </div>
        @endif
    </div>
</div>

@endsection
