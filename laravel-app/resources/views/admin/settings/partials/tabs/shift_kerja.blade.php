        <!-- CONTENT: SHIFT KERJA -->
        <div x-show="activeTab === 'shift_kerja'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-data="{
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                editShift: {},
                deleteShift: {}
            }">

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Shift Kerja</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Atur jadwal shift kerja karyawan.</p>
                    </div>
                    <button @click="showCreateModal = true"
                        class="px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg active:scale-95 flex items-center gap-2">
                        <span class="material-icons-round text-sm">add</span>
                        Tambah Shift
                    </button>
                </div>

                @if($shifts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama</th>
                                    <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jam</th>
                                    <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hari</th>
                                    <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($shifts as $shift)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="py-3 px-4">
                                            <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $shift->name }}</span>
                                            @if($shift->description)
                                                <p class="text-xs text-slate-400 mt-0.5 truncate max-w-[200px]">{{ $shift->description }}</p>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-mono text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg">
                                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-600 dark:text-slate-400">
                                            {{ implode(', ', $shift->days) }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $shift->is_active ? 'bg-sage/20 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $shift->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button @click="editShift = {{ $shift }}; showEditModal = true"
                                                    class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-slate-700">
                                                    <span class="material-icons-round text-sm">edit</span>
                                                </button>
                                                <button @click="deleteShift = { id: {{ $shift->id }}, name: '{{ addslashes($shift->name) }}' }; showDeleteModal = true"
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
                            <span class="material-icons-round text-slate-400 text-3xl">schedule</span>
                        </div>
                        <h4 class="font-bold text-slate-600 dark:text-slate-400 mb-1">Belum Ada Shift</h4>
                        <p class="text-sm text-slate-400">Klik "Tambah Shift" untuk membuat shift kerja baru.</p>
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
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Shift Kerja</h3>
                        <button @click="showCreateModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            <span class="material-icons-round text-slate-400">close</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.settings.shifts.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Shift *</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam Masuk *</label>
                                    <input type="time" name="start_time" required
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam Keluar *</label>
                                    <input type="time" name="end_time" required
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Hari Kerja</label>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                        <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer border border-slate-100 dark:border-slate-700">
                                            <input type="checkbox" name="days[]" value="{{ $day }}" checked
                                                class="rounded border-slate-300 text-sage focus:ring-sage" />
                                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                                <textarea name="description" rows="2"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white"></textarea>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <input type="hidden" name="is_active" value="0" />
                                <input type="checkbox" name="is_active" value="1" checked
                                    class="rounded border-slate-300 text-sage focus:ring-sage" />
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Aktif</span>
                            </div>
                            <button type="submit"
                                class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                Simpan Shift
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
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Shift Kerja</h3>
                        <button @click="showEditModal = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            <span class="material-icons-round text-slate-400">close</span>
                        </button>
                    </div>
                    <form :action="'{{ route('admin.settings.shifts.store') }}/' + editShift.id" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Shift *</label>
                                <input type="text" name="name" x-model="editShift.name" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam Masuk *</label>
                                    <input type="time" name="start_time" x-model="editShift.start_time" required
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam Keluar *</label>
                                    <input type="time" name="end_time" x-model="editShift.end_time" required
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white font-bold" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Hari Kerja</label>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                        <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer border border-slate-100 dark:border-slate-700">
                                            <input type="checkbox" name="days[]" value="{{ $day }}"
                                                :checked="editShift.days && editShift.days.includes('{{ $day }}')"
                                                class="rounded border-slate-300 text-sage focus:ring-sage" />
                                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                                <textarea name="description" rows="2" x-model="editShift.description"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-slate-900 dark:text-white"></textarea>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <input type="hidden" name="is_active" value="0" />
                                <input type="checkbox" name="is_active" value="1" x-bind:checked="editShift.is_active"
                                    class="rounded border-slate-300 text-sage focus:ring-sage" />
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Aktif</span>
                            </div>
                            <button type="submit"
                                class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                Update Shift
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
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Hapus Shift</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">
                            Apakah Anda yakin ingin menghapus shift <strong x-text="deleteShift.name"></strong>?
                        </p>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button @click="showDeleteModal = false"
                            class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                            Batal
                        </button>
                        <form :action="'{{ url('/admin/settings/shifts') }}/' + deleteShift.id" method="POST" class="flex-1">
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
