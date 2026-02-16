@extends('layouts.absensi')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        .employee-dashboard {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-attendance {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
    </style>
@endpush

@section('content')
    <div x-data="{}" class="employee-dashboard space-y-6 pb-16">
        <header class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="relative z-10 flex flex-col gap-2">
                <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Employee Dashboard</p>
                <h1 class="text-2xl font-bold text-slate-900 md:text-3xl">Halo, {{ $user->name }} 👋</h1>
                <p class="text-sm text-slate-500 md:text-base">
                    Ringkasan absensi Anda hari ini, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}.
                </p>
            </div>
            <div class="pointer-events-none absolute -right-10 -top-14 h-40 w-40 rounded-full bg-emerald-200/30 blur-2xl"></div>
        </header>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-5">
                <h2 class="mb-5 flex items-center gap-2 text-lg font-bold text-slate-900">
                    <span class="material-icons-round text-emerald-600">history</span>
                    Aktivitas Hari Ini
                </h2>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Absensi Masuk</p>
                        @if($attendanceToday && $attendanceToday->jam_masuk)
                            <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-emerald-700">
                                <span class="material-icons-round text-base">check_circle</span>
                                {{ \Carbon\Carbon::parse($attendanceToday->jam_masuk)->format('H:i') }} WIB
                            </p>
                        @else
                            <p class="mt-2 flex items-center gap-2 text-sm italic text-slate-500">
                                <span class="material-icons-round text-base">warning_amber</span>
                                Absen masuk belum dilakukan
                            </p>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Makan Siang</p>
                        <p class="mt-2 text-sm italic text-slate-500">Belum dilakukan</p>
                    </div>

                    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Absen Keluar</p>
                        @if($attendanceToday && $attendanceToday->jam_keluar)
                            <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-sky-700">
                                <span class="material-icons-round text-base">check_circle</span>
                                {{ \Carbon\Carbon::parse($attendanceToday->jam_keluar)->format('H:i') }} WIB
                            </p>
                        @else
                            <p class="mt-2 flex items-center gap-2 text-sm text-sky-700">
                                <span class="material-icons-round text-base">hourglass_empty</span>
                                Belum dilakukan
                            </p>
                        @endif
                    </div>
                </div>
            </section>

            <section class="space-y-4 xl:col-span-7">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jam Masuk Kerja</p>
                        <p class="mt-2 text-xl font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($workStartTime)->format('H.i') }} WIB
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jam Pulang Kerja</p>
                        <p class="mt-2 text-xl font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($workEndTime)->format('H.i') }} WIB
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="mb-3 text-sm font-bold text-slate-900">Kegiatan Hari Ini</h2>
                    <textarea
                        class="h-32 w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-500"
                        placeholder="Tuliskan laporan singkat kegiatan hari ini..."
                        >{{ $attendanceToday?->kegiatan && !in_array($attendanceToday->kegiatan, ['hadir', 'hadir_lembur']) ? $attendanceToday->kegiatan : '' }}</textarea>
                    <p class="mt-2 text-xs text-slate-400">Kolom ini siap dipakai saat fitur simpan kegiatan diaktifkan.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <button
                        @click="startAttendance('masuk')"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-emerald-200 px-4 py-4 text-sm font-bold text-slate-800 transition hover:brightness-95">
                        <span class="material-icons-round">login</span>
                        Absen Masuk
                    </button>

                    <button
                        @click="startAttendance('pulang')"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-rose-200 px-4 py-4 text-sm font-bold text-slate-800 transition hover:brightness-95">
                        <span class="material-icons-round">logout</span>
                        Absen Keluar
                    </button>

                    <button
                        @click="$dispatch('open-modal', 'izin')"
                        class="md:col-span-2 flex items-center justify-center gap-2 rounded-2xl bg-violet-200 px-4 py-4 text-sm font-bold text-slate-800 transition hover:brightness-95">
                        <span class="material-icons-round">event_busy</span>
                        Pengajuan Ketidakhadiran
                    </button>
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 p-4 md:flex-row md:items-center md:justify-between md:p-6">
                <form method="GET" action="{{ route('dashboard') }}" class="flex w-full flex-col gap-3 md:flex-row md:items-center">
                    <div class="relative w-full md:max-w-sm">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input
                            name="q"
                            type="text"
                            value="{{ $search }}"
                            placeholder="Cari status, kegiatan, atau jam..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-4 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    </div>

                    <div class="flex w-full flex-col gap-2 sm:flex-row md:w-auto">
                        <input
                            name="start_date"
                            type="date"
                            value="{{ $startDate }}"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        <input
                            name="end_date"
                            type="date"
                            value="{{ $endDate }}"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    </div>

                    <div class="flex items-center gap-2 md:ml-auto">
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Filter
                        </button>
                        <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Jam Masuk</th>
                            <th class="px-6 py-4">Jam Keluar</th>
                            <th class="px-6 py-4">Jam Kerja</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
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
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-6 py-4">{{ ($attendanceHistory->firstItem() ?? 0) + $loop->index }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4">{{ $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') . ' WIB' : '-' }}</td>
                                <td class="px-6 py-4">{{ $row->jam_keluar ? \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') . ' WIB' : '-' }}</td>
                                <td class="px-6 py-4">{{ $durationLabel }}</td>
                                <td class="px-6 py-4">
                                    @if($row->status === 'tepat_waktu')
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Tepat Waktu</span>
                                    @elseif($row->status === 'terlambat')
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">Terlambat</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ ucfirst(str_replace('_', ' ', $row->status ?? '-')) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    @if($row->kegiatan === 'hadir_lembur')
                                        Lembur
                                    @elseif($row->kegiatan === 'hadir')
                                        Hadir
                                    @else
                                        {{ $row->kegiatan ?: '-' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center italic text-slate-500">Belum ada riwayat absensi pada filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 p-4 md:p-5">
                {{ $attendanceHistory->links() }}
            </div>
        </section>

        <x-pastel-modal name="izin" title="Pengajuan Ketidakhadiran" maxWidth="lg">
            <form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-medium text-text-secondary">Jenis Pengajuan</label>
                    <select name="jenis" required
                        class="w-full rounded-xl border border-neutral-stone bg-white px-4 py-2 transition focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20">
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                        <option value="dinas">Dinas</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-text-secondary">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required
                            class="w-full rounded-xl border border-neutral-stone bg-white px-4 py-2 transition focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-text-secondary">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required
                            class="w-full rounded-xl border border-neutral-stone bg-white px-4 py-2 transition focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-text-secondary">Alasan</label>
                    <textarea name="alasan" required rows="3"
                        class="w-full rounded-xl border border-neutral-stone bg-white px-4 py-2 transition focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20"></textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-text-secondary">Lampirkan Foto atau Dokumen</label>
                    <input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                        class="w-full text-sm text-text-secondary file:mr-4 file:rounded-xl file:border-0 file:bg-pastel-sage/20 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-pastel-sage-dark hover:file:bg-pastel-sage/30">
                    <p class="mt-1 text-xs text-text-secondary">Format: PDF, JPG, PNG, DOCX (Max 5MB)</p>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="show = false"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-text-secondary transition hover:bg-neutral-stone/30">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-pastel-sage px-4 py-2 text-sm font-medium text-text-primary shadow-soft transition hover:bg-pastel-sage-dark">
                        Ajukan
                    </button>
                </div>
            </form>
        </x-pastel-modal>
    </div>

    <x-pastel-modal name="camera-modal" title="Absensi" maxWidth="2xl">
        <div x-data="cameraHandler()"
            @open-camera.window="initCamera($event.detail.type)"
            @modal-closed.window="if ($event.detail === 'camera-modal') resetState()"
            class="glass-attendance rounded-3xl p-2 md:p-4">
            <div class="mb-4 flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700">
                        <span class="material-icons-round">fingerprint</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900" x-text="attendanceType === 'pulang' ? 'Absen Pulang' : 'Absen Masuk'"></h3>
                        <p class="text-xs text-slate-500"
                            x-text="step === 'result' ? 'Konfirmasi hasil verifikasi absensi.' : 'Pastikan wajah terlihat jelas saat pengambilan foto.'"></p>
                    </div>
                </div>
                <div class="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 md:block"
                    x-text="new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'"></div>
            </div>

            <template x-if="step === 'capture'">
                <div>
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                        <div class="space-y-4">
                            <div class="relative aspect-square overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 bg-slate-100">
                                <video x-show="!capturedPreview" x-ref="video" class="h-full w-full scale-x-[-1] object-cover" autoplay playsinline muted></video>
                                <img x-show="capturedPreview" :src="capturedPreview" alt="Hasil foto absensi" class="h-full w-full object-cover" />
                                <canvas x-ref="canvas" class="hidden"></canvas>

                                <div x-show="!stream && !loading && !capturedPreview"
                                    class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-slate-500">
                                    <span class="material-icons-round text-4xl text-slate-400">photo_camera</span>
                                    <p class="text-sm font-medium">Kamera belum menyala</p>
                                    <p class="text-xs">Izinkan akses kamera di browser Anda</p>
                                </div>

                                <div x-show="loading" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/50 text-white">
                                    <svg class="mb-2 h-8 w-8 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="loadingText" class="text-sm font-medium"></span>
                                </div>

                                <div x-show="errorMessage"
                                    class="absolute bottom-3 left-3 right-3 rounded-xl bg-red-500/90 px-3 py-2 text-center text-xs text-white"
                                    x-text="errorMessage"></div>
                            </div>

                            <button @click="takePicture" :disabled="loading || !stream"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-200 px-4 py-3.5 text-sm font-bold text-slate-800 transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                                <span class="material-icons-round">camera_alt</span>
                                Ambil Absensi
                            </button>
                        </div>

                        <div class="flex flex-col gap-4">
                            <div>
                                <label for="kegiatan-modal" class="mb-2 block text-sm font-semibold text-slate-700">Kegiatan Hari Ini</label>
                                <textarea id="kegiatan-modal" x-model="kegiatanText" rows="7"
                                    placeholder="Tuliskan rencana kegiatan atau target Anda hari ini..."
                                    class="w-full resize-none rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                                <p class="mt-1 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-400">Opsional</p>
                            </div>

                            <div class="flex items-start gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4">
                                <span class="material-icons-round text-sky-600">info</span>
                                <div class="text-sm">
                                    <p class="font-semibold text-slate-800">Informasi Penting</p>
                                    <p class="mt-1 text-slate-600">Pastikan wajah tidak tertutup masker atau objek lain saat mengambil absensi.</p>
                                </div>
                            </div>

                            <div class="mt-auto grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <button type="button" @click="$dispatch('close-modal', 'camera-modal')"
                                    class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                    Batal
                                </button>
                                <button type="button" @click="saveAttendance" :disabled="loading || !capturedBlob"
                                    class="flex items-center justify-center gap-2 rounded-2xl bg-sky-200 px-4 py-3 text-sm font-bold text-slate-800 transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                                    <span class="material-icons-round text-base">save</span>
                                    Simpan Kehadiran
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-3">
                            <span class="material-icons-round text-amber-600">location_on</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Lokasi</p>
                                <p class="text-xs font-semibold text-slate-700">{{ $officeName }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-violet-200 bg-violet-50 p-3">
                            <span class="material-icons-round text-violet-600">verified_user</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-violet-700">Verifikasi</p>
                                <p class="text-xs font-semibold text-slate-700">Biometric Face Recognition</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3">
                            <span class="material-icons-round text-emerald-600">history</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Status Hari Ini</p>
                                <p class="text-xs font-semibold text-slate-700"
                                    x-text="attendanceType === 'pulang' ? 'Proses Absen Pulang' : 'Belum Melakukan Absen Masuk'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="step === 'result'">
                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-100 p-4">
                        <div class="mx-auto w-full max-w-2xl rounded-xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-4 py-3 text-center text-lg font-bold text-slate-800"
                                x-text="attendanceType === 'pulang' ? 'Absen Pulang' : 'Absen Masuk'"></div>

                            <div class="space-y-4 p-4">
                                <div class="relative h-56 overflow-hidden rounded-lg bg-slate-700">
                                    <img x-show="capturedPreview" :src="capturedPreview" alt="Capture result" class="h-full w-full object-cover opacity-70" />
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="relative h-36 w-36 rounded-2xl border-4"
                                            :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'">
                                            <div class="absolute -left-1 -top-1 h-4 w-4 border-l-4 border-t-4"
                                                :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'"></div>
                                            <div class="absolute -right-1 -top-1 h-4 w-4 border-r-4 border-t-4"
                                                :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'"></div>
                                            <div class="absolute -bottom-1 -left-1 h-4 w-4 border-b-4 border-l-4"
                                                :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'"></div>
                                            <div class="absolute -bottom-1 -right-1 h-4 w-4 border-b-4 border-r-4"
                                                :class="attendanceResult?.success ? 'border-emerald-400' : 'border-red-400'"></div>
                                            <div class="absolute -top-10 left-1/2 -translate-x-1/2 rounded-full px-3 py-1 text-xs font-semibold text-white"
                                                :class="attendanceResult?.success ? 'bg-emerald-500' : 'bg-red-500'">
                                                <span x-text="(attendanceResult?.data?.user_name || '{{ $user->name }}') + (attendanceResult?.success ? ' - Dikenali' : ' - Tidak dikenali')"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 rounded-lg border bg-slate-100 px-4 py-3"
                                    :class="attendanceResult?.success ? 'border-emerald-200' : 'border-red-200'">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full border-2 border-slate-800 bg-white">
                                        <span class="material-icons-round text-lg" x-text="attendanceResult?.success ? 'check' : 'close'"></span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900" x-text="attendanceResult?.success ? 'Wajah dikenali' : 'Wajah tidak dikenali'"></p>
                                        <p class="text-sm text-slate-600">Nama: <span class="font-semibold" x-text="attendanceResult?.data?.user_name || '{{ $user->name }}'"></span></p>
                                    </div>
                                </div>

                                <div class="rounded-lg border border-slate-200 p-3 text-sm text-slate-700">
                                    <div class="flex items-center gap-2">
                                        <span class="material-icons-round text-base">schedule</span>
                                        <span x-text="capturedAt"></span>
                                    </div>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="material-icons-round text-base">location_on</span>
                                        <span x-text="locationStatus"></span>
                                    </div>
                                </div>

                                <div class="rounded-lg border border-slate-200 p-3">
                                    <div class="flex items-center gap-3 text-sm text-slate-700">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full border border-slate-800">
                                            <span class="material-icons-round text-sm">check</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold" x-text="attendanceType === 'pulang' ? 'Jam Pulang Tercatat' : 'Jam Masuk Tercatat'"></p>
                                            <p x-text="resultStatusLabel()"></p>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="attendanceResult?.success && attendanceType === 'masuk' && attendanceResult?.data?.status_masuk === 'terlambat'"
                                    class="flex items-start gap-2 rounded-lg border border-red-200 bg-white px-3 py-2 text-xs text-red-700">
                                    <span class="material-icons-round text-sm">warning</span>
                                    <p>Absen terlambat. Mohon tingkatkan ketepatan waktu.</p>
                                </div>

                                <div x-show="attendanceResult && !attendanceResult.success"
                                    class="flex items-start gap-2 rounded-lg border border-red-200 bg-white px-3 py-2 text-xs text-red-700">
                                    <span class="material-icons-round text-sm">error</span>
                                    <p x-text="attendanceResult?.message"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <button type="button" @click="closeAndRefresh" x-show="attendanceResult?.success"
                            class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 sm:col-span-2">
                            Kembali ke Dashboard
                        </button>
                        <button type="button" @click="retryCapture" x-show="attendanceResult && !attendanceResult.success"
                            class="rounded-2xl bg-sky-200 px-4 py-3 text-sm font-bold text-slate-800 transition hover:brightness-95">
                            Coba Lagi
                        </button>
                        <button type="button" @click="$dispatch('close-modal', 'camera-modal')" x-show="attendanceResult && !attendanceResult.success"
                            class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Tutup
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </x-pastel-modal>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cameraHandler', () => ({
                stream: null,
                loading: false,
                loadingText: 'Menyiapkan kamera...',
                errorMessage: '',
                step: 'capture',
                attendanceType: '',
                capturedBlob: null,
                capturedPreview: '',
                kegiatanText: '',
                attendanceResult: null,
                capturedAt: '',
                locationStatus: '',

                async initCamera(type) {
                    this.attendanceType = type;
                    this.resetState(false);
                    this.step = 'capture';
                    this.loading = true;
                    this.loadingText = 'Menyalakan kamera...';
                    this.errorMessage = '';

                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                        this.$refs.video.srcObject = this.stream;
                        this.loading = false;
                    } catch (err) {
                        this.loading = false;
                        this.errorMessage = 'Gagal akses kamera: ' + err.message;
                    }
                },

                takePicture() {
                    this.loading = true;
                    this.loadingText = 'Mengambil foto...';
                    this.errorMessage = '';

                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;

                    if (!video.videoWidth || !video.videoHeight) {
                        this.loading = false;
                        this.errorMessage = 'Kamera belum siap. Silakan coba lagi.';
                        return;
                    }

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob((blob) => {
                        if (!blob) {
                            this.loading = false;
                            this.errorMessage = 'Gagal mengambil gambar.';
                            return;
                        }

                        this.capturedBlob = blob;
                        if (this.capturedPreview) {
                            URL.revokeObjectURL(this.capturedPreview);
                        }
                        this.capturedPreview = URL.createObjectURL(blob);
                        this.loading = false;
                    }, 'image/jpeg', 0.8);
                },

                saveAttendance() {
                    if (!this.capturedBlob) {
                        this.errorMessage = 'Ambil foto absensi terlebih dahulu.';
                        return;
                    }

                    this.loading = true;
                    this.loadingText = 'Mengambil lokasi...';
                    this.errorMessage = '';

                        if (!navigator.geolocation) {
                            this.loading = false;
                            this.errorMessage = 'Browser tidak support Geolocation.';
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(
                            async (position) => {
                                this.capturedAt = new Date().toLocaleString('id-ID', {
                                    weekday: 'long',
                                    day: '2-digit',
                                    month: 'long',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) + ' WIB';
                                this.locationStatus = `Lokasi terekam (${position.coords.latitude.toFixed(5)}, ${position.coords.longitude.toFixed(5)})`;
                                await this.submitAttendance(this.capturedBlob, position.coords.latitude, position.coords.longitude);
                            },
                            (err) => {
                                this.loading = false;
                                this.errorMessage = 'Gagal dapat lokasi: ' + err.message;
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                },

                async submitAttendance(photoBlob, lat, long) {
                    this.loadingText = 'Memproses Absensi...';

                    const formData = new FormData();
                    formData.append('photo', photoBlob, 'selfie.jpg');
                    formData.append('type', this.attendanceType);
                    formData.append('latitude', lat);
                    formData.append('longitude', long);

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;

                        const response = await fetch('/api/attendance/auto', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                Accept: 'application/json'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        this.loading = false;
                        this.stopCamera();
                        this.step = 'result';
                        this.attendanceResult = {
                            success: response.ok,
                            message: result?.message || 'Terjadi kesalahan sistem.',
                            data: result?.data || {}
                        };

                        if (!response.ok) {
                            this.errorMessage = '';
                        }
                    } catch (error) {
                        this.loading = false;
                        this.stopCamera();
                        this.step = 'result';
                        this.attendanceResult = {
                            success: false,
                            message: error.message || 'Terjadi kesalahan jaringan.',
                            data: {}
                        };
                        this.errorMessage = '';
                    }
                },

                resultStatusLabel() {
                    if (!this.attendanceResult) {
                        return '-';
                    }

                    if (!this.attendanceResult.success) {
                        return 'Status: Gagal diverifikasi';
                    }

                    if (this.attendanceType === 'pulang') {
                        const pulang = this.attendanceResult.data?.status_pulang;
                        if (pulang === 'lembur') {
                            return 'Status: Lembur';
                        }

                        return 'Status: Pulang tercatat';
                    }

                    const masuk = this.attendanceResult.data?.status_masuk;
                    if (masuk === 'tepat_waktu') {
                        return 'Status: Tepat waktu';
                    }
                    if (masuk === 'terlambat') {
                        return 'Status: Terlambat';
                    }

                    return 'Status: Berhasil tercatat';
                },

                closeAndRefresh() {
                    this.$dispatch('close-modal', 'camera-modal');
                    setTimeout(() => {
                        window.location.reload();
                    }, 150);
                },

                async retryCapture() {
                    await this.initCamera(this.attendanceType || 'masuk');
                },

                resetState(stopStream = true) {
                    this.loading = false;
                    this.loadingText = 'Menyiapkan kamera...';
                    this.errorMessage = '';
                    this.step = 'capture';
                    this.capturedBlob = null;
                    if (this.capturedPreview) {
                        URL.revokeObjectURL(this.capturedPreview);
                    }
                    this.capturedPreview = '';
                    this.kegiatanText = '';
                    this.attendanceResult = null;
                    this.capturedAt = '';
                    this.locationStatus = '';
                    if (stopStream) {
                        this.stopCamera();
                    }
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
@endsection
