@extends('layouts.admin')

@section('header-title', $company->name)
@section('header-subtitle', 'Detail perusahaan dan manajemen cabang')

@section('content')

    <div x-data="{
        showBranchModal: false,
        showAssignModal: false,
        editBranch: null,
        branchForm: { name: '', code: '', address: '', phone: '', latitude: '', longitude: '', radius: 100, is_active: true }
    }">

        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('admin.companies.index') }}"
                class="flex items-center gap-2 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                <span class="material-icons-round text-lg">arrow_back</span>
                <span class="text-sm font-bold">Kembali</span>
            </a>
            <div class="flex gap-2">
                <a href="{{ route('admin.companies.settings', $company) }}"
                    class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-round text-lg">settings</span>
                    Pengaturan
                </a>
                <a href="{{ route('admin.companies.edit', $company) }}"
                    class="px-4 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                    <span class="material-icons-round text-lg">edit</span>
                    Edit
                </a>
            </div>
        </div>

        {{-- Company Info Card --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
            <div class="flex items-start gap-4">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                        class="w-20 h-20 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shrink-0" />
                @else
                    <div class="w-20 h-20 rounded-2xl bg-lavender/30 dark:bg-lavender/10 flex items-center justify-center shrink-0">
                        <span class="material-icons-round text-lavender-600 dark:text-lavender-400 text-3xl">business</span>
                    </div>
                @endif
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="font-bold text-2xl text-slate-900 dark:text-white">{{ $company->name }}</h2>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide {{ $company->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-500' }}">
                            {{ $company->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        @if($company->is_headquarters)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                Kantor Pusat
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 font-medium mb-3">Kode: {{ $company->code }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                        @if($company->address)
                            <div class="flex items-start gap-2 text-slate-600 dark:text-slate-400">
                                <span class="material-icons-round text-base mt-0.5 shrink-0">location_on</span>
                                <span>{{ $company->address }}</span>
                            </div>
                        @endif
                        @if($company->phone)
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <span class="material-icons-round text-base">phone</span>
                                <span>{{ $company->phone }}</span>
                            </div>
                        @endif
                        @if($company->email)
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <span class="material-icons-round text-base">email</span>
                                <span>{{ $company->email }}</span>
                            </div>
                        @endif
                        @if($company->website)
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <span class="material-icons-round text-base">language</span>
                                <a href="{{ $company->website }}" target="_blank" class="hover:text-sky-600 transition-colors">{{ $company->website }}</a>
                            </div>
                        @endif
                        @if($company->npwp)
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <span class="material-icons-round text-base">receipt</span>
                                <span>NPWP: {{ $company->npwp }}</span>
                            </div>
                        @endif
                        @if($company->parent)
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                <span class="material-icons-round text-base">account_tree</span>
                                <span>Induk: {{ $company->parent->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                <div class="text-center p-3 bg-sky-50 dark:bg-sky-900/20 rounded-xl">
                    <p class="text-2xl font-bold text-sky-700 dark:text-sky-300">{{ $branchCount }}</p>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wide mt-1">Cabang</p>
                </div>
                <div class="text-center p-3 bg-sage/20 dark:bg-sage/10 rounded-xl">
                    <p class="text-2xl font-bold text-sage-700 dark:text-sage-300">{{ $employeeCount }}</p>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wide mt-1">Karyawan</p>
                </div>
                <div class="text-center p-3 bg-lavender/20 dark:bg-lavender/10 rounded-xl">
                    <p class="text-2xl font-bold text-lavender-700 dark:text-lavender-300">{{ $company->children->count() }}</p>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wide mt-1">Anak Perusahaan</p>
                </div>
                <div class="text-center p-3 bg-peach/20 dark:bg-peach/10 rounded-xl">
                    <p class="text-2xl font-bold text-peach-700 dark:text-peach-300">{{ $company->branches->where('is_active', true)->count() }}</p>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wide mt-1">Cabang Aktif</p>
                </div>
            </div>
        </div>

        {{-- Branches Section --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-icons-round text-sage">store</span> Daftar Cabang
                </h3>
                <button @click="editBranch = null; branchForm = { name: '', code: '', address: '', phone: '', latitude: '', longitude: '', radius: 100, is_active: true }; showBranchModal = true"
                    class="px-4 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2 active:scale-95">
                    <span class="material-icons-round text-lg">add</span>
                    Tambah Cabang
                </button>
            </div>

            {{-- Branch Map --}}
            @if($company->branches->where('latitude', '!=', null)->count() > 0)
                <div id="branch-map" class="w-full h-64 rounded-xl border border-slate-200 dark:border-slate-700 mb-4"></div>
            @endif

            @if($company->branches->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase tracking-wide text-xs">Cabang</th>
                                <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase tracking-wide text-xs">Kode</th>
                                <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase tracking-wide text-xs">Alamat</th>
                                <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase tracking-wide text-xs">Karyawan</th>
                                <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase tracking-wide text-xs">Radius</th>
                                <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase tracking-wide text-xs">Status</th>
                                <th class="text-right py-3 px-4 font-bold text-slate-500 uppercase tracking-wide text-xs">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->branches as $branch)
                                <tr class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $branch->name }}</td>
                                    <td class="py-3 px-4 text-slate-500 font-medium">{{ $branch->code }}</td>
                                    <td class="py-3 px-4 text-slate-500 max-w-xs truncate">{{ $branch->address ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                            {{ $branch->employees_count }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center text-slate-500">{{ $branch->radius }}m</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide {{ $branch->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button @click="editBranch = {{ $branch->id }}; branchForm = { name: '{{ addslashes($branch->name) }}', code: '{{ $branch->code }}', address: '{{ addslashes($branch->address ?? '') }}', phone: '{{ $branch->phone ?? '' }}', latitude: '{{ $branch->latitude ?? '' }}', longitude: '{{ $branch->longitude ?? '' }}', radius: {{ $branch->radius }}, is_active: {{ $branch->is_active ? 'true' : 'false' }} }; showBranchModal = true"
                                                class="p-1.5 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                                <span class="material-icons-round text-base">edit</span>
                                            </button>
                                            <form action="{{ url('/admin/companies/branches/' . $branch->id) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Hapus cabang {{ addslashes($branch->name) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                                                    <span class="material-icons-round text-base">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="material-icons-round text-slate-400 text-2xl">store</span>
                    </div>
                    <p class="text-slate-500 text-sm">Belum ada cabang. Tambahkan cabang pertama.</p>
                </div>
            @endif
        </div>

        {{-- Assign Employee Section --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-icons-round text-sage">people</span> Karyawan ({{ $employeeCount }})
                </h3>
                <button @click="showAssignModal = true"
                    class="px-4 py-2 rounded-xl bg-sage text-white font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2 active:scale-95">
                    <span class="material-icons-round text-lg">person_add</span>
                    Tugaskan Karyawan
                </button>
            </div>

            @if($company->employees->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($company->employees->take(12) as $emp)
                        <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden shrink-0">
                                <img src="{{ $emp->profile_photo_url }}" alt="{{ $emp->name }}" class="w-full h-full object-cover" />
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $emp->name }}</p>
                                <p class="text-xs text-slate-500">{{ $emp->jabatan ?? 'Belum ada jabatan' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($company->employees->count() > 12)
                    <p class="text-center text-sm text-slate-500 mt-4">
                        dan {{ $company->employees->count() - 12 }} karyawan lainnya...
                    </p>
                @endif
            @else
                <div class="py-6 text-center">
                    <p class="text-slate-500 text-sm">Belum ada karyawan yang ditugaskan ke perusahaan ini.</p>
                </div>
            @endif
        </div>

        {{-- Branch Modal --}}
        <div x-show="showBranchModal" style="display: none;" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto" @click.outside="showBranchModal = false">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4" x-text="editBranch ? 'Edit Cabang' : 'Tambah Cabang Baru'"></h3>

                <form :action="editBranch ? `{{ url('/admin/companies/branches') }}/${editBranch}` : `{{ route('admin.companies.show', $company) }}/branches`"
                    method="POST">
                    @csrf
                    <template x-if="editBranch">
                        <input type="hidden" name="_method" value="PUT" />
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Cabang</label>
                            <input type="text" name="name" x-model="branchForm.name" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage text-sm text-slate-900 dark:text-white font-medium" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Cabang</label>
                            <input type="text" name="code" x-model="branchForm.code" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage text-sm text-slate-900 dark:text-white font-medium uppercase" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat</label>
                            <textarea name="address" x-model="branchForm.address" rows="2"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage text-sm text-slate-900 dark:text-white font-medium"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Telepon</label>
                            <input type="text" name="phone" x-model="branchForm.phone"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage text-sm text-slate-900 dark:text-white font-medium" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Latitude</label>
                                <input type="number" name="latitude" x-model="branchForm.latitude" step="0.0000001"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage text-sm text-slate-900 dark:text-white font-medium" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Longitude</label>
                                <input type="number" name="longitude" x-model="branchForm.longitude" step="0.0000001"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage text-sm text-slate-900 dark:text-white font-medium" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Radius Geofence (meter)</label>
                            <input type="number" name="radius" x-model="branchForm.radius" min="10" max="10000"
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage text-sm text-slate-900 dark:text-white font-medium" />
                        </div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="branchForm.is_active"
                                class="w-5 h-5 rounded-lg border-slate-300 text-sage focus:ring-sage" />
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Aktif</span>
                        </label>
                    </div>

                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="showBranchModal = false"
                            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition-all text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:opacity-90 transition-all text-sm active:scale-95">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Assign Employee Modal --}}
        <div x-show="showAssignModal" style="display: none;" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.outside="showAssignModal = false">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4">Tugaskan Karyawan</h3>

                <form action="{{ url('/admin/companies/' . $company->id . '/assign-employee') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Karyawan</label>
                            <select name="user_id" required
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage text-sm text-slate-900 dark:text-white font-medium">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach(\App\Models\User::whereNull('company_id')->orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                                @endforeach
                            </select>
                        </div>
                        @if($company->branches->count() > 0)
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Cabang (Opsional)</label>
                                <select name="branch_id"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage text-sm text-slate-900 dark:text-white font-medium">
                                    <option value="">-- Tidak ditugaskan ke cabang --</option>
                                    @foreach($company->branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="showAssignModal = false"
                            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition-all text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-sage text-white rounded-xl font-bold hover:opacity-90 transition-all text-sm active:scale-95">
                            Tugaskan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg font-bold text-sm flex items-center gap-2">
            <span class="material-icons-round text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapEl = document.getElementById('branch-map');
            if (!mapEl) return;

            const branches = @json($company->branches->filter(fn($b) => $b->latitude && $b->longitude)->map(fn($b) => [
                'name' => $b->name,
                'lat' => (float) $b->latitude,
                'lng' => (float) $b->longitude,
                'radius' => $b->radius,
                'address' => $b->address ?? '',
            ])->values());

            if (branches.length === 0) return;

            const map = L.map('branch-map').setView([branches[0].lat, branches[0].lng], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const bounds = [];
            branches.forEach(function (b) {
                const marker = L.marker([b.lat, b.lng]).addTo(map);
                marker.bindPopup(`<strong>${b.name}</strong><br>${b.address}<br>Radius: ${b.radius}m`);
                L.circle([b.lat, b.lng], { radius: b.radius, color: '#C8D5B9', fillOpacity: 0.15 }).addTo(map);
                bounds.push([b.lat, b.lng]);
            });

            if (bounds.length > 1) {
                map.fitBounds(bounds, { padding: [30, 30] });
            }
        });
    </script>
@endpush
