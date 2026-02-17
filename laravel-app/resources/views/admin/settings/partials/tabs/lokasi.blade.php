        <!-- CONTENT: LOKASI KANTOR -->
        <div x-show="activeTab === 'lokasi'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Lokasi Kantor</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Titik koordinat untuk validasi radius absensi.
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-full bg-peach/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                        <span class="material-icons-round">location_on</span>
                    </div>
                </div>

                <form action="{{ route('admin.settings.location.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Latitude</label>
                            <input type="text" name="office_latitude" value="{{ $settings->office_latitude ?? '-6.2088' }}"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-mono text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Longitude</label>
                            <input type="text" name="office_longitude"
                                value="{{ $settings->office_longitude ?? '106.8456' }}"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-mono text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Radius Maksimal
                                (Kilometer)</label>
                            <input type="number" step="0.01" name="office_radius"
                                value="{{ $settings->office_radius ?? '0.1' }}"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                            <p class="text-xs text-slate-400 mt-1">Jarak maksimal karyawan diizinkan absen dari titik
                                kantor.</p>
                        </div>
                    </div>

                    <!-- MAP PREVIEW (Placeholder) -->
                    <div class="w-full h-64 bg-slate-100 rounded-xl overflow-hidden mb-6 relative group">
                        <iframe width="100%" height="100%" id="gmap_canvas"
                            src="https://maps.google.com/maps?q={{ $settings->office_latitude ?? '-6.2088' }},{{ $settings->office_longitude ?? '106.8456' }}&t=&z=15&ie=UTF8&iwloc=&output=embed"
                            frameborder="0" scrolling="no" marginheight="0" marginwidth="0">
                        </iframe>
                        <div
                            class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <span class="text-white font-bold bg-black/50 px-4 py-2 rounded-lg backdrop-blur-sm">Preview
                                Lokasi</span>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                            Simpan Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
