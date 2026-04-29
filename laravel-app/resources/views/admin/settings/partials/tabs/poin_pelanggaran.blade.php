        <!-- CONTENT: POIN PELANGGARAN -->
        <div x-show="activeTab === 'poin_pelanggaran'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- DEDUCTION SETTINGS -->
                <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Potongan Pelanggaran</h3>
                        <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400">
                            <span class="material-icons-round">gavel</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.violation-settings.update') }}" method="POST"
                        x-data="{ deductionType: '{{ $settings->violation_deduction_type ?? 'per_point' }}' }">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Tipe Potongan</label>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition-all"
                                        :class="deductionType === 'per_point' ? 'ring-2 ring-sage' : ''">
                                        <input type="radio" name="violation_deduction_type" value="per_point"
                                            x-model="deductionType"
                                            class="text-sage focus:ring-sage" />
                                        <div>
                                            <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Per Poin</p>
                                            <p class="text-xs text-slate-400">Potongan dihitung berdasarkan jumlah poin pelanggaran.</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition-all"
                                        :class="deductionType === 'percentage' ? 'ring-2 ring-sage' : ''">
                                        <input type="radio" name="violation_deduction_type" value="percentage"
                                            x-model="deductionType"
                                            class="text-sage focus:ring-sage" />
                                        <div>
                                            <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Persentase Gaji</p>
                                            <p class="text-xs text-slate-400">Potongan berdasarkan persentase gaji pokok.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div x-show="deductionType === 'per_point'" x-transition>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Potongan Per Poin (Rp)</label>
                                <input type="number" name="violation_deduction_per_point"
                                    value="{{ $settings->violation_deduction_per_point ?? 25000 }}"
                                    min="0" step="1000"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                <p class="text-xs text-slate-400 mt-1">Jumlah potongan untuk setiap poin pelanggaran.</p>
                            </div>

                            <div x-show="deductionType === 'percentage'" x-transition>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Persentase Potongan (%)</label>
                                <input type="number" name="violation_deduction_percentage"
                                    value="{{ $settings->violation_deduction_percentage ?? 0 }}"
                                    min="0" max="100" step="0.5"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                <p class="text-xs text-slate-400 mt-1">Persentase dari gaji pokok yang dipotong.</p>
                            </div>

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                    Simpan Pengaturan Potongan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- SP THRESHOLDS -->
                <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Ambang Batas Surat Peringatan</h3>
                        <div class="w-10 h-10 rounded-full bg-peach/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                            <span class="material-icons-round">warning</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-3 bg-sage/10 dark:bg-sage/5 rounded-xl border border-sage/20">
                            <span class="inline-flex items-center rounded-full bg-sage px-3 py-1 text-xs font-bold text-slate-900">SP1</span>
                            <div class="flex-1">
                                <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Surat Peringatan 1</p>
                                <p class="text-xs text-slate-400">Peringatan awal untuk karyawan.</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $settings->sp1_threshold ?? 10 }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Poin</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-peach/10 dark:bg-peach/5 rounded-xl border border-peach/20">
                            <span class="inline-flex items-center rounded-full bg-peach px-3 py-1 text-xs font-bold text-slate-900">SP2</span>
                            <div class="flex-1">
                                <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Surat Peringatan 2</p>
                                <p class="text-xs text-slate-400">Peringatan kedua, eskalasi.</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $settings->sp2_threshold ?? 20 }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Poin</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-rose-50 dark:bg-rose-900/10 rounded-xl border border-rose-200 dark:border-rose-800/30">
                            <span class="inline-flex items-center rounded-full bg-rose-400 px-3 py-1 text-xs font-bold text-white">SP3</span>
                            <div class="flex-1">
                                <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Surat Peringatan 3</p>
                                <p class="text-xs text-slate-400">Peringatan terakhir sebelum tindakan.</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $settings->sp3_threshold ?? 30 }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Poin</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.violation-settings.update') }}" method="POST" class="mt-6">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="violation_deduction_type" value="{{ $settings->violation_deduction_type ?? 'per_point' }}" />
                        <input type="hidden" name="violation_deduction_per_point" value="{{ $settings->violation_deduction_per_point ?? 25000 }}" />
                        <input type="hidden" name="violation_deduction_percentage" value="{{ $settings->violation_deduction_percentage ?? 0 }}" />

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">SP1</label>
                                <input type="number" name="sp1_threshold"
                                    value="{{ $settings->sp1_threshold ?? 10 }}"
                                    min="1"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold text-center" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">SP2</label>
                                <input type="number" name="sp2_threshold"
                                    value="{{ $settings->sp2_threshold ?? 20 }}"
                                    min="1"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold text-center" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">SP3</label>
                                <input type="number" name="sp3_threshold"
                                    value="{{ $settings->sp3_threshold ?? 30 }}"
                                    min="1"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold text-center" />
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                Simpan Ambang Batas SP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
