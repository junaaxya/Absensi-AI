@extends('layouts.admin')

@section('header-title', 'Template Gaji')
@section('header-subtitle', 'Pilih template penggajian yang sesuai dengan industri Anda')

@section('content')

<div x-data="{ selectedTemplate: null }" x-cloak>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Template Gaji</h1>
            <p class="text-sm text-slate-500 mt-1">Pilih template penggajian yang sesuai dengan industri Anda</p>
        </div>
        <a href="{{ route('admin.salary-components.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
            <span class="material-icons-round text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if($hasExistingComponents)
    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl">
        <div class="flex items-start gap-3">
            <span class="material-icons-round text-amber-600 dark:text-amber-400 text-xl mt-0.5">info</span>
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Komponen gaji sudah ada</p>
                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Anda sudah memiliki komponen gaji. Saat menerapkan template, Anda bisa memilih mode <strong>Merge</strong> (tambah yang belum ada) atau <strong>Replace</strong> (ganti semua).</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($templates as $template)
        <div class="group relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 hover:shadow-lg hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 hover:border-sage/50 transition-all duration-300">
            @if(in_array($template->slug, $recommendedSlugs))
            <div class="absolute -top-2.5 right-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-sage/80 text-emerald-900 text-[11px] font-bold rounded-full uppercase tracking-wide">
                    <span class="material-icons-round text-[12px]">star</span>
                    Rekomendasi
                </span>
            </div>
            @endif

            <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-lavender/30 dark:bg-lavender/10 flex items-center justify-center shrink-0">
                    <span class="material-icons-round text-purple-600 dark:text-purple-400 text-2xl">{{ $template->icon ?? 'description' }}</span>
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-tight">{{ $template->name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $template->component_count }} komponen</p>
                </div>
            </div>

            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 line-clamp-2">{{ $template->description }}</p>

            <div class="flex flex-wrap gap-1.5 mb-5">
                @if($template->includes_bpjs)
                <span class="inline-flex items-center px-2 py-0.5 bg-sky/30 dark:bg-sky/10 text-blue-700 dark:text-blue-400 text-[11px] font-semibold rounded-lg">BPJS</span>
                @endif
                @if($template->includes_pph21)
                <span class="inline-flex items-center px-2 py-0.5 bg-peach/40 dark:bg-peach/10 text-orange-700 dark:text-orange-400 text-[11px] font-semibold rounded-lg">PPh 21</span>
                @endif
                @if($template->includes_overtime)
                <span class="inline-flex items-center px-2 py-0.5 bg-sage/30 dark:bg-sage/10 text-emerald-700 dark:text-emerald-400 text-[11px] font-semibold rounded-lg">Lembur</span>
                @endif
                @if($template->industry_type)
                <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 text-[11px] font-semibold rounded-lg capitalize">{{ $template->industry_type }}</span>
                @endif
            </div>

            <a href="{{ route('admin.payroll-templates.preview', $template) }}"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-md shadow-slate-900/10 active:scale-[0.98]">
                <span class="material-icons-round text-[18px]">visibility</span>
                Pilih Template
            </a>
        </div>
        @endforeach

        <div class="group relative bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 p-6 hover:border-slate-400 dark:hover:border-slate-500 transition-all duration-300 flex flex-col items-center justify-center text-center min-h-[280px]">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                <span class="material-icons-round text-slate-400 dark:text-slate-500 text-2xl">add_circle_outline</span>
            </div>
            <h3 class="font-bold text-slate-700 dark:text-slate-300 text-lg mb-2">Mulai dari Kosong</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">Buat komponen gaji secara manual satu per satu</p>
            <a href="{{ route('admin.salary-components.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-semibold text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                <span class="material-icons-round text-[18px]">edit</span>
                Buat Manual
            </a>
        </div>
    </div>

</div>

@endsection
