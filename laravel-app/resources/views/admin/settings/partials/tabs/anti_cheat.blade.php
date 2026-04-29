        <div x-show="activeTab === 'anti_cheat'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 max-w-2xl">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Anti-Cheat GPS</h3>
                    <div class="w-10 h-10 rounded-full bg-peach/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                        <span class="material-icons-round">gps_off</span>
                    </div>
                </div>

                <form action="{{ route('admin.settings.anti-cheat.update') }}" method="POST" x-data="{
                    enabled: {{ ($settings->enable_anti_cheat ?? true) ? 'true' : 'false' }}
                }">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-5">

                        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                            <div>
                                <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Aktifkan Anti-Cheat</p>
                                <p class="text-xs text-slate-400 mt-0.5">Deteksi otomatis anomali GPS, perangkat, dan lokasi palsu.</p>
                            </div>
                            <input type="hidden" name="enable_anti_cheat" :value="enabled ? '1' : '0'">
                            <button type="button" @click="enabled = !enabled"
                                :class="enabled ? 'bg-sage' : 'bg-slate-300 dark:bg-slate-600'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="enabled ? 'translate-x-6' : 'translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Maks Perangkat per Karyawan</label>
                            <input type="number" name="max_devices_per_user" value="{{ $settings->max_devices_per_user ?? 2 }}"
                                min="1" max="10"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                            <p class="text-xs text-slate-400 mt-1">Jumlah perangkat berbeda yang diizinkan per karyawan.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Threshold Peringatan</label>
                                <input type="number" name="anomaly_score_warning_threshold" value="{{ $settings->anomaly_score_warning_threshold ?? 30 }}"
                                    min="1" max="100"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                <p class="text-xs text-slate-400 mt-1">Skor anomali untuk peringatan.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Threshold Tolak</label>
                                <input type="number" name="anomaly_score_reject_threshold" value="{{ $settings->anomaly_score_reject_threshold ?? 60 }}"
                                    min="1" max="100"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                <p class="text-xs text-slate-400 mt-1">Skor anomali untuk menolak absensi.</p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/20 p-4">
                            <div class="flex items-start gap-3">
                                <span class="material-icons-round text-sky-600 mt-0.5">info</span>
                                <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                                    <p class="font-bold">Cara Kerja Skor Anomali</p>
                                    <p>Setiap absensi diperiksa 6 indikator: GPS terlalu stabil, kecepatan tidak wajar, akurasi paradoks, perangkat baru, timezone tidak cocok, dan mock GPS. Skor total menentukan apakah absensi diterima atau ditolak.</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                Simpan Pengaturan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
