@extends('layouts.admin')

@section('header-title', 'Kategori Aset')
@section('header-subtitle', 'Kelola Kategori Inventaris')

@section('content')

    <div x-data="{ showCreateModal: false, showEditModal: false, showDeleteModal: false, editData: {}, deleteData: {} }">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <a href="{{ route('admin.assets.index') }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium">
                <span class="material-icons-round text-lg">arrow_back</span>
                Kembali ke Daftar Aset
            </a>
            <button @click="showCreateModal = true"
                class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95 shrink-0">
                <span class="material-icons-round text-lg">add</span>
                Tambah Kategori
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($categories as $category)
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-lg transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-lg">{{ $category->name }}</h3>
                            <span class="font-mono text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg text-slate-600 dark:text-slate-400">{{ $category->code }}</span>
                        </div>
                        <div class="flex gap-1">
                            <button @click="editData = { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', code: '{{ addslashes($category->code) }}', description: '{{ addslashes($category->description ?? '') }}', depreciation_method: '{{ $category->depreciation_method }}', useful_life_years: '{{ $category->useful_life_years ?? '' }}' }; showEditModal = true"
                                class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button @click="deleteData = { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', count: {{ $category->assets_count }} }; showDeleteModal = true"
                                class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </div>
                    </div>

                    @if($category->description)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">{{ $category->description }}</p>
                    @endif

                    <div class="flex flex-wrap gap-2 mt-3">
                        @php
                            $methodLabel = match($category->depreciation_method) {
                                'straight_line' => 'Garis Lurus',
                                'declining_balance' => 'Saldo Menurun',
                                'none' => 'Tanpa Depresiasi',
                                default => $category->depreciation_method,
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">{{ $methodLabel }}</span>
                        @if($category->useful_life_years)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">{{ $category->useful_life_years }} tahun</span>
                        @endif
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-lavender/30 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">{{ $category->assets_count }} aset</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <span class="material-icons-round text-4xl text-slate-300">category</span>
                    <p class="text-slate-500 font-medium mt-2">Belum ada kategori aset</p>
                </div>
            @endforelse
        </div>

        {{-- Create Modal --}}
        <div x-show="showCreateModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="showCreateModal = false"
                class="bg-white dark:bg-card-dark rounded-2xl p-6 w-full max-w-lg border border-slate-200 dark:border-slate-800 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tambah Kategori Aset</h3>
                <form method="POST" action="{{ route('admin.assets.categories.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kode <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" required placeholder="ELK, FRN, KND"
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white font-mono" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                            <textarea name="description" rows="2"
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Metode Depresiasi <span class="text-rose-500">*</span></label>
                            <select name="depreciation_method" required
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">
                                <option value="straight_line">Garis Lurus (Straight Line)</option>
                                <option value="declining_balance">Saldo Menurun (Declining Balance)</option>
                                <option value="none">Tanpa Depresiasi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Masa Manfaat (Tahun)</label>
                            <input type="number" name="useful_life_years" min="1" max="100"
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="showEditModal = false"
                class="bg-white dark:bg-card-dark rounded-2xl p-6 w-full max-w-lg border border-slate-200 dark:border-slate-800 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit Kategori Aset</h3>
                <form :action="'/admin/assets/categories/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required x-model="editData.name"
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kode <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" required x-model="editData.code"
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white font-mono" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                            <textarea name="description" rows="2" x-model="editData.description"
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Metode Depresiasi <span class="text-rose-500">*</span></label>
                            <select name="depreciation_method" required x-model="editData.depreciation_method"
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">
                                <option value="straight_line">Garis Lurus (Straight Line)</option>
                                <option value="declining_balance">Saldo Menurun (Declining Balance)</option>
                                <option value="none">Tanpa Depresiasi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Masa Manfaat (Tahun)</label>
                            <input type="number" name="useful_life_years" min="1" max="100" x-model="editData.useful_life_years"
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
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
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Hapus Kategori</h3>
                </div>
                <template x-if="deleteData.count > 0">
                    <p class="text-slate-600 dark:text-slate-400 mb-6">Kategori <strong x-text="deleteData.name" class="text-slate-900 dark:text-white"></strong> memiliki <strong x-text="deleteData.count"></strong> aset dan tidak dapat dihapus.</p>
                </template>
                <template x-if="deleteData.count === 0">
                    <div>
                        <p class="text-slate-600 dark:text-slate-400 mb-6">Apakah Anda yakin ingin menghapus kategori <strong x-text="deleteData.name" class="text-slate-900 dark:text-white"></strong>?</p>
                        <div class="flex gap-3 justify-end">
                            <button @click="showDeleteModal = false"
                                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                Batal
                            </button>
                            <form :action="'/admin/assets/categories/' + deleteData.id" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2.5 rounded-xl bg-rose-500 text-white font-bold text-sm hover:bg-rose-600 transition-colors shadow-lg shadow-rose-500/20">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </template>
                <template x-if="deleteData.count > 0">
                    <div class="flex justify-end">
                        <button @click="showDeleteModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Tutup
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

@endsection
