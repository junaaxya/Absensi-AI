@extends('layouts.admin')

@section('header-title', 'Training Management')
@section('header-subtitle', 'Kelola kursus dan pelatihan karyawan')

@section('content')
<div x-data="{ showDeleteModal: false, deleteData: {} }">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-2xl bg-sky/30 flex items-center justify-center">
                    <span class="material-icons-round text-sky-700 dark:text-sky-300 text-[24px]">school</span>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $courses->total() }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Kursus</p>
        </div>

        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-2xl bg-primary/30 flex items-center justify-center">
                    <span class="material-icons-round text-green-700 dark:text-green-300 text-[24px]">check_circle</span>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $courses->where('is_published', true)->count() }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Dipublikasikan</p>
        </div>

        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-2xl bg-lavender/30 flex items-center justify-center">
                    <span class="material-icons-round text-purple-700 dark:text-purple-300 text-[24px]">people</span>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $courses->sum('enrollments_count') }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Pendaftar</p>
        </div>

        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-2xl bg-peach/30 flex items-center justify-center">
                    <span class="material-icons-round text-orange-700 dark:text-orange-300 text-[24px]">emoji_events</span>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $courses->sum('completed_count') }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Selesai</p>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <form method="GET" action="{{ route('admin.training.index') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <div class="relative">
                <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kursus..."
                    class="w-full sm:w-64 pl-10 pr-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm" />
            </div>
            <select name="status" onchange="this.form.submit()"
                class="px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
        </form>
        <div class="flex gap-3">
            <a href="{{ route('admin.training.report') }}"
                class="bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                <span class="material-icons-round text-lg">assessment</span>
                Laporan
            </a>
            <a href="{{ route('admin.training.create') }}"
                class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95 shrink-0">
                <span class="material-icons-round text-lg">add</span>
                Buat Kursus
            </a>
        </div>
    </div>

    {{-- Course List --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <th class="px-6 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Kursus</th>
                        <th class="px-6 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Difficulty</th>
                        <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Materi</th>
                        <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Peserta</th>
                        <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Selesai</th>
                        <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($courses as $course)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.training.show', $course) }}" class="font-bold text-slate-900 dark:text-white hover:text-sky-600 transition-colors">
                                {{ $course->title }}
                            </a>
                            @if($course->is_mandatory)
                                <span class="ml-2 inline-block px-2 py-0.5 bg-peach/50 text-orange-700 dark:text-orange-300 rounded-md text-[10px] font-bold">WAJIB</span>
                            @endif
                            @if($course->category)
                                <p class="text-xs text-slate-500 mt-1">{{ $course->category }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $diffClass = [
                                    'beginner' => 'bg-primary/30 text-green-700 dark:text-green-300',
                                    'intermediate' => 'bg-sky/30 text-sky-700 dark:text-sky-300',
                                    'advanced' => 'bg-lavender/30 text-purple-700 dark:text-purple-300',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $diffClass[$course->difficulty] ?? '' }}">
                                {{ ucfirst($course->difficulty) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400">{{ $course->materials_count }}</td>
                        <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400">{{ $course->enrollments_count }}</td>
                        <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400">{{ $course->completed_count }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($course->is_published)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-primary/30 text-green-700 dark:text-green-300">Published</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.training.show', $course) }}"
                                    class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    <span class="material-icons-round text-lg">visibility</span>
                                </a>
                                <a href="{{ route('admin.training.edit', $course) }}"
                                    class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    <span class="material-icons-round text-lg">edit</span>
                                </a>
                                <button @click="deleteData = { slug: '{{ $course->slug }}', title: '{{ addslashes($course->title) }}' }; showDeleteModal = true"
                                    class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-icons-round text-4xl mb-2 block">school</span>
                            Belum ada kursus. Buat kursus pertama Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>

    {{-- Delete Modal --}}
    <div x-show="showDeleteModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showDeleteModal = false"
            class="bg-white dark:bg-card-dark rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center mx-auto mb-4">
                    <span class="material-icons-round text-rose-500 text-3xl">warning</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Hapus Kursus?</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    Kursus <strong x-text="deleteData.title"></strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex gap-3 justify-center">
                    <button @click="showDeleteModal = false"
                        class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    <form :action="'/admin/training/' + deleteData.slug" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-rose-500 text-white font-bold text-sm hover:bg-rose-600 transition-colors shadow-lg shadow-rose-500/20">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
