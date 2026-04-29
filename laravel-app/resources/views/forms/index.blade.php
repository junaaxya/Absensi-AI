@extends('layouts.absensi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Form Internal</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Pilih form yang ingin Anda isi.</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('forms.submissions') }}"
            class="px-4 py-2 bg-sky/30 hover:bg-sky/50 text-slate-700 dark:text-slate-200 font-medium rounded-xl transition text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Submission Saya
        </a>
    </div>

    @if($templates->isEmpty())
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada form yang tersedia.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $template)
                <a href="{{ route('forms.show', $template) }}"
                    class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md hover:border-primary/50 transition-all group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-lavender/30 dark:bg-lavender/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        @if($template->requires_approval)
                            <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-peach/30 text-orange-700 dark:text-orange-300 rounded-lg">Perlu Approval</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition">{{ $template->name }}</h3>
                    @if($template->description)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ $template->description }}</p>
                    @endif
                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-400">
                        <span>{{ count($template->fields ?? []) }} field</span>
                        <span>&middot;</span>
                        <span>{{ $template->submissions_count }} submission</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
