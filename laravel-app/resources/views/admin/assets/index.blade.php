@extends('layouts.admin')

@section('header-title', 'Manajemen Aset')
@section('header-subtitle', 'Kelola Inventaris dan Aset Perusahaan')

@section('content')

    <div x-data="{ showDeleteModal: false, deleteData: {} }">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center">
                        <span class="material-icons-round text-sky-600 dark:text-sky-400">inventory_2</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalAssets) }}</p>
                        <p class="text-xs text-slate-500">Total Aset</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <span class="material-icons-round text-emerald-600 dark:text-emerald-400">payments</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500">Total Nilai</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-lavender/30 dark:bg-purple-900/30 flex items-center justify-center">
                        <span class="material-icons-round text-purple-600 dark:text-purple-400">person</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($assignedCount) }}</p>
                        <p class="text-xs text-slate-500">Ditugaskan</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <span class="material-icons-round text-amber-600 dark:text-amber-400">build</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($maintenanceNeeded) }}</p>
                        <p class="text-xs text-slate-500">Perlu Perbaikan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters & Actions --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <form method="GET" action="{{ route('admin.assets.index') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative">
                    <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aset..."
                        class="w-full sm:w-56 pl-10 pr-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm" />
                </div>
                <select name="category" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="condition" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm">
                    <option value="">Semua Kondisi</option>
                    <option value="baik" {{ request('condition') === 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak_ringan" {{ request('condition') === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ request('condition') === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                    <option value="hilang" {{ request('condition') === 'hilang' ? 'selected' : '' }}>Hilang</option>
                    <option value="dihapuskan" {{ request('condition') === 'dihapuskan' ? 'selected' : '' }}>Dihapuskan</option>
                </select>
                <select name="assignment" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="assigned" {{ request('assignment') === 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                    <option value="available" {{ request('assignment') === 'available' ? 'selected' : '' }}>Tersedia</option>
                </select>
            </form>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('admin.assets.categories') }}"
                    class="bg-white dark:bg-card-dark text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-sm">
                    <span class="material-icons-round text-lg">category</span>
                    Kategori
                </a>
                <a href="{{ route('admin.assets.create') }}"
                    class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95 shrink-0">
                    <span class="material-icons-round text-lg">add</span>
                    Tambah Aset
                </a>
            </div>
        </div>

        {{-- Assets Table --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="text-left px-6 py-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase tracking-wider">Aset</th>
                            <th class="text-left px-6 py-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase tracking-wider">Kode</th>
                            <th class="text-left px-6 py-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase tracking-wider">Kategori</th>
                            <th class="text-left px-6 py-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase tracking-wider">Kondisi</th>
                            <th class="text-left px-6 py-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase tracking-wider">Ditugaskan</th>
                            <th class="text-right px-6 py-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase tracking-wider">Nilai</th>
                            <th class="text-center px-6 py-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($assets as $asset)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($asset->photo)
                                            <img src="{{ asset('storage/' . $asset->photo) }}" alt="{{ $asset->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                                <span class="material-icons-round text-slate-400 text-lg">inventory_2</span>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.assets.show', $asset) }}" class="font-bold text-slate-900 dark:text-white hover:text-sky-600 transition-colors">{{ $asset->name }}</a>
                                            @if($asset->serial_number)
                                                <p class="text-xs text-slate-500">SN: {{ $asset->serial_number }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg text-slate-700 dark:text-slate-300">{{ $asset->asset_code }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $asset->category->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $conditionStyle = match($asset->condition) {
                                            'baik' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                            'rusak_ringan' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                            'rusak_berat' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
                                            'hilang' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                            'dihapuskan' => 'bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-500',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                        $conditionLabel = match($asset->condition) {
                                            'baik' => 'Baik',
                                            'rusak_ringan' => 'Rusak Ringan',
                                            'rusak_berat' => 'Rusak Berat',
                                            'hilang' => 'Hilang',
                                            'dihapuskan' => 'Dihapuskan',
                                            default => $asset->condition,
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $conditionStyle }}">{{ $conditionLabel }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($asset->assignedUser)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden">
                                                <img src="{{ $asset->assignedUser->profile_photo_url }}" alt="" class="w-full h-full object-cover">
                                            </div>
                                            <span class="text-slate-700 dark:text-slate-300 text-xs font-medium">{{ $asset->assignedUser->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Tersedia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-slate-700 dark:text-slate-300">
                                    Rp {{ number_format($asset->current_value, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('admin.assets.show', $asset) }}"
                                            class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                            <span class="material-icons-round text-lg">visibility</span>
                                        </a>
                                        <a href="{{ route('admin.assets.edit', $asset) }}"
                                            class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                            <span class="material-icons-round text-lg">edit</span>
                                        </a>
                                        <button @click="deleteData = { id: {{ $asset->id }}, name: '{{ addslashes($asset->name) }}' }; showDeleteModal = true"
                                            class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                                            <span class="material-icons-round text-lg">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-icons-round text-4xl text-slate-300">inventory_2</span>
                                        <p class="text-slate-500 font-medium">Belum ada data aset</p>
                                        <a href="{{ route('admin.assets.create') }}" class="text-sky-600 hover:text-sky-700 text-sm font-bold">Tambah Aset Pertama</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assets->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $assets->links() }}
                </div>
            @endif
        </div>

        {{-- Delete Modal --}}
        <div x-show="showDeleteModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="showDeleteModal = false"
                class="bg-white dark:bg-card-dark rounded-2xl p-6 w-full max-w-md border border-slate-200 dark:border-slate-800 shadow-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                        <span class="material-icons-round text-rose-500">warning</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Hapus Aset</h3>
                </div>
                <p class="text-slate-600 dark:text-slate-400 mb-6">Apakah Anda yakin ingin menghapus aset <strong x-text="deleteData.name" class="text-slate-900 dark:text-white"></strong>?</p>
                <div class="flex gap-3 justify-end">
                    <button @click="showDeleteModal = false"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    <form :action="'/admin/assets/' + deleteData.id" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-rose-500 text-white font-bold text-sm hover:bg-rose-600 transition-colors shadow-lg shadow-rose-500/20">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
