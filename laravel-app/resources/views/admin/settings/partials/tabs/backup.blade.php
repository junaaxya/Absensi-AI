        <!-- CONTENT: BACKUP & MAINTENANCE -->
        <div x-show="activeTab === 'backup'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- CONFIGURATION -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Konfigurasi Backup</h3>
                            <div class="w-10 h-10 rounded-full bg-peach/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                                <span class="material-icons-round">settings_backup_restore</span>
                            </div>
                        </div>

                        <form action="{{ route('admin.settings.backup-config.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Retensi Backup (Hari)</label>
                                    <input type="number" name="backup_retention_days" value="{{ $settings->backup_retention_days ?? 30 }}" min="1"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                    <p class="text-xs text-slate-400 mt-1">Hapus otomatis backup yang lebih lama dari hari ini.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Retensi Audit Log (Hari)</label>
                                    <input type="number" name="audit_log_retention_days" value="{{ $settings->audit_log_retention_days ?? 90 }}" min="1"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>

                                <div class="pt-2">
                                    <button type="submit"
                                        class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                        Simpan Konfigurasi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ACTIONS -->
                    <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4">Tindakan Cepat</h3>
                        <div class="space-y-3">
                            <form action="{{ route('admin.backups.create') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-3 bg-sky/20 text-sky-700 dark:text-sky-300 rounded-xl font-bold hover:bg-sky/30 transition-all flex items-center justify-center gap-2">
                                    <span class="material-icons-round">cloud_upload</span>
                                    Backup Database Sekarang
                                </button>
                            </form>

                            <form action="{{ route('admin.cleanup') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan data lama? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                <button type="submit"
                                    class="w-full py-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl font-bold hover:bg-red-100 dark:hover:bg-red-900/40 transition-all flex items-center justify-center gap-2">
                                    <span class="material-icons-round">cleaning_services</span>
                                    Bersihkan Data Lama
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- BACKUP LIST -->
                <div class="lg:col-span-2 bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Riwayat Backup</h3>
                        <div class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-600 dark:text-slate-400">
                            Total: {{ count($backups ?? []) }} File
                        </div>
                    </div>

                    @if(count($backups ?? []) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-700">
                                        <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama File</th>
                                        <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Ukuran</th>
                                        <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="text-center py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($backups as $backup)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="py-3 px-4">
                                                <span class="font-mono text-sm font-bold text-slate-700 dark:text-slate-300">{{ $backup['filename'] }}</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="text-sm text-slate-600 dark:text-slate-400">{{ $backup['size'] }}</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="text-sm text-slate-600 dark:text-slate-400">{{ $backup['date'] }}</span>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('admin.backups.download', ['filename' => $backup['filename']]) }}"
                                                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-500 hover:text-slate-700"
                                                        title="Download">
                                                        <span class="material-icons-round text-sm">download</span>
                                                    </a>
                                                    <form action="{{ route('admin.backups.destroy', ['filename' => $backup['filename']]) }}" method="POST" onsubmit="return confirm('Hapus file backup ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-slate-500 hover:text-red-600"
                                                            title="Hapus">
                                                            <span class="material-icons-round text-sm">delete</span>
                                                        </button>
                                                    </form>
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
                                <span class="material-icons-round text-slate-400 text-3xl">cloud_off</span>
                            </div>
                            <h4 class="font-bold text-slate-600 dark:text-slate-400 mb-1">Belum Ada Backup</h4>
                            <p class="text-sm text-slate-400">Klik "Backup Database Sekarang" untuk membuat backup pertama.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
