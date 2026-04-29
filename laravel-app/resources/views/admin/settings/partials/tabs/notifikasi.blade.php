        <!-- CONTENT: NOTIFIKASI -->
        <div x-show="activeTab === 'notifikasi'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Pengaturan Notifikasi</h3>
                    <div class="w-10 h-10 rounded-full bg-sky/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                        <span class="material-icons-round">notifications</span>
                    </div>
                </div>

                <form action="{{ route('admin.settings.notifications.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-6">
                        <!-- Toggles -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <div>
                                    <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Notifikasi Keterlambatan</p>
                                    <p class="text-xs text-slate-400">Kirim email saat karyawan terlambat check-in.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="notify_late_checkin" value="0">
                                    <input type="checkbox" name="notify_late_checkin" value="1" {{ $settings->notify_late_checkin ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-sage focus:ring-sage w-5 h-5">
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <div>
                                    <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Notifikasi Ketidakhadiran</p>
                                    <p class="text-xs text-slate-400">Kirim email saat karyawan tidak hadir tanpa keterangan.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="notify_absence" value="0">
                                    <input type="checkbox" name="notify_absence" value="1" {{ $settings->notify_absence ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-sage focus:ring-sage w-5 h-5">
                                </div>
                            </div>
                        </div>

                        <!-- Email List -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email Penerima Notifikasi</label>
                            <textarea name="notification_emails" rows="4" placeholder="email1@example.com&#10;email2@example.com"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-mono text-sm">{{ $settings->notification_emails }}</textarea>
                            <p class="text-xs text-slate-400 mt-1">Masukkan satu email per baris atau pisahkan dengan koma.</p>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                Simpan Pengaturan Notifikasi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
