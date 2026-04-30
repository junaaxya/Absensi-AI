@extends('layouts.admin')

@section('header-title', 'Komponen Gaji')
@section('header-subtitle', 'Kelola komponen penggajian karyawan')

@section('content')

<div x-data="salaryComponentIndex()" x-cloak>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Komponen Gaji</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola komponen penggajian karyawan</p>
        </div>
        <a href="{{ route('admin.salary-components.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95 shrink-0">
            <span class="material-icons-round text-lg">add</span>
            Tambah Komponen
        </a>
    </div>

    @php
        $typeConfig = [
            'earning' => [
                'label' => 'Pendapatan',
                'icon' => 'trending_up',
                'bgHeader' => 'bg-sage/30 dark:bg-sage/10',
                'borderHeader' => 'border-sage/50',
                'iconBg' => 'bg-sage/50 dark:bg-sage/20',
                'iconColor' => 'text-emerald-700 dark:text-emerald-400',
            ],
            'deduction' => [
                'label' => 'Potongan',
                'icon' => 'trending_down',
                'bgHeader' => 'bg-rose-100/60 dark:bg-rose-900/10',
                'borderHeader' => 'border-rose-200/50',
                'iconBg' => 'bg-rose-100 dark:bg-rose-900/20',
                'iconColor' => 'text-rose-600 dark:text-rose-400',
            ],
            'benefit' => [
                'label' => 'Benefit',
                'icon' => 'health_and_safety',
                'bgHeader' => 'bg-sky/30 dark:bg-sky/10',
                'borderHeader' => 'border-sky/50',
                'iconBg' => 'bg-sky/50 dark:bg-sky/20',
                'iconColor' => 'text-blue-600 dark:text-blue-400',
            ],
        ];
    @endphp

    @foreach(['earning', 'deduction', 'benefit'] as $type)
        @php $config = $typeConfig[$type]; @endphp
        <div class="mb-6" x-data="{ expanded: true }">
            <button @click="expanded = !expanded"
                class="w-full flex items-center justify-between px-5 py-4 rounded-2xl {{ $config['bgHeader'] }} border {{ $config['borderHeader'] }} transition-all hover:shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl {{ $config['iconBg'] }} flex items-center justify-center">
                        <span class="material-icons-round {{ $config['iconColor'] }} text-[20px]">{{ $config['icon'] }}</span>
                    </div>
                    <div class="text-left">
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wide">{{ $config['label'] }}</h3>
                        <p class="text-xs text-slate-500">
                            {{ isset($components[$type]) ? $components[$type]->count() : 0 }} komponen
                        </p>
                    </div>
                </div>
                <span class="material-icons-round text-slate-400 text-[20px] transition-transform duration-200"
                    :class="expanded ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="expanded" x-collapse x-cloak class="mt-3 space-y-2">
                @if(isset($components[$type]) && $components[$type]->count() > 0)
                    @foreach($components[$type] as $component)
                        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 flex items-center gap-4 group hover:shadow-md transition-all">
                            <div class="flex-shrink-0 cursor-grab text-slate-300 dark:text-slate-600 hover:text-slate-500 dark:hover:text-slate-400">
                                <span class="material-icons-round text-[20px]">drag_indicator</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $component->name }}</h4>
                                    <span class="text-xs text-slate-400 font-mono">({{ $component->code }})</span>
                                    @if(!$component->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">Nonaktif</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                    @if($component->is_fixed)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-sage/30 text-emerald-700 dark:bg-sage/20 dark:text-emerald-400">Tetap</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-lavender/40 text-purple-700 dark:bg-lavender/20 dark:text-purple-400">Formula</span>
                                    @endif

                                    @if($component->is_fixed && $component->default_amount > 0)
                                        <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">Rp {{ number_format($component->default_amount, 0, ',', '.') }}</span>
                                    @elseif(!$component->is_fixed)
                                        <span class="text-xs text-slate-400 italic">Dihitung via rumus</span>
                                    @else
                                        <span class="text-xs text-slate-400">Rp 0</span>
                                    @endif

                                    <span class="text-slate-200 dark:text-slate-700">·</span>

                                    @if($component->is_taxable)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-peach/40 text-orange-700 dark:bg-peach/20 dark:text-orange-400">Kena Pajak</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-sky/40 text-blue-700 dark:bg-sky/20 dark:text-blue-400">Bebas Pajak</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                <a href="{{ route('admin.salary-components.edit', $component) }}"
                                    class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all"
                                    title="Edit">
                                    <span class="material-icons-round text-[18px]">edit</span>
                                </a>
                                <button @click="confirmDelete({{ $component->id }}, '{{ addslashes($component->name) }}')"
                                    class="p-2 rounded-xl text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all"
                                    title="Hapus">
                                    <span class="material-icons-round text-[18px]">delete</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-8 text-center">
                        <span class="material-icons-round text-slate-300 dark:text-slate-600 text-[40px] mb-2">inbox</span>
                        <p class="text-sm text-slate-400">Belum ada komponen {{ strtolower($config['label']) }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    @if(!isset($components['earning']) && !isset($components['deduction']) && !isset($components['benefit']))
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 text-center">
            <span class="material-icons-round text-slate-300 dark:text-slate-600 text-[56px] mb-3">tune</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Belum ada komponen gaji</h3>
            <p class="text-sm text-slate-500 mb-4">Mulai dengan menambahkan komponen gaji pertama</p>
            <a href="{{ route('admin.salary-components.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all">
                <span class="material-icons-round text-lg">add</span>
                Tambah Komponen
            </a>
        </div>
    @endif

    <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @click.self="showDeleteModal = false" x-cloak>
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-md p-6"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                    <span class="material-icons-round text-rose-600 dark:text-rose-400">warning</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white">Hapus Komponen</h3>
                    <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                Apakah Anda yakin ingin menghapus komponen <strong x-text="deleteName" class="text-slate-900 dark:text-white"></strong>?
            </p>
            <div class="flex justify-end gap-3">
                <button @click="showDeleteModal = false"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                    Batal
                </button>
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold bg-rose-600 text-white hover:bg-rose-700 transition-all shadow-lg shadow-rose-600/20">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function salaryComponentIndex() {
    return {
        showDeleteModal: false,
        deleteId: null,
        deleteName: '',
        get deleteUrl() {
            return `/admin/salary-components/${this.deleteId}`;
        },
        confirmDelete(id, name) {
            this.deleteId = id;
            this.deleteName = name;
            this.showDeleteModal = true;
        }
    };
}
</script>
@endpush
