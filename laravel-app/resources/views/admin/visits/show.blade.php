@extends('layouts.admin')

@section('header-title', 'Detail Kunjungan')
@section('header-subtitle', $visitAttendance->client_name)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.visits.index') }}"
            class="flex items-center gap-1 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
            <span class="material-icons-round text-base">arrow_back</span>
            Kembali
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Detail Kunjungan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $visitAttendance->user->name ?? '-' }} — {{ $visitAttendance->tanggal->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sky-500">info</span>
                    Informasi Kunjungan
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Karyawan</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $visitAttendance->user->name ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</p>
                        <div class="mt-1">
                            @if($visitAttendance->status === 'active')
                                <span class="inline-flex items-center rounded-full bg-sky-100 dark:bg-sky-900/30 px-3 py-1 text-xs font-bold text-sky-700 dark:text-sky-400">Aktif</span>
                            @elseif($visitAttendance->status === 'completed')
                                <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400">Selesai</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-rose-100 dark:bg-rose-900/30 px-3 py-1 text-xs font-bold text-rose-700 dark:text-rose-400">Dibatalkan</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Client</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $visitAttendance->client_name }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Lokasi</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $visitAttendance->location_name }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Check In</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $visitAttendance->check_in_time->format('H:i') }} WIB</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Check Out</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $visitAttendance->check_out_time ? $visitAttendance->check_out_time->format('H:i') . ' WIB' : '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-icons-round text-lavender">description</span>
                    Tujuan Kunjungan
                </h2>
                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $visitAttendance->purpose }}</p>
            </div>

            @if($visitAttendance->anomaly_score > 0)
            <div class="rounded-2xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4">
                <div class="flex items-center gap-2">
                    <span class="material-icons-round text-amber-600">warning</span>
                    <p class="text-sm font-bold text-amber-800 dark:text-amber-400">Anomaly Score: {{ $visitAttendance->anomaly_score }}</p>
                </div>
            </div>
            @endif

            @if($visitAttendance->visitLocations->count() > 0)
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-icons-round text-emerald-500">timeline</span>
                    GPS Trail ({{ $visitAttendance->visitLocations->count() }} titik)
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Latitude</th>
                                <th class="px-4 py-3">Longitude</th>
                                <th class="px-4 py-3">Akurasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($visitAttendance->visitLocations as $loc)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-4 py-3 text-slate-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $loc->recorded_at->format('H:i:s') }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ number_format($loc->latitude, 7) }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ number_format($loc->longitude, 7) }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ number_format($loc->accuracy, 1) }}m</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-card-dark shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-emerald-500">map</span>
                        Peta Lokasi
                    </h2>
                </div>
                <div id="visit-map" style="height: 450px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkInLat = {{ $visitAttendance->check_in_lat }};
        const checkInLng = {{ $visitAttendance->check_in_long }};
        const checkOutLat = {{ $visitAttendance->check_out_lat ?? 'null' }};
        const checkOutLng = {{ $visitAttendance->check_out_long ?? 'null' }};

        const map = L.map('visit-map').setView([checkInLat, checkInLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        const greenIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        const redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        L.marker([checkInLat, checkInLng], { icon: greenIcon })
            .addTo(map)
            .bindPopup('<strong>Check In</strong><br>{{ $visitAttendance->check_in_time->format("H:i") }} WIB')
            .openPopup();

        const bounds = [[checkInLat, checkInLng]];

        if (checkOutLat !== null && checkOutLng !== null) {
            L.marker([checkOutLat, checkOutLng], { icon: redIcon })
                .addTo(map)
                .bindPopup('<strong>Check Out</strong><br>{{ $visitAttendance->check_out_time ? $visitAttendance->check_out_time->format("H:i") : "-" }} WIB');
            bounds.push([checkOutLat, checkOutLng]);
        }

        @if($visitAttendance->visitLocations->count() > 0)
        const trailCoords = [
            [checkInLat, checkInLng],
            @foreach($visitAttendance->visitLocations as $loc)
            [{{ $loc->latitude }}, {{ $loc->longitude }}],
            @endforeach
            @if($visitAttendance->check_out_lat && $visitAttendance->check_out_long)
            [checkOutLat, checkOutLng],
            @endif
        ];

        L.polyline(trailCoords, {
            color: '#3B82F6',
            weight: 3,
            opacity: 0.7,
            dashArray: '8, 6'
        }).addTo(map);

        trailCoords.forEach(function(coord) {
            bounds.push(coord);
        });
        @endif

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    });
</script>
@endpush
