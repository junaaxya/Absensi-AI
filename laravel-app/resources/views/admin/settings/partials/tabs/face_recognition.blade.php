        <!-- CONTENT: FACE RECOGNITION -->
        <div x-show="activeTab === 'face_recognition'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- FORM -->
                <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Face Recognition</h3>
                        <div class="w-10 h-10 rounded-full bg-sky/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                            <span class="material-icons-round">face</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.face-recognition.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-5">
                            <!-- Similarity Threshold Slider -->
                            <div x-data="{ threshold: {{ $settings->face_similarity_threshold ?? 0.5 }} }">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                    Similarity Threshold: <span class="text-sage" x-text="threshold.toFixed(2)"></span>
                                </label>
                                <input type="range" name="face_similarity_threshold" min="0.1" max="1.0" step="0.05"
                                    x-model="threshold"
                                    class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-sage" />
                                <div class="flex justify-between text-xs text-slate-400 mt-1">
                                    <span>0.1 (Longgar)</span>
                                    <span>1.0 (Ketat)</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Maks Foto Registrasi</label>
                                    <input type="number" name="face_max_registration_photos"
                                        value="{{ $settings->face_max_registration_photos ?? 6 }}" min="1" max="20"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Min Kualitas Foto</label>
                                    <input type="number" name="face_min_photo_quality"
                                        value="{{ $settings->face_min_photo_quality ?? 80 }}" min="0" max="100"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </form>

                    <form action="{{ route('admin.settings.face-recognition.reset') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit"
                            class="w-full py-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300 rounded-xl font-bold hover:bg-red-100 dark:hover:bg-red-900/30 transition-all text-sm flex items-center justify-center gap-2"
                            onclick="return confirm('Reset semua pengaturan face recognition?')">
                            <span class="material-icons-round text-sm">warning</span>
                            Reset ke Default
                        </button>
                    </form>
                </div>

                <!-- TOGGLES + STATUS -->
                <div class="space-y-6">
                    <!-- Toggle Cards -->
                    <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-4">Fitur Keamanan</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <div>
                                    <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Anti-Spoofing</p>
                                    <p class="text-xs text-slate-400">Deteksi apakah wajah berasal dari foto/video.</p>
                                </div>
                                <div class="px-3 py-1 rounded-full text-xs font-bold {{ $settings->face_anti_spoofing_enabled ? 'bg-sage/20 text-green-700' : 'bg-slate-200 text-slate-500' }}">
                                    {{ $settings->face_anti_spoofing_enabled ? 'Aktif' : 'Nonaktif' }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-xl">
                                <div>
                                    <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Liveness Detection</p>
                                    <p class="text-xs text-slate-400">Verifikasi bahwa wajah berasal dari orang yang hidup.</p>
                                </div>
                                <div class="px-3 py-1 rounded-full text-xs font-bold {{ $settings->face_require_liveness ? 'bg-sage/20 text-green-700' : 'bg-slate-200 text-slate-500' }}">
                                    {{ $settings->face_require_liveness ? 'Aktif' : 'Nonaktif' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Status -->
                    <div x-data="{
                        status: null,
                        loading: false,
                        async testConnection() {
                            this.loading = true;
                            this.status = null;
                            try {
                                const res = await fetch('{{ route('admin.settings.face-recognition.test') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    }
                                });
                                const data = await res.json();
                                this.status = data.status === 'ok' ? 'connected' : 'error';
                            } catch(e) {
                                this.status = 'error';
                            }
                            this.loading = false;
                        }
                    }" class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-4">Status Face Service</h4>

                        <div class="mb-4 p-4 rounded-xl"
                            :class="{
                                'bg-slate-50 dark:bg-slate-800': !status,
                                'bg-green-50 dark:bg-green-900/20': status === 'connected',
                                'bg-red-50 dark:bg-red-900/20': status === 'error'
                            }">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full"
                                    :class="{
                                        'bg-slate-400': !status,
                                        'bg-green-500 animate-pulse': status === 'connected',
                                        'bg-red-500': status === 'error'
                                    }"></div>
                                <span class="text-sm font-bold"
                                    :class="{
                                        'text-slate-500': !status,
                                        'text-green-700 dark:text-green-400': status === 'connected',
                                        'text-red-700 dark:text-red-400': status === 'error'
                                    }"
                                    x-text="status === 'connected' ? 'Terhubung' : (status === 'error' ? 'Tidak Terhubung' : 'Belum Dicek')"></span>
                            </div>
                        </div>

                        <button @click="testConnection()" :disabled="loading"
                            class="w-full py-2.5 bg-sky/20 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-sky/30 transition-all text-sm disabled:opacity-50">
                            <span x-show="!loading">Test Koneksi</span>
                            <span x-show="loading">Testing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
