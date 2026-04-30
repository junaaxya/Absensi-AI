        <!-- CONTENT: PROFIL PERUSAHAAN -->
        <div x-show="activeTab === 'profil_perusahaan'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- FORM -->
                <div class="md:col-span-2 bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Profil Perusahaan</h3>
                        <div class="w-10 h-10 rounded-full bg-lavender/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                            <span class="material-icons-round">business</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.company-profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Perusahaan</label>
                                <input type="text" name="company_name" value="{{ $settings->company_name }}"
                                    placeholder="PT. Contoh Indonesia"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alamat</label>
                                <textarea name="company_address" rows="3" placeholder="Jl. Contoh No. 123, Jakarta"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white">{{ $settings->company_address }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Telepon</label>
                                    <input type="text" name="company_phone" value="{{ $settings->company_phone }}"
                                        placeholder="021-12345678"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email</label>
                                    <input type="email" name="company_email" value="{{ $settings->company_email }}"
                                        placeholder="info@perusahaan.com"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Website</label>
                                    <input type="url" name="company_website" value="{{ $settings->company_website }}"
                                        placeholder="https://perusahaan.com"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">NPWP</label>
                                    <input type="text" name="company_npwp" value="{{ $settings->company_npwp }}"
                                        placeholder="01.234.567.8-901.000"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-mono text-sm" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Logo Perusahaan</label>
                                <div class="flex items-center gap-4">
                                    @if($settings->company_logo)
                                        <img src="{{ Storage::url($settings->company_logo) }}" alt="Logo"
                                            class="w-16 h-16 rounded-xl object-contain bg-slate-100 border border-slate-200" />
                                    @else
                                        <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                            <span class="material-icons-round text-slate-400">image</span>
        </div>

                                    @endif
                                    <input type="file" name="company_logo" accept="image/jpg,image/jpeg,image/png,image/svg+xml"
                                        class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-all" />
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, SVG. Maks 10MB. Disarankan ukuran &lt; 2MB untuk performa optimal.</p>
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="submit"
                                    class="flex-1 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95">
                                    Simpan Profil
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Reset Form (separate) -->
                    <form action="{{ route('admin.settings.company-profile.reset') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit"
                            class="w-full py-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300 rounded-xl font-bold hover:bg-red-100 dark:hover:bg-red-900/30 transition-all text-sm flex items-center justify-center gap-2"
                            onclick="return confirm('Reset semua data profil perusahaan?')">
                            <span class="material-icons-round text-sm">warning</span>
                            Reset ke Default
                        </button>
                    </form>
                </div>

                <!-- INFO CARD -->
                <div class="bg-lavender/10 rounded-2xl p-6 border border-lavender/20 h-fit">
                    <h4 class="font-bold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                        <span class="material-icons-round">info</span>
                        Informasi
                    </h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Profil perusahaan akan digunakan pada header laporan, export data absensi, dan dokumen resmi lainnya.
                    </p>
                </div>
            </div>
        </div>
