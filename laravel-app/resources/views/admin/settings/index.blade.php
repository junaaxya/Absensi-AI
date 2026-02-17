@extends('layouts.admin')

@section('header-title', 'Pengaturan Sistem')
@section('header-subtitle', 'Konfigurasi Jam Kerja, Lokasi, Profil Perusahaan, dan Kebijakan')

@section('content')

    @php
        $tabLabels = [
            'jam_kerja' => 'Jam Kerja',
            'lokasi' => 'Lokasi Kantor',
            'profil_perusahaan' => 'Profil Perusahaan',
            'kebijakan_absensi' => 'Kebijakan Absensi',
            'face_recognition' => 'Face Recognition',
            'departemen' => 'Departemen',
            'shift_kerja' => 'Shift Kerja',
            'hari_libur' => 'Hari Libur',
            'tipe_cuti' => 'Tipe Cuti',
            'notifikasi' => 'Notifikasi',
            'export' => 'Export & Laporan',
            'backup' => 'Backup & Maintenance',
        ];
    @endphp

    <div x-data="{
        defaultTab: @js($defaultTab),
        activeTab: @js($defaultTab),
        allowedTabs: @js($allowedTabs),
        setTab(tab) {
            if (!this.allowedTabs.includes(tab)) {
                return;
            }
            this.activeTab = tab;
            if (window.location.hash !== '#' + tab) {
                history.replaceState(null, '', '#' + tab);
            }
        },
        syncTabFromHash() {
            const tabFromHash = window.location.hash.replace('#', '');
            if (this.allowedTabs.includes(tabFromHash)) {
                this.activeTab = tabFromHash;
                return;
            }
            this.activeTab = this.defaultTab;
        },
        init() {
            this.syncTabFromHash();
            window.addEventListener('hashchange', () => this.syncTabFromHash());
        }
    }" x-init="init()">
        <div class="mb-6 bg-white dark:bg-card-dark rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-start justify-between gap-3 flex-wrap mb-4">
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Navigasi Pengaturan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pilih kategori lalu atur menu yang tersedia di dalamnya.</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('admin.settings.category', ['category' => 'umum']) }}"
                        class="px-3 py-2 rounded-lg text-xs font-bold transition-all {{ $activeCategory === 'umum' ? 'bg-slate-900 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700' }}">Konfigurasi Umum</a>
                    <a href="{{ route('admin.settings.category', ['category' => 'kehadiran']) }}"
                        class="px-3 py-2 rounded-lg text-xs font-bold transition-all {{ $activeCategory === 'kehadiran' ? 'bg-slate-900 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700' }}">Kehadiran & Cuti</a>
                    <a href="{{ route('admin.settings.category', ['category' => 'organisasi']) }}"
                        class="px-3 py-2 rounded-lg text-xs font-bold transition-all {{ $activeCategory === 'organisasi' ? 'bg-slate-900 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700' }}">Organisasi</a>
                    <a href="{{ route('admin.settings.category', ['category' => 'data']) }}"
                        class="px-3 py-2 rounded-lg text-xs font-bold transition-all {{ $activeCategory === 'data' ? 'bg-slate-900 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700' }}">Integrasi & Data</a>
                </div>
            </div>

            <div class="rounded-xl border border-slate-100 dark:border-slate-700 p-3 bg-slate-50/70 dark:bg-slate-800/50">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-2">Menu Dalam Kategori</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($allowedTabs as $tab)
                        <button @click="setTab('{{ $tab }}')"
                            class="px-2.5 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold hover:bg-slate-100 dark:hover:bg-slate-600 transition-all">
                            {{ $tabLabels[$tab] ?? $tab }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- TABS -->
        <div class="mb-6 flex gap-2 overflow-x-auto pb-2">
            @foreach ($allowedTabs as $tab)
                <button @click="setTab('{{ $tab }}')"
                    :class="activeTab === '{{ $tab }}' ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white text-slate-600 hover:bg-slate-50'"
                    class="px-6 py-2.5 rounded-xl font-bold text-sm whitespace-nowrap transition-all">
                    {{ $tabLabels[$tab] ?? $tab }}
                </button>
            @endforeach
        </div>

        @php
            $categoryPartials = [
                'umum' => 'admin.settings.partials.umum',
                'kehadiran' => 'admin.settings.partials.kehadiran',
                'organisasi' => 'admin.settings.partials.organisasi',
                'data' => 'admin.settings.partials.data',
            ];
            $activeCategoryPartial = $categoryPartials[$activeCategory] ?? 'admin.settings.partials.umum';
        @endphp

        @include($activeCategoryPartial)

    </div>

@endsection
