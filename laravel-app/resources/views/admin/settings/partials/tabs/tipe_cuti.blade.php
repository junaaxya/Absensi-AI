        <!-- CONTENT: TIPE CUTI -->
        <div x-show="activeTab === 'tipe_cuti'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-data="{
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                editLeaveType: {},
                deleteLeaveType: {}
            }">

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tipe Cuti</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Kelola jenis cuti dan kuota tahunan.</p>
                    </div>
                    <button @click="showCreateModal = true"
                        class="px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg active:scale-95 flex items-center gap-2">
                        <span class="material-icons-round text-sm">add</span>
                        Tambah Tipe Cuti
                    </button>
                </div>

                @if($leaveTypes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kode</th>
                                    <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama</th>
                                    <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kuota (Hari)</th>
                                    <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Berbayar</th>
                                    <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($leaveTypes as $leaveType)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="py-3 px-4">
                                            <span class="font-mono text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg">
                                                {{ $leaveType->code }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $leaveType->name }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="font-bold text-sm text-slate-700 dark:text-slate-300">{{ $leaveType->quota }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $leaveType->is_paid ? 'bg-sage/20 text-green-700' : 'bg-slate-200 text-slate-500' }}">
                                                {{ $leaveType->is_paid ? 'Ya' : 'Tidak' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $leaveType->is_active ? 'bg-sage/20 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $leaveType->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button @click="editLeaveType = {{ $leaveType }}; showEditModal = true"
                                                    class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-slate-700">
                                                    <span class="material-icons-round text-sm">edit</span>
                                                </button>
                                                <button @click="deleteLeaveType = { id: {{ $leaveType->id }}, name: '{{ addslashes($leaveType->name) }}' }; showDeleteModal = true"
                                                    class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-slate-500 hover:text-red-600">
                                                    <span class="material-icons-round text-sm">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                            <span class="material-icons-round text-slate-400 text-3xl">beach_access</span>
                        </div>
                        <h4 class="font-bold text-slate-600 dark:text-slate-400 mb-1">Belum Ada Tipe Cuti</h4>
                        <p class="text-sm text-slate-400">Klik "Tambah Tipe Cuti" untuk menambahkan.</p>
                    </div>
                @endif
            </div>

            <!-- CREATE MODAL -->
            <div x-show="showCreateModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showCreateModal = false"></div>
                <div class="relative bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-lg shadow-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Tipe Cuti</h3>
                        <button @click="showCreateModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            <span class="material-icons-round text-slate-400">close</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.settings.leave-types.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Cuti *</label>
                                    <input type="text" name="name" required
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kode *</label>
                                    <input type="text" name="code" required maxlength="10"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-mono font-bold uppercase" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kuota (Hari) *</label>
                                <input type="number" name="quota" required min="0"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <input type="hidden" name="is_paid" value="0" />
                                    <input type="checkbox" name="is_paid" value="1" checked
                                        class="rounded border-slate-300 text-sage focus:ring-sage" />
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Cuti Berbayar</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <input type="hidden" name="is_active" value="0" />
                                    <input type="checkbox" name="is_active" value="1" checked
                                        class="rounded border-slate-300 text-sage focus:ring-sage" />
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Aktif</span>
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                Simpan Tipe Cuti
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- EDIT MODAL -->
            <div x-show="showEditModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showEditModal = false"></div>
                <div class="relative bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-lg shadow-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Tipe Cuti</h3>
                        <button @click="showEditModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            <span class="material-icons-round text-slate-400">close</span>
                        </button>
                    </div>
                    <form :action="'{{ route('admin.settings.leave-types.store') }}/' + editLeaveType.id" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Cuti *</label>
                                    <input type="text" name="name" x-model="editLeaveType.name" required
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kode *</label>
                                    <input type="text" name="code" x-model="editLeaveType.code" required maxlength="10"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-mono font-bold uppercase" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kuota (Hari) *</label>
                                <input type="number" name="quota" x-model="editLeaveType.quota" required min="0"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <input type="hidden" name="is_paid" value="0" />
                                    <input type="checkbox" name="is_paid" value="1" x-bind:checked="editLeaveType.is_paid"
                                        class="rounded border-slate-300 text-sage focus:ring-sage" />
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Cuti Berbayar</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                    <input type="hidden" name="is_active" value="0" />
                                    <input type="checkbox" name="is_active" value="1" x-bind:checked="editLeaveType.is_active"
                                        class="rounded border-slate-300 text-sage focus:ring-sage" />
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Aktif</span>
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                Update Tipe Cuti
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- DELETE MODAL -->
            <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                <div class="relative bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                            <span class="material-icons-round text-red-500 text-3xl">warning</span>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Hapus Tipe Cuti</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">
                            Apakah Anda yakin ingin menghapus <strong x-text="deleteLeaveType.name"></strong>?
                        </p>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button @click="showDeleteModal = false"
                            class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                            Batal
                        </button>
                        <form :action="'{{ url('/admin/settings/leave-types') }}/' + deleteLeaveType.id" method="POST" class="flex-1">
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
