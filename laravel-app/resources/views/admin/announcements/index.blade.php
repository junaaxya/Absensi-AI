@extends('layouts.admin')

@section('header-title', 'Pengumuman')
@section('header-subtitle', 'Kelola Informasi dan Berita untuk Pengguna')

@section('content')

    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editData: {},
        deleteData: {},
        resetForm() {
            this.editData = {};
        }
    }">

        <!-- ACTIONS -->
        <div class="flex justify-between items-center mb-6">
            <div class="relative w-full max-w-md">
                <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" placeholder="Cari pengumuman..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm" />
            </div>
            <button @click="showCreateModal = true"
                class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95">
                <span class="material-icons-round text-lg">add</span>
                Buat Pengumuman
            </button>
        </div>

        <!-- ANNOUNCEMENTS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($announcements as $announcement)
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-xl transition-all relative group">
                    <!-- Type Badge -->
                    <div class="absolute top-4 right-4">
                        @php
                            $typeColor = match($announcement->type) {
                                'info' => 'bg-blue-100 text-blue-700',
                                'warning' => 'bg-yellow-100 text-yellow-700',
                                'danger' => 'bg-red-100 text-red-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                            $icon = match($announcement->type) {
                                'info' => 'info',
                                'warning' => 'warning',
                                'danger' => 'error',
                                default => 'campaign',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold {{ $typeColor }} uppercase tracking-wide">
                            <span class="material-icons-round text-sm">{{ $icon }}</span>
                            {{ $announcement->type }}
                        </span>
                    </div>

                    <div class="mb-4 pr-20">
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-tight mb-2 line-clamp-2">
                            {{ $announcement->title }}
                        </h3>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">
                            Target: {{ $announcement->target_role === 'all' ? 'Semua User' : ucfirst($announcement->target_role) }}
                        </p>
                    </div>

                    <div class="text-slate-600 dark:text-slate-400 text-sm mb-6 line-clamp-3 h-16">
                        {{ $announcement->content }}
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="text-xs text-slate-400">
                            <div class="flex items-center gap-1">
                                <span class="material-icons-round text-sm">event</span>
                                {{ \Carbon\Carbon::parse($announcement->start_date)->format('d M') }} - 
                                {{ \Carbon\Carbon::parse($announcement->end_date)->format('d M Y') }}
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="editData = {{ $announcement }}; showEditModal = true"
                                class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <button @click="deleteData = { id: {{ $announcement->id }}, title: '{{ addslashes($announcement->title) }}' }; showDeleteModal = true"
                                class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-icons-round text-slate-400 text-4xl">campaign</span>
                    </div>
                    <h3 class="text-slate-900 dark:text-white font-bold text-lg">Belum ada pengumuman</h3>
                    <p class="text-slate-500 text-sm mt-1">Buat pengumuman baru untuk memberitahu pengguna.</p>
                </div>
            @endforelse
        </div>

        <!-- PAGINATION -->
        <div class="mt-8">
            {{ $announcements->links() }}
        </div>

        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" style="display: none;" x-transition.opacity 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-lg shadow-2xl" @click.outside="showCreateModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Buat Pengumuman Baru</h3>
                    <button @click="showCreateModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-icons-round text-slate-400">close</span>
                    </button>
                </div>

                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul</label>
                            <input type="text" name="title" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Konten</label>
                            <textarea name="content" rows="4" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tipe</label>
                                <select name="type" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">
                                    <option value="info">Info (Biru)</option>
                                    <option value="warning">Warning (Kuning)</option>
                                    <option value="danger">Urgent (Merah)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Target</label>
                                <select name="target_role" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">
                                    <option value="all">Semua User</option>
                                    <option value="user">Karyawan</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Mulai</label>
                                <input type="date" name="start_date" required value="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Selesai</label>
                                <input type="date" name="end_date" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="showCreateModal = false"
                                class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT MODAL -->
        <div x-show="showEditModal" style="display: none;" x-transition.opacity 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-lg shadow-2xl" @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Pengumuman</h3>
                    <button @click="showEditModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-icons-round text-slate-400">close</span>
                    </button>
                </div>

                <form :action="`{{ route('admin.announcements.index') }}/${editData.id}`" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul</label>
                            <input type="text" name="title" x-model="editData.title" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Konten</label>
                            <textarea name="content" rows="4" x-model="editData.content" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tipe</label>
                                <select name="type" x-model="editData.type" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">
                                    <option value="info">Info (Biru)</option>
                                    <option value="warning">Warning (Kuning)</option>
                                    <option value="danger">Urgent (Merah)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Target</label>
                                <select name="target_role" x-model="editData.target_role" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">
                                    <option value="all">Semua User</option>
                                    <option value="user">Karyawan</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Mulai</label>
                                <input type="date" name="start_date" x-model="editData.start_date" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Selesai</label>
                                <input type="date" name="end_date" x-model="editData.end_date" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white" />
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="showEditModal = false"
                                class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition-all">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- DELETE MODAL -->
        <div x-show="showDeleteModal" style="display: none;" x-transition.opacity 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl text-center" @click.outside="showDeleteModal = false">
                <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-icons-round text-rose-500 text-3xl">delete_forever</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Hapus Pengumuman?</h3>
                <p class="text-slate-500 text-sm mb-6">
                    Anda akan menghapus pengumuman "<span x-text="deleteData.title" class="font-bold text-slate-700 dark:text-slate-300"></span>". Tindakan ini tidak dapat dibatalkan.
                </p>

                <form :action="`{{ route('admin.announcements.index') }}/${deleteData.id}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3 justify-center">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-rose-500 text-white rounded-xl font-bold hover:bg-rose-600 transition-all shadow-lg shadow-rose-500/30 active:scale-95">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
