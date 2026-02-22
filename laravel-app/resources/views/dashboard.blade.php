@extends('layouts.employee-dashboard')

@push('styles')
    <style>
        .camera-container {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            border-radius: 1rem;
            overflow: hidden;
            background-color: #000;
            position: relative;
            aspect-ratio: 4/3;
        }

        #camera-feed {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .face-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            height: 70%;
            border: 2px dashed rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.3);
            pointer-events: none;
            z-index: 10;
        }

        .camera-overlay-text {
            position: absolute;
            top: 1rem;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            z-index: 20;
        }
    </style>
@endpush

@section('content')
    <div x-data="{}" class="space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Selamat Datang, {{ explode(' ', $user->name)[0] }}</h2>
                <p class="text-slate-500 dark:text-slate-400">Silahkan isi kegiatan anda hari ini.</p>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="document.documentElement.classList.toggle('dark')" class="w-10 h-10 rounded-full flex items-center justify-center bg-card-light dark:bg-card-dark border border-slate-200 dark:border-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-800">
                    <span class="material-symbols-outlined">dark_mode</span>
                </button>
            </div>
        </div>

        <!-- Announcements -->
        @if(isset($activeAnnouncements) && $activeAnnouncements->count() > 0)
            <div class="space-y-4">
                @foreach($activeAnnouncements as $announcement)
                    @php
                        $bgColor = match($announcement->type) {
                            'info' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-800/50 text-blue-800 dark:text-blue-300',
                            'warning' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-800/50 text-amber-800 dark:text-amber-300',
                            'danger' => 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800/50 text-red-800 dark:text-red-300',
                            default => 'bg-slate-50 dark:bg-slate-800/50 border-slate-100 dark:border-slate-700/50 text-slate-800 dark:text-slate-300',
                        };
                        $icon = match($announcement->type) {
                            'info' => 'info',
                            'warning' => 'warning',
                            'danger' => 'error',
                            default => 'campaign',
                        };
                        $iconColor = match($announcement->type) {
                            'info' => 'text-blue-500 dark:text-blue-400',
                            'warning' => 'text-amber-500 dark:text-amber-400',
                            'danger' => 'text-red-500 dark:text-red-400',
                            default => 'text-slate-500 dark:text-slate-400',
                        };
                    @endphp
                    <div class="{{ $bgColor }} border rounded-2xl p-4 flex items-start gap-3 shadow-sm">
                        <span class="material-symbols-outlined mt-0.5 {{ $iconColor }}">{{ $icon }}</span>
                        <div>
                            <h4 class="font-bold text-sm mb-1">{{ $announcement->title }}</h4>
                            <p class="text-xs opacity-90 leading-relaxed">{{ $announcement->content }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Kegiatan -->
        <section class="bg-card-light dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 transition-all">
            <div class="mb-2 flex items-center justify-between">
                <label class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Kegiatan Hari Ini</label>
                <span class="text-[11px] text-slate-400">Wajib diisi sebelum absen</span>
            </div>
            <textarea 
                id="kegiatan-input"
                class="w-full min-h-[140px] p-4 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 focus:ring-primary focus:border-primary transition-all text-sm placeholder:text-slate-400 text-slate-800 dark:text-slate-200" 
                placeholder="Tuliskan kegiatan yang anda lakukan hari ini..."
            >{{ $attendanceToday?->kegiatan && !in_array($attendanceToday->kegiatan, ['hadir', 'hadir_lembur']) ? $attendanceToday->kegiatan : '' }}</textarea>
            <p class="mt-2 text-[12px] text-slate-400 italic flex items-center gap-1">
                <span class="material-symbols-outlined !text-sm">info</span>
                Kegiatan ini akan tersimpan otomatis saat anda melakukan absensi.
            </p>
        </section>

        <!-- Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <button @click="startAttendance('masuk')" class="group flex items-center justify-center gap-3 bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 py-6 rounded-2xl font-bold text-lg transition-all border border-emerald-200/50 dark:border-emerald-500/20">
                <span class="material-symbols-outlined transition-transform group-hover:-translate-x-1">login</span>
                Absen Masuk
            </button>
            <button @click="startAttendance('pulang')" class="group flex items-center justify-center gap-3 bg-rose-100 hover:bg-rose-200 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 py-6 rounded-2xl font-bold text-lg transition-all border border-rose-200/50 dark:border-rose-500/20">
                <span class="material-symbols-outlined transition-transform group-hover:translate-x-1">logout</span>
                Absen Keluar
            </button>
        </div>

        <button onclick="window.location='{{ route('izin.index') }}'" class="flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors font-semibold text-sm">
            <span class="material-symbols-outlined">event_note</span>
            Pengajuan Ketidakhadiran
        </button>

        <!-- Table Section -->
        <section class="bg-card-light dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/20">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col lg:flex-row gap-4">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input name="q" value="{{ $search }}" class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 focus:ring-primary focus:border-primary text-slate-800 dark:text-slate-200" placeholder="Cari aktivitas..." type="text"/>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="flex items-center gap-2">
                            <input name="start_date" value="{{ $startDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 focus:ring-primary focus:border-primary text-slate-800 dark:text-slate-200" type="date"/>
                            <span class="text-slate-400">s/d</span>
                            <input name="end_date" value="{{ $endDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800/50 focus:ring-primary focus:border-primary text-slate-800 dark:text-slate-200" type="date"/>
                        </div>
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-xl text-sm font-semibold hover:bg-emerald-600 transition-colors shadow-sm shadow-emerald-500/20">
                            Filter
                        </button>
                        <a href="{{ route('dashboard') }}" class="px-6 py-2 text-slate-500 hover:text-slate-800 dark:hover:text-white text-sm font-semibold transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Tanggal</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Jam Masuk</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Jam Keluar</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Jam Kerja</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @forelse($attendanceHistory as $row)
                            @php
                                $durationLabel = '-';
                                if ($row->jam_masuk && $row->jam_keluar) {
                                    $minutes = \Carbon\Carbon::parse($row->jam_masuk)->diffInMinutes(\Carbon\Carbon::parse($row->jam_keluar));
                                    $hours = intdiv($minutes, 60);
                                    $remainingMinutes = $minutes % 60;
                                    $durationLabel = $remainingMinutes > 0 ? "{$hours}j {$remainingMinutes}m" : "{$hours}j";
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') . ' WIB' : '-' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $row->jam_keluar ? \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') . ' WIB' : '-' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $durationLabel }}</td>
                                <td class="px-6 py-4">
                                    @if($row->status === 'tepat_waktu')
                                        <span class="px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase">Tepat Waktu</span>
                                    @elseif($row->status === 'terlambat')
                                        <span class="px-2 py-1 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-[10px] font-bold uppercase">Terlambat</span>
                                    @elseif($row->status === 'pulang_cepat')
                                        <span class="px-2 py-1 rounded-full bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400 text-[10px] font-bold uppercase">Pulang Cepat</span>
                                    @elseif($row->status === 'lembur')
                                        <span class="px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 text-[10px] font-bold uppercase">Lembur</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full bg-slate-100 dark:bg-slate-500/20 text-slate-700 dark:text-slate-400 text-[10px] font-bold uppercase">{{ str_replace('_', ' ', $row->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $row->kegiatan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Belum ada riwayat absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($attendanceHistory->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-500">Menampilkan {{ $attendanceHistory->firstItem() }} sampai {{ $attendanceHistory->lastItem() }} dari {{ $attendanceHistory->total() }} riwayat</span>
                    <div class="flex gap-2">
                        @if ($attendanceHistory->onFirstPage())
                            <button disabled class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 opacity-50"><span class="material-symbols-outlined">chevron_left</span></button>
                        @else
                            <a href="{{ $attendanceHistory->previousPageUrl() }}" class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800"><span class="material-symbols-outlined">chevron_left</span></a>
                        @endif

                        @if ($attendanceHistory->hasMorePages())
                            <a href="{{ $attendanceHistory->nextPageUrl() }}" class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800"><span class="material-symbols-outlined">chevron_right</span></a>
                        @else
                            <button disabled class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 opacity-50"><span class="material-symbols-outlined">chevron_right</span></button>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </div>

    <!-- Modal Camera Absensi (Reused functionality) -->
    <div x-data="cameraModal"
        @open-modal.window="if ($event.detail === 'camera-modal') { show = true }"
        @close-modal.window="if ($event.detail === 'camera-modal') { show = false; stopCamera(); }"
        @open-camera.window="initCamera($event.detail.type)"
        x-show="show" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/80 p-4 backdrop-blur-sm">
        
        <div x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.outside="$dispatch('close-modal', 'camera-modal')"
            class="relative w-full max-w-2xl transform overflow-hidden rounded-3xl bg-card-light dark:bg-card-dark text-left shadow-xl transition-all border border-slate-200 dark:border-slate-700">
            
            <div class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" x-text="attendanceType === 'masuk' ? 'Absen Masuk' : 'Absen Keluar'"></h3>
                <button @click="$dispatch('close-modal', 'camera-modal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6">
                <!-- Step 1: Loading/Camera -->
                <div x-show="step === 'capture'" class="space-y-6">
                    <div x-show="loading" class="flex flex-col items-center justify-center py-12">
                        <svg class="h-10 w-10 animate-spin text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-4 text-sm font-medium text-slate-500 dark:text-slate-400" x-text="loadingText"></p>
                    </div>

                    <div x-show="errorMessage" class="rounded-xl border border-red-200 bg-red-50 p-4 dark:bg-red-900/20 dark:border-red-800/50">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-600 dark:text-red-400">error</span>
                            <div>
                                <h3 class="text-sm font-bold text-red-800 dark:text-red-300">Akses Kamera Gagal</h3>
                                <p class="mt-1 text-sm text-red-700 dark:text-red-400" x-text="errorMessage"></p>
                                <button @click="retryCapture" class="mt-3 rounded-lg bg-red-100 dark:bg-red-800/50 px-4 py-2 text-xs font-bold text-red-800 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800">Coba Lagi</button>
                            </div>
                        </div>
                    </div>

                    <div x-show="!loading && !errorMessage" class="relative">
                        <div class="camera-container bg-slate-100 dark:bg-slate-900">
                            <video id="camera-feed" autoplay playsinline></video>
                            <div class="face-overlay"></div>
                            <p class="camera-overlay-text text-sm">Posisikan wajah Anda dalam area lingkaran</p>
                        </div>
                        <div class="mt-4 flex justify-center">
                            <button @click="takeSnapshot" class="flex items-center gap-2 rounded-full bg-primary px-8 py-3 font-bold text-white shadow-lg hover:bg-emerald-600 transition-colors">
                                <span class="material-symbols-outlined">photo_camera</span>
                                Ambil Foto
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Processing/Result -->
                <div x-show="step === 'result'" class="space-y-6">
                    <div x-show="loading" class="flex flex-col items-center justify-center py-12">
                        <svg class="h-10 w-10 animate-spin text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-4 text-sm font-medium text-slate-500 dark:text-slate-400" x-text="loadingText"></p>
                    </div>

                    <div x-show="!loading">
                        <template x-if="attendanceResult && attendanceResult.success">
                            <div class="flex flex-col items-center py-6 text-center">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 shadow-inner">
                                    <span class="material-symbols-outlined !text-4xl">check_circle</span>
                                </div>
                                <h3 class="mb-2 text-2xl font-bold text-slate-800 dark:text-white">Absensi Berhasil!</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6" x-text="attendanceResult.message"></p>
                                
                                <div class="w-full rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 p-4 text-left">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold">Waktu</p>
                                            <p class="font-medium text-slate-900 dark:text-white" x-text="capturedAt"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold">Status</p>
                                            <p class="font-medium text-emerald-600 dark:text-emerald-400" x-text="resultStatusLabel()"></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <button @click="closeAndRefresh" class="mt-8 w-full rounded-xl bg-slate-900 dark:bg-white py-3 text-sm font-bold text-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors">
                                    Tutup & Refresh Halaman
                                </button>
                            </div>
                        </template>

                        <template x-if="attendanceResult && !attendanceResult.success">
                            <div class="flex flex-col items-center py-6 text-center">
                                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 shadow-inner">
                                    <span class="material-symbols-outlined !text-4xl">cancel</span>
                                </div>
                                <h3 class="mb-2 text-2xl font-bold text-slate-800 dark:text-white">Absensi Gagal</h3>
                                <p class="text-sm text-red-600 dark:text-red-400 mb-6" x-text="attendanceResult.message"></p>
                                
                                <div class="flex w-full gap-3 mt-4">
                                    <button @click="$dispatch('close-modal', 'camera-modal')" class="flex-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-3 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                        Batal
                                    </button>
                                    <button @click="retryCapture" class="flex-1 rounded-xl bg-primary py-3 text-sm font-bold text-white hover:bg-emerald-600">
                                        Coba Lagi
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cameraModal', () => ({
                show: false,
                step: 'capture',
                loading: false,
                loadingText: '',
                errorMessage: '',
                stream: null,
                attendanceType: 'masuk',
                capturedBlob: null,
                capturedPreview: '',
                kegiatanText: '',
                attendanceResult: null,
                capturedAt: '',
                locationStatus: '',

                init() {
                    this.$watch('show', value => {
                        if (!value) {
                            this.resetState(true);
                        }
                    });
                },

                async initCamera(type) {
                    this.resetState(false);
                    this.attendanceType = type;
                    this.step = 'capture';
                    this.loading = true;
                    this.loadingText = 'Menyiapkan kamera...';
                    
                    const kegiatanInput = document.getElementById('kegiatan-input');
                    this.kegiatanText = kegiatanInput ? kegiatanInput.value.trim() : '';

                    if (this.attendanceType === 'masuk' && !this.kegiatanText) {
                        this.loading = false;
                        this.errorMessage = 'Silahkan isi kolom Kegiatan Hari Ini terlebih dahulu sebelum melakukan absen masuk.';
                        return;
                    }

                    try {
                        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                            this.stream = await navigator.mediaDevices.getUserMedia({ 
                                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
                            });
                            
                            setTimeout(() => {
                                const videoEl = document.getElementById('camera-feed');
                                if (videoEl) {
                                    videoEl.srcObject = this.stream;
                                    videoEl.onloadedmetadata = () => {
                                        videoEl.play();
                                        this.loading = false;
                                    };
                                }
                            }, 100);
                        } else {
                            throw new Error('Browser tidak mendukung akses kamera.');
                        }
                    } catch (error) {
                        console.error('Camera error:', error);
                        this.loading = false;
                        this.errorMessage = error.name === 'NotAllowedError' 
                            ? 'Izin kamera ditolak. Silahkan izinkan akses kamera di browser Anda.' 
                            : 'Gagal mengakses kamera. Pastikan perangkat Anda memiliki kamera yang berfungsi.';
                    }
                },

                takeSnapshot() {
                    const video = document.getElementById('camera-feed');
                    if (!video) return;

                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    
                    // Mirror image if facing user
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob((blob) => {
                        this.capturedBlob = blob;
                        this.capturedPreview = URL.createObjectURL(blob);
                        this.stopCamera();
                        this.submitAttendance();
                    }, 'image/jpeg', 0.85);
                },

                async submitAttendance() {
                    this.step = 'result';
                    this.loading = true;
                    this.loadingText = 'Memverifikasi wajah dan mencatat presensi...';
                    
                    const formData = new FormData();
                    formData.append('photo', this.capturedBlob, 'attendance.jpg');
                    formData.append('type', this.attendanceType);
                    
                    if (this.attendanceType === 'masuk') {
                        formData.append('kegiatan', this.kegiatanText);
                    }

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        
                        const response = await fetch('/api/attendance/auto', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                ...(csrfToken ? {'X-CSRF-TOKEN': csrfToken} : {})
                            }
                        });

                        const result = await response.json();
                        
                        if (response.ok) {
                            this.attendanceResult = {
                                success: true,
                                message: result.message || 'Presensi berhasil dicatat.',
                                data: result.data || {}
                            };
                            
                            const now = new Date();
                            this.capturedAt = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
                            
                            // Emit global event to trigger toast (optional, handled by API anyway but good for UX)
                            window.dispatchEvent(new CustomEvent('attendance-success', { detail: result }));
                        } else {
                            throw new Error(result.message || 'Gagal memverifikasi wajah.');
                        }
                    } catch (error) {
                        this.attendanceResult = {
                            success: false,
                            message: error.message || 'Terjadi kesalahan jaringan atau server tidak merespon.',
                            data: {}
                        };
                    } finally {
                        this.loading = false;
                    }
                },

                resultStatusLabel() {
                    if (!this.attendanceResult) return '-';
                    if (!this.attendanceResult.success) return 'Gagal diverifikasi';

                    if (this.attendanceType === 'pulang') {
                        const pulang = this.attendanceResult.data?.status_pulang;
                        if (pulang === 'lembur') return 'Lembur';
                        if (pulang === 'pulang_cepat') return 'Pulang Cepat';
                        return 'Pulang tercatat';
                    }

                    const masuk = this.attendanceResult.data?.status_masuk;
                    if (masuk === 'tepat_waktu') return 'Tepat waktu';
                    if (masuk === 'terlambat') return 'Terlambat';
                    return 'Berhasil tercatat';
                },

                closeAndRefresh() {
                    this.$dispatch('close-modal', 'camera-modal');
                    setTimeout(() => { window.location.reload(); }, 300);
                },

                async retryCapture() {
                    this.attendanceResult = null;
                    await this.initCamera(this.attendanceType);
                },

                resetState(stopStream = true) {
                    this.loading = false;
                    this.errorMessage = '';
                    this.step = 'capture';
                    this.capturedBlob = null;
                    if (this.capturedPreview) { URL.revokeObjectURL(this.capturedPreview); }
                    this.capturedPreview = '';
                    this.attendanceResult = null;
                    if (stopStream) { this.stopCamera(); }
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                }
            }));
        });

        function startAttendance(type) {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'camera-modal' }));
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-camera', { detail: { type: type } }));
            }, 100);
        }
    </script>
@endpush
