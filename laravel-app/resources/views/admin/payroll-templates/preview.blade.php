@extends('layouts.admin')

@section('header-title', 'Preview Template')
@section('header-subtitle', $template->name)

@section('content')

<div x-data="templatePreview()" x-cloak>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <nav class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('admin.payroll-templates.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Template Gaji</a>
                <span class="material-icons-round text-[14px]">chevron_right</span>
                <span class="text-slate-900 dark:text-white font-medium">{{ $template->name }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $template->name }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $template->description }}</p>
        </div>
    </div>

    <form action="{{ route('admin.payroll-templates.apply', $template) }}" method="POST">
        @csrf

        @if($hasExistingComponents)
        <div class="mb-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="material-icons-round text-[20px] text-lavender">swap_horiz</span>
                Mode Penerapan
            </h3>
            <p class="text-sm text-slate-500 mb-4">Anda sudah memiliki {{ $previewData['existing_count'] }} komponen gaji. Pilih cara menerapkan template:</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="relative cursor-pointer">
                    <input type="radio" name="mode" value="merge" x-model="mode" class="peer sr-only" {{ $defaultMode === 'merge' ? 'checked' : '' }}>
                    <div class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-sage peer-checked:bg-sage/5 transition-all">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="material-icons-round text-[18px] text-sage">merge_type</span>
                            <span class="font-bold text-sm text-slate-900 dark:text-white">Merge</span>
                        </div>
                        <p class="text-xs text-slate-500">Tambahkan komponen yang belum ada saja</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="mode" value="replace" x-model="mode" class="peer sr-only">
                    <div class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-rose-400 peer-checked:bg-rose-50/50 dark:peer-checked:bg-rose-900/10 transition-all">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="material-icons-round text-[18px] text-rose-500">sync</span>
                            <span class="font-bold text-sm text-slate-900 dark:text-white">Replace</span>
                        </div>
                        <p class="text-xs text-slate-500">Hapus semua & ganti dengan template</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="mode" value="fresh" x-model="mode" class="peer sr-only">
                    <div class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-sky peer-checked:bg-sky/5 transition-all">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="material-icons-round text-[18px] text-sky">add_circle</span>
                            <span class="font-bold text-sm text-slate-900 dark:text-white">Fresh</span>
                        </div>
                        <p class="text-xs text-slate-500">Tambahkan semua (mungkin duplikat)</p>
                    </div>
                </label>
            </div>
        </div>
        @else
        <input type="hidden" name="mode" value="fresh">
        @endif

        <div class="space-y-5 mb-8">
            @foreach($groupedItems as $groupLabel => $items)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        @php
                            $groupIcon = match(true) {
                                str_contains($groupLabel, 'BPJS Karyawan') => 'health_and_safety',
                                str_contains($groupLabel, 'BPJS Perusahaan') => 'corporate_fare',
                                str_contains($groupLabel, 'Pajak') => 'receipt_long',
                                str_contains($groupLabel, 'Potongan') => 'remove_circle_outline',
                                str_contains($groupLabel, 'Variabel') => 'show_chart',
                                default => 'payments',
                            };
                        @endphp
                        <span class="material-icons-round text-[20px] text-slate-400">{{ $groupIcon }}</span>
                        {{ $groupLabel }}
                        <span class="text-xs font-normal text-slate-500 ml-1">({{ $items->count() }} item)</span>
                    </h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($items as $item)
                    <div class="px-5 py-3.5 flex items-center gap-4 hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="shrink-0">
                            @if($item->is_required)
                            <div class="w-5 h-5 rounded bg-slate-200 dark:bg-slate-600 flex items-center justify-center" title="Wajib">
                                <span class="material-icons-round text-[14px] text-slate-500 dark:text-slate-400">lock</span>
                            </div>
                            @elseif($item->is_optional)
                            <input type="checkbox"
                                name="excluded_codes[]"
                                value="{{ $item->code }}"
                                class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-sage focus:ring-sage/50"
                                x-bind:checked="!isExcluded('{{ $item->code }}')"
                                @change="toggleExclude('{{ $item->code }}')">
                            @else
                            <div class="w-5 h-5 rounded bg-sage/20 flex items-center justify-center">
                                <span class="material-icons-round text-[14px] text-emerald-600">check</span>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-sm text-slate-900 dark:text-white">{{ $item->name }}</span>
                                <code class="text-[10px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded font-mono">{{ $item->code }}</code>
                                @if($item->is_required)
                                <span class="text-[10px] px-1.5 py-0.5 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded font-semibold">Wajib</span>
                                @endif
                                @if($item->is_optional)
                                <span class="text-[10px] px-1.5 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded font-semibold">Opsional</span>
                                @endif
                            </div>
                            @if($item->description)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $item->description }}</p>
                            @endif
                        </div>
                        <div class="shrink-0 text-right">
                            @if($item->value_type === 'formula')
                            <code class="text-[11px] text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-2 py-1 rounded-lg font-mono max-w-[200px] truncate block">{{ $item->formula }}</code>
                            @elseif($item->default_amount > 0)
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Rp {{ number_format($item->default_amount, 0, ',', '.') }}</span>
                            @else
                            <span class="text-xs text-slate-400">Flat (diisi manual)</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="sticky bottom-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-t border-slate-200 dark:border-slate-700 -mx-6 px-6 py-4 flex items-center justify-between gap-4 rounded-b-2xl">
            <a href="{{ route('admin.payroll-templates.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <span class="material-icons-round text-[18px]">arrow_back</span>
                Kembali
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-[0.98]">
                <span class="material-icons-round text-[18px]">check_circle</span>
                Terapkan Template
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
function templatePreview() {
    return {
        mode: '{{ $defaultMode }}',
        excludedCodes: [],
        isExcluded(code) {
            return this.excludedCodes.includes(code);
        },
        toggleExclude(code) {
            if (this.isExcluded(code)) {
                this.excludedCodes = this.excludedCodes.filter(c => c !== code);
            } else {
                this.excludedCodes.push(code);
            }
        }
    }
}
</script>
@endpush

@endsection
