        <!-- CONTENT: DEPARTEMEN -->
        <div x-show="activeTab === 'departemen'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-data="{
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                editDept: {},
                deleteDept: {}
            }">

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Manajemen Departemen</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Kelola struktur organisasi perusahaan.</p>
                    </div>
                    <button @click="showCreateModal = true"
                        class="px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg active:scale-95 flex items-center gap-2">
                        <span class="material-icons-round text-sm">add</span>
                        Tambah Departemen
                    </button>
                </div>

                @if($departments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kode</th>
                                    <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama</th>
                                    <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kepala Dept.</th>
                                    <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Parent</th>
                                    <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Karyawan</th>
                                    <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($departments as $dept)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="py-3 px-4">
                                            <span class="font-mono text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg">{{ $dept->code }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $dept->name }}</span>
                                            @if($dept->description)
                                                <p class="text-xs text-slate-400 mt-0.5 truncate max-w-[200px]">{{ $dept->description }}</p>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-600 dark:text-slate-400">
                                            {{ $dept->head?->name ?? '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-600 dark:text-slate-400">
                                            {{ $dept->parent?->name ?? '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                {{ $dept->employees_count }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $dept->is_active ? 'bg-sage/20 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $dept->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button @click="editDept = {
                                                    id: {{ $dept->id }},
                                                    name: '{{ addslashes($dept->name) }}',
                                                    code: '{{ $dept->code }}',
                                                    description: '{{ addslashes($dept->description ?? '') }}',
                                                    head_user_id: '{{ $dept->head_user_id ?? '' }}',
                                                    parent_id: '{{ $dept->parent_id ?? '' }}',
                                                    is_active: {{ $dept->is_active ? 'true' : 'false' }}
                                                }; showEditModal = true"
                                                    class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-slate-700">
                                                    <span class="material-icons-round text-sm">edit</span>
                                                </button>
                                                <button @click="deleteDept = { id: {{ $dept->id }}, name: '{{ addslashes($dept->name) }}', employees_count: {{ $dept->employees_count }} }; showDeleteModal = true"
                                                    class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-slate-500 hover:text-red-600">
                                                    <span class="material-icons-round text-sm">delete</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Empty State for Departments -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                            <span class="material-icons-round text-slate-400 text-3xl">corporate_fare</span>
                        </div>
                        <h4 class="font-bold text-slate-600 dark:text-slate-400 mb-1">Belum Ada Departemen</h4>
                        <p class="text-sm text-slate-400">Klik "Tambah Departemen" untuk membuat departemen pertama.</p>
                    </div>
                @endif
            </div>

            <!-- CREATE MODAL (Department) -->
            <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-lg shadow-2xl" @click.outside="showCreateModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Departemen</h3>
                        <button @click="showCreateModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            <span class="material-icons-round text-slate-400">close</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.settings.departments.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama *</label>
                                <input type="text" name="name" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kode *</label>
                                <input type="text" name="code" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold dark:text-white uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kepala Dept</label>
                                <select name="head_user_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold dark:text-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Parent Dept</label>
                                <select name="parent_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold dark:text-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded text-sage focus:ring-sage">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Aktif</label>
                            </div>
                            <button type="submit" class="w-full py-3 bg-slate-900 text-white rounded-xl font-bold">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- EDIT MODAL (Department) -->
            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-lg shadow-2xl" @click.outside="showEditModal = false">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Departemen</h3>
                        <button @click="showEditModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            <span class="material-icons-round text-slate-400">close</span>
                        </button>
                    </div>
                    <form :action="'{{ route('admin.settings.departments.store') }}/' + editDept.id" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama *</label>
                                <input type="text" name="name" x-model="editDept.name" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kode *</label>
                                <input type="text" name="code" x-model="editDept.code" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold dark:text-white uppercase">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kepala Dept</label>
                                <select name="head_user_id" x-model="editDept.head_user_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold dark:text-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Parent Dept</label>
                                <select name="parent_id" x-model="editDept.parent_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold dark:text-white">
                                    <option value="">-- Pilih --</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" x-model="editDept.is_active" class="rounded text-sage focus:ring-sage">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Aktif</label>
                            </div>
                            <button type="submit" class="w-full py-3 bg-slate-900 text-white rounded-xl font-bold">Update</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- DELETE MODAL (Department) -->
            <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl" @click.outside="showDeleteModal = false">
                    <div class="text-center">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Hapus Departemen?</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">
                            Apakah Anda yakin ingin menghapus departemen <strong x-text="deleteDept.name"></strong>?
                        </p>
                        <p x-show="deleteDept.employees_count > 0" class="text-sm text-red-500 font-bold mb-4">
                            Departemen ini masih memiliki <span x-text="deleteDept.employees_count"></span> karyawan.
                        </p>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button @click="showDeleteModal = false"
                            class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                            Batal
                        </button>
                        <form :action="'{{ url('/admin/settings/departments') }}/' + deleteDept.id" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full py-2.5 bg-red-500 text-white rounded-xl font-bold hover:bg-red-600 transition-all">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
