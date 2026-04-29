@extends('layouts.admin')

@section('header-title', 'Buat Kursus Baru')
@section('header-subtitle', 'Tambahkan kursus pelatihan baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('admin.training.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 mb-6 transition-colors">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.training.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Kursus <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-3 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary text-sm dark:text-white"
                        placeholder="Masukkan judul kursus">
                    @error('title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="4" required
                        class="w-full px-4 py-3 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary text-sm dark:text-white"
                        placeholder="Deskripsi kursus">{{ old('description') }}</textarea>
                    @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Category --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori</label>
                        <input type="text" name="category" value="{{ old('category') }}"
                            class="w-full px-4 py-3 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary text-sm dark:text-white"
                            placeholder="Contoh: IT, Soft Skills, dll">
                    </div>

                    {{-- Difficulty --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tingkat Kesulitan <span class="text-rose-500">*</span></label>
                        <select name="difficulty" required
                            class="w-full px-4 py-3 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary text-sm dark:text-white">
                            <option value="beginner" {{ old('difficulty') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('difficulty') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('difficulty') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Instructor --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Instruktur</label>
                        <select name="instructor_id"
                            class="w-full px-4 py-3 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary text-sm dark:text-white">
                            <option value="">-- Pilih Instruktur --</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Duration --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Durasi (Jam)</label>
                        <input type="number" name="duration_hours" value="{{ old('duration_hours') }}" step="0.5" min="0"
                            class="w-full px-4 py-3 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary text-sm dark:text-white"
                            placeholder="Contoh: 8.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Max Participants --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Maks. Peserta</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants') }}" min="1"
                            class="w-full px-4 py-3 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary text-sm dark:text-white"
                            placeholder="Kosongkan jika tidak terbatas">
                    </div>

                    {{-- Thumbnail --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Thumbnail</label>
                        <input type="file" name="thumbnail" accept="image/*"
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-primary/20 file:text-slate-700">
                    </div>
                </div>

                {{-- Target Roles --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Target Role</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-800 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <input type="checkbox" name="target_roles[]" value="{{ $role }}"
                                    {{ in_array($role, old('target_roles', [])) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-primary focus:ring-primary">
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $role }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Is Mandatory --}}
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_mandatory" value="1" {{ old('is_mandatory') ? 'checked' : '' }}
                            class="rounded border-slate-300 text-primary focus:ring-primary w-5 h-5">
                        <div>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Kursus Wajib</span>
                            <p class="text-xs text-slate-500">Tandai jika kursus ini wajib diikuti oleh karyawan</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.training.index') }}"
                    class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-slate-900/20">
                    Simpan Kursus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
