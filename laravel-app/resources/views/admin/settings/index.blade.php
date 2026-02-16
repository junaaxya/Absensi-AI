@extends('layouts.admin')

@section('header-title', 'Pengaturan Sistem')
@section('header-subtitle', 'Konfigurasi Jam Kerja dan Lokasi Kantor')

@section('content')

    <div x-data="{ activeTab: 'jam_kerja' }">
        <!-- TABS -->
        <div class="mb-6 flex gap-2 overflow-x-auto pb-2">
            <button @click="activeTab = 'jam_kerja'"
                :class="activeTab === 'jam_kerja' ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-600 hover:bg-slate-50'"
                class="px-6 py-2.5 rounded-xl font-bold text-sm whitespace-nowrap transition-all">
                Jam Kerja
            </button>
            <button @click="activeTab = 'lokasi'"
                :class="activeTab === 'lokasi' ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-600 hover:bg-slate-50'"
                class="px-6 py-2.5 rounded-xl font-bold text-sm whitespace-nowrap transition-all">
                Lokasi Kantor
            </button>
        </div>

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
    </div>

@endsection