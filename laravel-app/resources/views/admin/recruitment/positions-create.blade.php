@extends('layouts.admin')

@section('header-title', isset($jobPosition) ? 'Edit Posisi' : 'Tambah Posisi')
@section('header-subtitle', isset($jobPosition) ? 'Perbarui detail posisi pekerjaan' : 'Buat posisi pekerjaan baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.recruitment.positions') }}"
        class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition-colors">
        <span class="material-icons-round text-[16px]">arrow_back</span>
        Kembali ke Daftar Posisi
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                {{ isset($jobPosition) ? 'Edit Posisi Pekerjaan' : 'Posisi Pekerjaan Baru' }}
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Isi detail posisi yang akan dibuka</p>
        </div>

        <form method="POST"
            action="{{ isset($jobPosition) ? route('admin.recruitment.positions.update', $jobPosition) : route('admin.recruitment.positions.store') }}"
            class="p-6 space-y-5">
            @csrf
            @if(isset($jobPosition))
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Posisi *</label>
                <input type="text" name="title" required
                    value="{{ old('title', $jobPosition->title ?? '') }}"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                    placeholder="contoh: Software Engineer">
                @error('title')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Departemen</label>
                <select name="department_id"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
                    <option value="">Tidak ada departemen</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id', $jobPosition->department_id ?? '') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                <textarea name="description" rows="4"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                    placeholder="Deskripsi pekerjaan...">{{ old('description', $jobPosition->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Persyaratan</label>
                <textarea name="requirements" rows="4"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                    placeholder="Persyaratan kandidat...">{{ old('requirements', $jobPosition->requirements ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Pekerjaan *</label>
                    <select name="employment_type" required
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
                        <option value="full_time" {{ old('employment_type', $jobPosition->employment_type ?? 'full_time') === 'full_time' ? 'selected' : '' }}>Full Time</option>
                        <option value="part_time" {{ old('employment_type', $jobPosition->employment_type ?? '') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                        <option value="contract" {{ old('employment_type', $jobPosition->employment_type ?? '') === 'contract' ? 'selected' : '' }}>Kontrak</option>
                        <option value="internship" {{ old('employment_type', $jobPosition->employment_type ?? '') === 'internship' ? 'selected' : '' }}>Magang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Status *</label>
                    <select name="status" required
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
                        <option value="draft" {{ old('status', $jobPosition->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="open" {{ old('status', $jobPosition->status ?? '') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ old('status', $jobPosition->status ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="on_hold" {{ old('status', $jobPosition->status ?? '') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Jumlah Lowongan *</label>
                <input type="number" name="openings" min="1" required
                    value="{{ old('openings', $jobPosition->openings ?? 1) }}"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Gaji Minimum (Rp)</label>
                    <input type="number" name="salary_range_min" min="0" step="100000"
                        value="{{ old('salary_range_min', $jobPosition->salary_range_min ?? '') }}"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Gaji Maksimum (Rp)</label>
                    <input type="number" name="salary_range_max" min="0" step="100000"
                        value="{{ old('salary_range_max', $jobPosition->salary_range_max ?? '') }}"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                        placeholder="0">
                </div>
            </div>

            @if($errors->any())
            <div class="bg-pastel-rose/30 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.recruitment.positions') }}"
                    class="px-4 py-2 text-sm font-bold text-slate-600 hover:text-slate-800 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                    <span class="material-icons-round text-[16px]">{{ isset($jobPosition) ? 'save' : 'add' }}</span>
                    {{ isset($jobPosition) ? 'Simpan Perubahan' : 'Tambah Posisi' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
