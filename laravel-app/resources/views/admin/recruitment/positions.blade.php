@extends('layouts.admin')

@section('header-title', 'Posisi Pekerjaan')
@section('header-subtitle', 'Kelola lowongan dan posisi')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Posisi</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $positions->total() }} posisi ditemukan</p>
    </div>
    <a href="{{ route('admin.recruitment.positions.create') }}"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
        <span class="material-icons-round text-[18px]">add</span>
        Tambah Posisi
    </a>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.recruitment.positions') }}" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[150px]">
            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Status</label>
            <select name="status"
                class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
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
                class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
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
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($positions as $position)
    <a href="{{ route('admin.recruitment.positions.show', $position) }}"
        class="block bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-soft transition-all group">
        <div class="p-6">
            <div class="flex items-start justify-between mb-3">
                <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-sky-600 transition-colors text-lg leading-tight">
                    {{ $position->title }}
                </h3>
                @php
                    $statusColors = [
                        'draft' => 'bg-neutral-stone/60 text-slate-700 dark:text-slate-300',
                        'open' => 'bg-primary/40 text-green-800 dark:text-green-200',
                        'closed' => 'bg-pastel-rose/40 text-rose-800 dark:text-rose-200',
                        'on_hold' => 'bg-peach/40 text-orange-800 dark:text-orange-200',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $statusColors[$position->status] ?? 'bg-gray-100 text-gray-600' }} whitespace-nowrap">
                    {{ ucfirst(str_replace('_', ' ', $position->status)) }}
                </span>
            </div>

            <div class="space-y-2 mb-4">
                @if($position->department)
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <span class="material-icons-round text-[16px]">business</span>
                    {{ $position->department->name }}
                </div>
                @endif
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    @php
                        $typeLabels = [
                            'full_time' => 'Full Time',
                            'part_time' => 'Part Time',
                            'contract' => 'Kontrak',
                            'internship' => 'Magang',
                        ];
                        $typeColors = [
                            'full_time' => 'bg-sky/30 text-sky-800',
                            'part_time' => 'bg-lavender/30 text-purple-800',
                            'contract' => 'bg-peach/30 text-orange-800',
                            'internship' => 'bg-primary/30 text-green-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $typeColors[$position->employment_type] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $typeLabels[$position->employment_type] ?? $position->employment_type }}
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-1 text-sm text-slate-500">
                    <span class="material-icons-round text-[16px]">group</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $position->candidates_count }}</span>
                    <span>kandidat</span>
                </div>
                <div class="flex items-center gap-1 text-sm text-slate-500">
                    <span class="material-icons-round text-[16px]">person_add</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $position->openings }}</span>
                    <span>lowongan</span>
                </div>
            </div>
        </div>
    </a>
    @empty
    <div class="col-span-full text-center py-16">
        <span class="material-icons-round text-[60px] text-slate-300 dark:text-slate-600 mb-4 block">work_off</span>
        <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada posisi pekerjaan</p>
        <a href="{{ route('admin.recruitment.positions.create') }}"
            class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all">
            <span class="material-icons-round text-[18px]">add</span>
            Tambah Posisi Pertama
        </a>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $positions->withQueryString()->links() }}
</div>
@endsection
