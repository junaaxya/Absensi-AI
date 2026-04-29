        <!-- CONTENT: EXPORT & LAPORAN -->
        <div x-show="activeTab === 'export'" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- EXPORT ATTENDANCE -->
                <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800"
                    x-data="{
                        startDate: '{{ now()->startOfMonth()->format('Y-m-d') }}',
                        endDate: '{{ now()->endOfMonth()->format('Y-m-d') }}',
                        departmentId: ''
                    }">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Laporan Absensi</h3>
                        <div class="w-10 h-10 rounded-full bg-sage/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                            <span class="material-icons-round">table_view</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Dari Tanggal</label>
                                <input type="date" x-model="startDate"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Sampai Tanggal</label>
                                <input type="date" x-model="endDate"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Departemen (Opsional)</label>
                            <select x-model="departmentId" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold">
                                <option value="">Semua Departemen</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-4 grid grid-cols-2 gap-3">
                            {{-- CSV Download --}}
                            <a :href="`{{ route('admin.export.attendance') }}?start_date=${startDate}&end_date=${endDate}&department_id=${departmentId}`"
                                class="py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                                <span class="material-icons-round text-lg">table_chart</span>
                                Download CSV
                            </a>

                            {{-- PDF Download --}}
                            <a :href="`{{ route('admin.export.attendance-pdf') }}?start_date=${startDate}&end_date=${endDate}&department_id=${departmentId}`"
                                class="py-3 bg-rose-600 dark:bg-rose-500 text-white rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                                <span class="material-icons-round text-lg">picture_as_pdf</span>
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- EXPORT EMPLOYEES -->
                <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 h-fit">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Data Karyawan</h3>
                        <div class="w-10 h-10 rounded-full bg-sky/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                            <span class="material-icons-round">people</span>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                        Export data lengkap seluruh karyawan termasuk informasi jabatan, departemen, dan status kepegawaian dalam format CSV/Excel.
                    </p>

                    <form action="{{ route('admin.export.employees') }}" method="GET">
                        <button type="submit"
                            class="w-full py-3 bg-sky/20 text-sky-700 dark:text-sky-300 rounded-xl font-bold hover:bg-sky/30 transition-all flex items-center justify-center gap-2">
                            <span class="material-icons-round">download</span>
                            Download Data Karyawan
                        </button>
                    </form>
                </div>
            </div>
        </div>
