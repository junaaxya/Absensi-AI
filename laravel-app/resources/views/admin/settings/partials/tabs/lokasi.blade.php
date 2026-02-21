<!-- CONTENT: LOKASI KANTOR -->
<div x-show="activeTab === 'lokasi'" style="display: none;" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

    <!-- Leaflet Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800"
        x-data="{
            lat: '{{ $settings->office_latitude ?? '-6.2088' }}',
            lng: '{{ $settings->office_longitude ?? '106.8456' }}',
            radius: '{{ $settings->office_radius ?? '0.1' }}',
            address: 'Memuat alamat...',
            locating: false,
            locError: '',
            map: null,
            marker: null,
            circle: null,
            geocodeTimeout: null,
            copied: false,
            
            init() {
                this.$watch('activeTab', val => {
                    if (val === 'lokasi') {
                        if (!this.map) {
                            setTimeout(() => this.initMap(), 100);
                        } else {
                            setTimeout(() => this.map.invalidateSize(), 100);
                        }
                    }
                });
                
                this.$watch('radius', val => {
                    if (this.circle) {
                        this.circle.setRadius(val * 1000);
                    }
                });
                
                if (this.activeTab === 'lokasi') {
                    setTimeout(() => this.initMap(), 100);
                }
            },
            
            initMap() {
                if (this.map) return;
                
                this.map = L.map(this.$refs.mapContainer).setView([this.lat, this.lng], 15);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a>'
                }).addTo(this.map);
                
                this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
                
                this.circle = L.circle([this.lat, this.lng], {
                    color: '#C8D5B9',
                    fillColor: '#C8D5B9',
                    fillOpacity: 0.4,
                    radius: this.radius * 1000
                }).addTo(this.map);
                
                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.updateLocation(pos.lat, pos.lng);
                });
                
                this.map.on('click', (e) => {
                    this.updateLocation(e.latlng.lat, e.latlng.lng);
                });
                
                this.reverseGeocode(this.lat, this.lng);
            },
            
            updateLocation(newLat, newLng) {
                this.lat = parseFloat(newLat).toFixed(7);
                this.lng = parseFloat(newLng).toFixed(7);
                
                const latlng = [this.lat, this.lng];
                this.marker.setLatLng(latlng);
                this.circle.setLatLng(latlng);
                this.map.panTo(latlng);
                
                this.reverseGeocode(this.lat, this.lng);
            },
            
            getLocation() {
                if (!navigator.geolocation) {
                    this.locError = 'Browser tidak mendukung geolokasi.';
                    return;
                }
                this.locating = true;
                this.locError = '';
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.updateLocation(pos.coords.latitude, pos.coords.longitude);
                        this.locating = false;
                    },
                    (err) => {
                        this.locating = false;
                        if (err.code === 1) this.locError = 'Izin lokasi ditolak. Aktifkan izin lokasi di browser Anda.';
                        else if (err.code === 2) this.locError = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
                        else this.locError = 'Gagal mendapatkan lokasi. Coba lagi.';
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            },
            
            reverseGeocode(lat, lng) {
                this.address = 'Mencari alamat...';
                clearTimeout(this.geocodeTimeout);
                
                this.geocodeTimeout = setTimeout(() => {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                        .then(res => res.json())
                        .then(data => {
                            this.address = data.display_name || 'Alamat tidak ditemukan';
                        })
                        .catch(() => {
                            this.address = 'Gagal memuat alamat';
                        });
                }, 800);
            },
            
            copyCoordinates() {
                navigator.clipboard.writeText(`${this.lat}, ${this.lng}`);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        }">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Lokasi Kantor</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Titik koordinat untuk validasi radius absensi.</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-peach/20 flex items-center justify-center text-slate-700 dark:text-slate-300">
                <span class="material-icons-round">location_on</span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-sage/20 border border-sage/30 rounded-xl flex items-center gap-3 text-sage-700 dark:text-sage-300">
                <span class="material-icons-round">check_circle</span>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.settings.location.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Left Column: Map -->
                <div class="lg:col-span-3 space-y-4">
                    <div class="relative w-full h-[350px] md:h-[450px] rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 shadow-inner z-0">
                        <div x-ref="mapContainer" class="w-full h-full z-0"></div>
                    </div>

                    <!-- Address Display -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-100 dark:border-slate-700/50 flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-full bg-sky/20 flex items-center justify-center text-sky-600 dark:text-sky-400 shrink-0 mt-1">
                            <span class="material-icons-round">place</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Terdeteksi</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed" x-text="address"></p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- GPS Button -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50">
                        <button type="button" @click="getLocation()" :disabled="locating"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-sage/20 text-sage-700 dark:text-sage-300 hover:bg-sage/30 rounded-xl font-bold transition-all disabled:opacity-50 border border-sage/30">
                            <svg x-show="locating" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <span class="material-icons-round text-[20px]" x-show="!locating">my_location</span>
                            <span x-text="locating ? 'Mendapatkan lokasi...' : 'Gunakan Lokasi Saat Ini'"></span>
                        </button>
                        <p x-show="locError" x-text="locError" class="text-sm text-red-500 mt-3 font-medium text-center"></p>
                    </div>

                    <!-- Coordinates -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-slate-800 dark:text-slate-200">Koordinat</h4>
                            <button type="button" @click="copyCoordinates()" 
                                class="text-xs flex items-center gap-1 px-2 py-1 rounded-md bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                                <span class="material-icons-round text-[14px]" x-text="copied ? 'check' : 'content_copy'"></span>
                                <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Latitude</label>
                                <input type="text" name="office_latitude" x-model="lat" readonly
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-mono text-sm cursor-not-allowed opacity-80" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Longitude</label>
                                <input type="text" name="office_longitude" x-model="lng" readonly
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-mono text-sm cursor-not-allowed opacity-80" />
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-1">
                            <span class="material-icons-round text-[14px]">info</span>
                            Geser marker pada peta untuk mengubah koordinat.
                        </p>
                    </div>

                    <!-- Radius -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50">
                        <h4 class="font-bold text-slate-800 dark:text-slate-200 mb-4">Pengaturan Radius</h4>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Radius Maksimal (Kilometer)</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0.01" name="office_radius" x-model="radius"
                                    class="w-full pl-4 pr-12 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <span class="text-slate-400 font-medium text-sm">KM</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                                Jarak maksimal karyawan diizinkan absen dari titik kantor. Lingkaran hijau pada peta menunjukkan batas radius.
                            </p>
                        </div>
                    </div>

                    <!-- Toleransi Akurasi GPS -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50">
                        <h4 class="font-bold text-slate-800 dark:text-slate-200 mb-4">Toleransi Akurasi GPS</h4>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Batas Akurasi (Meter)</label>
                            <div class="relative">
                                <input type="number" step="1" min="10" name="office_gps_tolerance" value="{{ $settings->office_gps_tolerance ?? 150 }}"
                                    class="w-full pl-4 pr-16 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-bold" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <span class="text-slate-400 font-medium text-sm">Meter</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                                Batas akurasi GPS yang diizinkan untuk absensi. Jika akurasi GPS perangkat melebihi nilai ini (sinyal lemah/menggunakan IP provider), absensi akan ditolak. Rekomendasi: 100-200 meter.
                            </p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                            <span class="material-icons-round">save</span>
                            Simpan Lokasi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
