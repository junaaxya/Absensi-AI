@extends('layouts.admin')

@section('header-title', 'Pengaturan Perusahaan')
@section('header-subtitle', 'Override pengaturan khusus untuk ' . $company->name)

@section('content')

    <div class="max-w-3xl mx-auto">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white">Pengaturan: {{ $company->name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pengaturan ini akan menimpa pengaturan global sistem untuk perusahaan ini.</p>
            </div>
            <a href="{{ route('admin.companies.show', $company) }}"
                class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-icons-round">arrow_back</span>
            </a>
        </div>

        <form action="{{ url('/admin/companies/' . $company->id . '/settings') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">schedule</span> Jam Kerja
                </h4>
                <p class="text-xs text-slate-500 mb-4">Kosongkan untuk menggunakan pengaturan global.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam Masuk</label>
                        <input type="time" name="work_start" value="{{ $settings['work_start'] ?? '' }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jam Pulang</label>
                        <input type="time" name="work_end" value="{{ $settings['work_end'] ?? '' }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Toleransi Keterlambatan (menit)</label>
                        <input type="number" name="late_tolerance" value="{{ $settings['late_tolerance'] ?? '' }}" min="0" max="120"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                            placeholder="Gunakan global" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">location_on</span> Lokasi & Geofence
                </h4>
                <p class="text-xs text-slate-500 mb-4">Lokasi kantor utama perusahaan ini. Kosongkan untuk menggunakan pengaturan global.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Latitude</label>
                        <input type="number" name="office_latitude" value="{{ $settings['office_latitude'] ?? '' }}" step="0.0000001"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                            placeholder="Gunakan global" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Longitude</label>
                        <input type="number" name="office_longitude" value="{{ $settings['office_longitude'] ?? '' }}" step="0.0000001"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                            placeholder="Gunakan global" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Radius Geofence (meter)</label>
                        <input type="number" name="office_radius" value="{{ $settings['office_radius'] ?? '' }}" min="10" max="10000"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                            placeholder="Gunakan global" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.companies.show', $company) }}"
                    class="px-6 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 dark:shadow-none active:scale-95">
                    Simpan Pengaturan
                </button>
            </div>

        </form>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg font-bold text-sm flex items-center gap-2">
            <span class="material-icons-round text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

@endsection
