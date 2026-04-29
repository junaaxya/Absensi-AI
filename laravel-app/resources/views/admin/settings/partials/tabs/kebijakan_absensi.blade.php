        <!-- CONTENT: KEBIJAKAN ABSENSI -->
        <div x-show="activeTab === 'kebijakan_absensi'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- FORM -->
                <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Kebijakan Absensi</h3>
                        <div class="w-10 h-10 rounded-full bg-peach/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                            <span class="material-icons-round">policy</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.attendance-policy.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Auto Checkout</label>
                                <input type="time" name="auto_checkout_time" value="{{ $settings->auto_checkout_time ?? '23:59' }}"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                <p class="text-xs text-slate-400 mt-1">Waktu otomatis checkout jika karyawan lupa.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Minimum Jam Kerja</label>
                                    <input type="number" name="minimum_work_hours" value="{{ $settings->minimum_work_hours ?? 8 }}"
                                        min="1" max="24"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Threshold Half-Day (Jam)</label>
                                    <input type="number" name="half_day_threshold_hours" value="{{ $settings->half_day_threshold_hours ?? 4 }}"
                                        min="1" max="12"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                    Simpan Kebijakan
                                </button>
                            </div>
                        </div>
                    </form>

                    <form action="{{ route('admin.settings.attendance-policy.reset') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit"
                            class="w-full py-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300 rounded-xl font-bold hover:bg-red-100 dark:hover:bg-red-900/30 transition-all text-sm flex items-center justify-center gap-2"
                            onclick="return confirm('Reset semua kebijakan absensi?')">
                            <span class="material-icons-round text-sm">warning</span>
                            Reset ke Default
                        </button>
                    </form>
                </div>

                <!-- TOGGLES + WEEKEND -->
                <div class="space-y-6">
                    <!-- Toggle Switches -->
                    <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-4">Opsi Tambahan</h4>

                        <!-- These are part of the same form but we need JS to handle it -->
                        <div x-data="{
                            requireCheckout: {{ $settings->require_checkout ? 'true' : 'false' }},
                            allowMultiple: {{ $settings->allow_multiple_checkin ? 'true' : 'false' }}
                        }" class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <div>
                                    <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Wajib Checkout</p>
                                    <p class="text-xs text-slate-400">Karyawan harus melakukan checkout untuk menyelesaikan absensi.</p>
                                </div>
                                <button type="button" @click="requireCheckout = !requireCheckout"
                                    :class="requireCheckout ? 'bg-sage' : 'bg-slate-300 dark:bg-slate-600'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                    <span :class="requireCheckout ? 'translate-x-6' : 'translate-x-1'"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                                </button>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <div>
                                    <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Izinkan Multi Check-in</p>
                                    <p class="text-xs text-slate-400">Karyawan bisa check-in lebih dari satu kali per hari.</p>
                                </div>
                                <button type="button" @click="allowMultiple = !allowMultiple"
                                    :class="allowMultiple ? 'bg-sage' : 'bg-slate-300 dark:bg-slate-600'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                    <span :class="allowMultiple ? 'translate-x-6' : 'translate-x-1'"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Weekend Days -->
                    <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-4">Hari Libur Mingguan</h4>
                        <div class="grid grid-cols-2 gap-2">
                            @php
                                $days = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
                                $weekendDays = $settings->weekend_days ?? ['Saturday', 'Sunday'];
                            @endphp
                            @foreach($days as $eng => $ind)
                                <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer">
                                    <input type="checkbox" value="{{ $eng }}"
                                        {{ in_array($eng, $weekendDays) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-sage focus:ring-sage" disabled />
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $ind }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-slate-400 mt-3">Hari yang dipilih tidak akan dihitung sebagai hari kerja.</p>
                    </div>

                    <!-- Manual Auto-Checkout Trigger -->
                    <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800"
                        x-data="{ loading: false, result: null }">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-slate-900 dark:text-white">Auto-Checkout Manual</h4>
                            <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                <span class="material-icons-round text-amber-600 dark:text-amber-400 text-sm">schedule_send</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                            Jalankan auto-checkout secara manual untuk karyawan yang belum checkout hari ini.
                            Waktu checkout akan menggunakan pengaturan Auto Checkout ({{ $settings->auto_checkout_time ?? '18:00' }}).
                        </p>

                        <form method="POST" action="{{ route('admin.auto-checkout.trigger') }}"
                            @submit.prevent="
                                loading = true;
                                result = null;
                                fetch($el.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(r => r.json())
                                .then(data => { result = data; loading = false; })
                                .catch(() => { result = { success: false, message: 'Terjadi kesalahan.' }; loading = false; })
                            ">
                            <button type="submit" :disabled="loading"
                                class="w-full py-2.5 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 rounded-xl font-bold hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-all text-sm flex items-center justify-center gap-2 disabled:opacity-50"
                                onclick="return confirm('Jalankan auto-checkout sekarang?')">
                                <span class="material-icons-round text-sm" x-show="!loading">play_arrow</span>
                                <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-text="loading ? 'Memproses...' : 'Jalankan Auto-Checkout'"></span>
                            </button>
                        </form>

                        <div x-show="result" x-transition class="mt-3">
                            <div x-show="result?.success"
                                class="p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-xl text-xs font-medium">
                                <span x-text="result?.message"></span>
                            </div>
                            <div x-show="result && !result?.success"
                                class="p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-xl text-xs font-medium">
                                <span x-text="result?.message"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
