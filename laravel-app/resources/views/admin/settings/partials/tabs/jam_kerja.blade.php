        <!-- CONTENT: JAM KERJA -->
        <div x-show="activeTab === 'jam_kerja'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- FORM JAM KERJA -->
                <div
                    class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Pengaturan Jam Kerja</h3>
                        <div
                            class="w-10 h-10 rounded-full bg-sage/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                            <span class="material-icons-round">schedule</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.work-hours.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam
                                        Masuk</label>
                                    <input type="time" name="work_start_time"
                                        value="{{ $settings->work_start_time ?? '08:00' }}"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam
                                        Keluar</label>
                                    <input type="time" name="work_end_time"
                                        value="{{ $settings->work_end_time ?? '17:00' }}"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Mulai
                                        Lembur</label>
                                    <input type="time" name="overtime_start_time"
                                        value="{{ $settings->overtime_start_time ?? '17:30' }}"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Selesai
                                        Lembur</label>
                                    <input type="time" name="overtime_end_time"
                                        value="{{ $settings->overtime_end_time ?? '21:00' }}"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Toleransi
                                    Keterlambatan (Menit)</label>
                                <input type="number" name="late_tolerance_minutes"
                                    value="{{ $settings->late_tolerance_minutes ?? '15' }}"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                            </div>

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- INFO CARD -->
                <div class="bg-sage/10 rounded-2xl p-6 border border-sage/20">
                    <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                        <span class="material-icons-round">info</span>
                        Informasi
                    </h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Pengaturan jam kerja ini akan berlaku untuk semua karyawan. Karyawan yang melakukan absensi di luar
                        jam masuk akan dihitung terlambat.
                    </p>
                </div>
            </div>
        </div>
