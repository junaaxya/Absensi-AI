@extends('layouts.admin')

@section('header-title', $course->title)
@section('header-subtitle', 'Detail Kursus')

@section('content')
<div x-data="{ showMaterialModal: false, showEnrollModal: false, editMaterial: null, showDeleteMaterialModal: false, deleteMaterialId: null }">

    <a href="{{ route('admin.training.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 mb-6 transition-colors">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>

    {{-- Course Info Card --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
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
                    @if($course->is_mandatory)
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-peach/50 text-orange-700 dark:text-orange-300">WAJIB</span>
                    @endif
                    @if($course->is_published)
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-primary/30 text-green-700 dark:text-green-300">Published</span>
                    @else
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">Draft</span>
                    @endif
                </div>

                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $course->title }}</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ $course->description }}</p>

                <div class="flex flex-wrap gap-4 text-sm text-slate-500">
                    @if($course->instructor)
                        <span>Instruktur: <strong class="text-slate-700 dark:text-slate-300">{{ $course->instructor->name }}</strong></span>
                    @endif
                    @if($course->category)
                        <span>Kategori: <strong class="text-slate-700 dark:text-slate-300">{{ $course->category }}</strong></span>
                    @endif
                    @if($course->duration_hours)
                        <span>Durasi: <strong class="text-slate-700 dark:text-slate-300">{{ $course->duration_hours }} jam</strong></span>
                    @endif
                    @if($course->max_participants)
                        <span>Maks: <strong class="text-slate-700 dark:text-slate-300">{{ $course->max_participants }} peserta</strong></span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-2 shrink-0">
                <a href="{{ route('admin.training.edit', $course) }}"
                    class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm text-center hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Edit Kursus
                </a>
                @if($course->is_published)
                    <form method="POST" action="/admin/training/{{ $course->slug }}/unpublish">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 rounded-xl bg-peach/30 text-orange-700 dark:text-orange-300 font-bold text-sm hover:bg-peach/50 transition-colors">
                            Unpublish
                        </button>
                    </form>
                @else
                    <form method="POST" action="/admin/training/{{ $course->slug }}/publish">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 rounded-xl bg-primary/30 text-green-700 dark:text-green-300 font-bold text-sm hover:bg-primary/50 transition-colors">
                            Publish
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Materials Section --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 dark:text-white">Materi Kursus ({{ $course->materials->count() }})</h3>
                    <button @click="editMaterial = null; showMaterialModal = true"
                        class="px-3 py-1.5 rounded-lg bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs hover:opacity-90 transition-all flex items-center gap-1">
                        <span class="material-icons-round text-sm">add</span>
                        Tambah
                    </button>
                </div>

                @forelse($course->materials as $material)
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800/50 flex items-center gap-4 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-sm font-bold text-slate-500 shrink-0">
                            {{ $material->sort_order }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $material->title }}</p>
                            <div class="flex gap-3 mt-1">
                                @php
                                    $typeIcons = ['document' => 'description', 'video' => 'play_circle', 'link' => 'link', 'quiz' => 'quiz'];
                                @endphp
                                <span class="text-xs text-slate-500 flex items-center gap-1">
                                    <span class="material-icons-round text-sm">{{ $typeIcons[$material->type] ?? 'article' }}</span>
                                    {{ ucfirst($material->type) }}
                                </span>
                                @if($material->duration_minutes)
                                    <span class="text-xs text-slate-500">{{ $material->duration_minutes }} menit</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <form method="POST" action="/admin/training/materials/{{ $material->id }}" class="inline"
                                onsubmit="return confirm('Hapus materi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-slate-400">
                        <span class="material-icons-round text-3xl mb-2 block">library_books</span>
                        <p class="text-sm">Belum ada materi. Tambahkan materi pertama.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Enrolled Students --}}
        <div>
            <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 dark:text-white">Peserta ({{ $enrollments->count() }})</h3>
                    <button @click="showEnrollModal = true"
                        class="px-3 py-1.5 rounded-lg bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs hover:opacity-90 transition-all flex items-center gap-1">
                        <span class="material-icons-round text-sm">person_add</span>
                        Daftarkan
                    </button>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    @forelse($enrollments as $enrollment)
                        <div class="px-6 py-3 border-b border-slate-50 dark:border-slate-800/50 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300 shrink-0">
                                {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $enrollment->user->name }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @php
                                        $statusColors = [
                                            'enrolled' => 'text-sky-600',
                                            'in_progress' => 'text-orange-600',
                                            'completed' => 'text-green-600',
                                            'dropped' => 'text-slate-400',
                                        ];
                                    @endphp
                                    <span class="text-xs font-bold {{ $statusColors[$enrollment->status] ?? '' }}">{{ $enrollment->progress_percentage }}%</span>
                                    <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-slate-400">
                            <span class="material-icons-round text-2xl mb-1 block">people</span>
                            <p class="text-xs">Belum ada peserta</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Add Material Modal --}}
    <div x-show="showMaterialModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm"
        x-transition>
        <div @click.away="showMaterialModal = false"
            class="bg-white dark:bg-card-dark rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-8 max-w-lg w-full mx-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Tambah Materi</h3>
            <form method="POST" action="/admin/training/{{ $course->slug }}/materials" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Judul</label>
                        <input type="text" name="title" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe</label>
                        <select name="type" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white">
                            <option value="document">Document</option>
                            <option value="video">Video</option>
                            <option value="link">Link</option>
                            <option value="quiz">Quiz</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Konten (URL atau teks)</label>
                        <textarea name="content" rows="3"
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">File (opsional)</label>
                        <input type="file" name="file"
                            class="w-full px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Durasi (menit)</label>
                        <input type="number" name="duration_minutes" min="0"
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showMaterialModal = false"
                        class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Enroll Employee Modal --}}
    <div x-show="showEnrollModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm"
        x-transition>
        <div @click.away="showEnrollModal = false"
            class="bg-white dark:bg-card-dark rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-8 max-w-lg w-full mx-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Daftarkan Karyawan</h3>
            <form method="POST" action="/admin/training/{{ $course->slug }}/enroll">
                @csrf
                <div class="space-y-4">
                    <p class="text-sm text-slate-500">Pilih karyawan yang akan didaftarkan ke kursus ini.</p>
                    <div class="max-h-64 overflow-y-auto border border-slate-200 dark:border-slate-700 rounded-xl p-3 space-y-2">
                        @php
                            $allUsers = \App\Models\User::orderBy('name')->get();
                            $enrolledUserIds = $enrollments->pluck('user_id')->toArray();
                        @endphp
                        @foreach($allUsers as $user)
                            @if(!in_array($user->id, $enrolledUserIds))
                                <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                        class="rounded border-slate-300 text-primary focus:ring-primary">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $user->name }}</span>
                                    @if($user->department)
                                        <span class="text-xs text-slate-400 ml-auto">{{ $user->department->name }}</span>
                                    @endif
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showEnrollModal = false"
                        class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm">
                        Daftarkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
