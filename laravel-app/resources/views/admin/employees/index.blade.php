@extends('layouts.absensi')

@section('content')
    <div x-data="{ 
                addModalOpen: false, 
                editModalOpen: false,
                faceModalOpen: false,
                deleteModalOpen: false,
                selectedEmployee: null,
                search: '{{ request('search') }}',

                // Face Management Data
                faceFiles: [],
                facePreviewUrls: [],

                openEdit(employee) {
                    this.selectedEmployee = employee;
                    $dispatch('open-modal', 'edit-employee-modal');
                },

                openDelete(employee) {
                    this.selectedEmployee = employee;
                    $dispatch('open-modal', 'delete-employee-modal');
                },

                openFace(employee) {
                    this.selectedEmployee = employee;
                    this.faceFiles = [];
                    this.facePreviewUrls = [];
                    $dispatch('open-modal', 'face-management-modal');
                },

                handleFaceFiles(event) {
                    const files = Array.from(event.target.files).slice(0, 6);
                    this.faceFiles = files;
                    this.facePreviewUrls = files.map(file => URL.createObjectURL(file));
                },

                async uploadFaces() {
                    if (this.faceFiles.length === 0) return alert('Pilih foto terlebih dahulu!');

                    const formData = new FormData();
                    formData.append('username', this.selectedEmployee.username); // Use username as key
                    this.faceFiles.forEach(file => formData.append('photos[]', file));

                    try {
                        const token = document.querySelector('meta[name=" csrf-token"]').content; const response=await
        fetch('/api/face/register', { method: 'POST' , headers: { 'X-CSRF-TOKEN' : token }, body: formData }); const
        result=await response.json(); if (response.ok) { window.location.href=window.location.pathname
        + '?status=success&message=' + encodeURIComponent('Data wajah berhasil didaftarkan'); } else {
        alert('Gagal: ' + (result.message || ' Error uploading faces')); } } catch (e) { console.error(e); alert('Terjadi
        kesalahan sistem'); } } }">

        <!-- HEADER -->
        <div
            class="bg-gradient-to-r from-pastel-sage/20 to-pastel-sky/20 p-6 rounded-2xl border border-neutral-stone/30 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-text-primary">Manajemen Karyawan</h1>
                <p class="text-text-secondary mt-1">Kelola data karyawan dan hak akses</p>
            </div>
            <div class="flex gap-3">
                <button @click="$dispatch('open-modal', 'settings-modal')"
                    class="px-4 py-2 bg-white text-text-primary font-bold rounded-xl transition shadow-soft border border-neutral-stone hover:bg-neutral-stone/10 flex items-center gap-2">
                    ⚙️ Pengaturan Kantor
                </button>
                <button @click="$dispatch('open-modal', 'add-employee-modal')"
                    class="px-4 py-2 bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary font-bold rounded-xl transition shadow-soft flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Karyawan
                </button>
            </div>
        </div>

        <!-- MAIN CARD -->
        <x-pastel-card>
            <!-- FILTER BAR -->
            <div class="flex flex-col md:flex-row gap-4 mb-6 justify-between items-center">
                <form method="GET" action="{{ route('employees.index') }}" class="w-full md:w-1/2 relative">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-text-secondary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau username..."
                        class="pl-10 w-full rounded-xl border-neutral-stone focus:border-pastel-sage focus:ring-pastel-sage/20 transition h-10">
                </form>

                <div class="flex gap-2">
                    <!-- Placeholder for future filters -->
                </div>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto -mx-6 px-6">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-neutral-stone">
                            <th
                                class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30 first:rounded-tl-lg">
                                No</th>
                            <th
                                class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30">
                                Nama</th>
                            <th
                                class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30">
                                Username</th>
                            <th
                                class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30">
                                Jabatan</th>
                            <th
                                class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30">
                                Role</th>
                            <th
                                class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30 text-center">
                                Status Wajah</th>
                            <th
                                class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30 text-center last:rounded-tr-lg">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-stone/30">
                        @forelse($employees as $index => $employee)
                            <tr class="hover:bg-neutral-warm/50 transition">
                                <td class="px-4 py-3 text-center">{{ $employees->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-text-primary">{{ $employee->name }}</td>
                                <td class="px-4 py-3 text-text-secondary">{{ $employee->username }}</td>
                                <td class="px-4 py-3 text-text-secondary">{{ $employee->jabatan ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-semibold {{ $employee->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($employee->role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($employee->has_face_data)
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">✓
                                            Terdaftar</span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">x Belum</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center flex justify-center gap-2">
                                    <button @click="openFace({{ $employee }})"
                                        class="p-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition"
                                        title="Kelola Wajah">
                                        👤
                                    </button>
                                    <button @click="openEdit({{ $employee }})"
                                        class="p-1.5 bg-amber-50 text-amber-600 rounded hover:bg-amber-100 transition"
                                        title="Edit">
                                        ✏️
                                    </button>
                                    <button @click="openDelete({{ $employee }})"
                                        class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-100 transition" title="Hapus">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-text-secondary italic">Tidak ada data karyawan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $employees->links() }}
            </div>
        </x-pastel-card>

        <!-- MODAL TAMBAH USER -->
        <x-pastel-modal name="add-employee-modal" title="Tambah Karyawan" maxWidth="lg">
            <form action="{{ route('employees.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="name" value="Nama Lengkap" />
                    <x-text-input id="name" name="name" class="block w-full mt-1" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="username" value="Username/NIP" />
                        <x-text-input id="username" name="username" class="block w-full mt-1" required />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input type="email" id="email" name="email" class="block w-full mt-1" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role"
                            class="block w-full mt-1 border-neutral-stone rounded-xl shadow-sm focus:border-pastel-sage focus:ring-pastel-sage/20">
                            <option value="karyawan">Karyawan</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="jabatan" value="Jabatan" />
                        <x-text-input id="jabatan" name="jabatan" class="block w-full mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input type="password" id="password" name="password" class="block w-full mt-1" required />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi" />
                        <x-text-input type="password" id="password_confirmation" name="password_confirmation"
                            class="block w-full mt-1" required />
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="$dispatch('close-modal', 'add-employee-modal')"
                        class="px-4 py-2 text-text-secondary hover:bg-neutral-stone/20 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary font-bold rounded-xl shadow-soft">Simpan</button>
                </div>
            </form>
        </x-pastel-modal>

        <!-- MODAL EDIT USER -->
        <x-pastel-modal name="edit-employee-modal" title="Edit Karyawan" maxWidth="lg">
            <form x-bind:action="'/admin/employees/' + (selectedEmployee ? selectedEmployee.id : '')" method="POST"
                class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <x-input-label value="Nama Lengkap" />
                    <x-text-input name="name" class="block w-full mt-1" x-model="selectedEmployee.name" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Username" />
                        <x-text-input name="username" class="block w-full mt-1" x-model="selectedEmployee.username"
                            required />
                    </div>
                    <div>
                        <x-input-label value="Email" />
                        <x-text-input type="email" name="email" class="block w-full mt-1" x-model="selectedEmployee.email"
                            required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Role" />
                        <select name="role" class="block w-full mt-1 border-neutral-stone rounded-xl shadow-sm"
                            x-model="selectedEmployee.role">
                            <option value="karyawan">Karyawan</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Jabatan" />
                        <x-text-input name="jabatan" class="block w-full mt-1" x-model="selectedEmployee.jabatan" />
                    </div>
                </div>

                <div class="border-t pt-4 mt-4">
                    <p class="text-xs text-text-secondary mb-2">Kosongkan jika tidak ingin mengganti password</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Password Baru" />
                            <x-text-input type="password" name="password" class="block w-full mt-1" />
                        </div>
                        <div>
                            <x-input-label value="Konfirmasi" />
                            <x-text-input type="password" name="password_confirmation" class="block w-full mt-1" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="$dispatch('close-modal', 'edit-employee-modal')"
                        class="px-4 py-2 text-text-secondary hover:bg-neutral-stone/20 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-amber-200 hover:bg-amber-300 text-amber-900 font-bold rounded-xl shadow-soft">Update</button>
                </div>
            </form>
        </x-pastel-modal>

        <!-- MODAL DELETE USER -->
        <x-pastel-modal name="delete-employee-modal" title="Hapus Karyawan" maxWidth="md">
            <div class="p-4 text-center">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-text-primary mb-2">Konfirmasi Penghapusan</h3>
                <p class="text-text-secondary text-sm mb-6">
                    Apakah anda yakin ingin menghapus karyawan <strong x-text="selectedEmployee?.name"></strong>? Data yang
                    dihapus tidak dapat dikembalikan.
                </p>

                <form x-bind:action="'/admin/employees/' + (selectedEmployee ? selectedEmployee.id : '')" method="POST"
                    class="flex justify-center gap-3">
                    @csrf @method('DELETE')
                    <button type="button" @click="$dispatch('close-modal', 'delete-employee-modal')"
                        class="px-4 py-2 bg-white border border-neutral-stone rounded-xl text-text-secondary hover:bg-neutral-stone/10 transition">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl shadow-soft">Ya,
                        Hapus</button>
                </form>
            </div>
        </x-pastel-modal>

        <!-- MODAL FACE MANAGEMENT -->
        <x-pastel-modal name="face-management-modal" title="Kelola Wajah" maxWidth="lg">
            <div class="space-y-4">
                <div class="bg-blue-50 p-4 rounded-xl flex items-start gap-3">
                    <div class="text-blue-500 text-xl font-bold">ℹ️</div>
                    <div class="text-sm text-blue-800">
                        <p class="font-bold">Pastikan foto wajah jelas!</p>
                        <ul class="list-disc pl-4 mt-1">
                            <li>Upload minimal 3 foto, maksimal 6 foto.</li>
                            <li>Wajah harus terlihat jelas dan pencahayaan cukup.</li>
                            <li>Pastikan username <span class="font-mono bg-blue-100 px-1 rounded"
                                    x-text="selectedEmployee?.username"></span> sesuai dengan orang di foto.</li>
                        </ul>
                    </div>
                </div>

                <!-- Upload Area -->
                <label
                    class="block w-full border-2 border-dashed border-neutral-stone rounded-xl p-8 text-center hover:bg-neutral-stone/10 cursor-pointer transition">
                    <input type="file" multiple accept="image/*" class="hidden" @change="handleFaceFiles">
                    <div class="text-4xl mb-2">📸</div>
                    <p class="font-bold text-text-primary">Klik untuk Pilih Foto</p>
                    <p class="text-xs text-text-secondary mt-1">Format: JPG/PNG</p>
                </label>

                <!-- Preview Grid -->
                <div class="grid grid-cols-3 gap-2" x-show="facePreviewUrls.length > 0">
                    <template x-for="(url, index) in facePreviewUrls" :key="index">
                        <div class="relative aspect-square rounded-lg overflow-hidden border border-neutral-stone">
                            <img :src="url" class="w-full h-full object-cover">
                        </div>
                    </template>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="$dispatch('close-modal', 'face-management-modal')"
                        class="px-4 py-2 text-text-secondary hover:bg-neutral-stone/20 rounded-xl transition">Batal</button>
                    <button @click="uploadFaces"
                        class="px-4 py-2 bg-pastel-sky hover:bg-pastel-sky-dark text-blue-900 font-bold rounded-xl shadow-soft">Upload
                        & Register</button>
                </div>
            </div>
        </x-pastel-modal>

        <!-- MODAL SETTINGS -->
        <x-pastel-modal name="settings-modal" title="Pengaturan Kantor" maxWidth="lg">
            <div x-data="{
                    lat: {{ $settings->office_latitude ?? -6.175392 }},
                    lng: {{ $settings->office_longitude ?? 106.827153 }},
                    radius: {{ $settings->office_radius ?? 0.5 }},
                    name: '{{ $settings->office_name ?? 'Kantor Pusat' }}',
                    map: null,
                    marker: null,

                    init() {
                        this.$watch('$store', () => {});
                        window.addEventListener('open-modal', (e) => {
                            if (e.detail === 'settings-modal') {
                                this.initMap();
                            }
                        });
                    },

                    initMap() {
                        setTimeout(() => {
                            if (this.map) return;

                            this.map = L.map('map').setView([this.lat, this.lng], 15);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap contributors'
                            }).addTo(this.map);

                            this.marker = L.marker([this.lat, this.lng], {draggable: true}).addTo(this.map);

                            const circle = L.circle([this.lat, this.lng], {
                                color: 'green',
                                fillColor: '#a7f3d0',
                                fillOpacity: 0.5,
                                radius: this.radius * 1000
                            }).addTo(this.map);

                            this.marker.on('dragend', (e) => {
                                const pos = e.target.getLatLng();
                                this.lat = pos.lat;
                                this.lng = pos.lng;
                                circle.setLatLng(pos);
                            });

                            this.map.on('click', (e) => {
                                 this.marker.setLatLng(e.latlng);
                                 circle.setLatLng(e.latlng);
                                 this.lat = e.latlng.lat;
                                 this.lng = e.latlng.lng;
                            });
                        }, 300);
                    },

                    async saveSettings() {
                        try {
                            const token = document.querySelector('meta[name=" csrf-token"]').content; const response=await
                fetch('/api/admin/settings', { method: 'PUT' , headers: { 'Content-Type' : 'application/json'
                , 'X-CSRF-TOKEN' : token, 'Accept' : 'application/json' }, body: JSON.stringify({ office_name: this.name,
                office_latitude: this.lat, office_longitude: this.lng, office_radius: this.radius }) }); const result=await
                response.json(); if (response.ok) { alert('Pengaturan berhasil disimpan');
                $dispatch('close-modal', 'settings-modal' ); window.location.reload(); } else {
                alert('Gagal: ' + (result.message || ' Error saving settings')); } } catch (e) { console.error(e);
                alert('Terjadi kesalahan sistem'); } } }" class="space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Nama Kantor" />
                        <x-text-input x-model="name" class="block w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Radius (km)" />
                        <x-text-input type="number" step="0.01" x-model="radius" class="block w-full mt-1" />
                    </div>
                </div>

                <div class="space-y-1">
                    <x-input-label value="Lokasi Kantor (Drag marker atau klik peta)" />
                    <div id="map" class="w-full h-64 rounded-xl border border-neutral-stone z-0"></div>
                    <div class="grid grid-cols-2 gap-4 text-xs text-text-secondary">
                        <span>Lat: <span x-text="lat.toFixed(6)"></span></span>
                        <span>Lng: <span x-text="lng.toFixed(6)"></span></span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="$dispatch('close-modal', 'settings-modal')"
                        class="px-4 py-2 text-text-secondary hover:bg-neutral-stone/20 rounded-xl transition">Batal</button>
                    <button @click="saveSettings"
                        class="px-4 py-2 bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary font-bold rounded-xl shadow-soft">Simpan
                        Pengaturan</button>
                </div>
            </div>
        </x-pastel-modal>

        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        @endpush

        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        @endpush

    </div>
@endsection