@extends('layouts.admin')

@section('header-title', 'Data Karyawan')
@section('header-subtitle', 'Kelola Data Perangkat Desa')

@section('content')
    <div x-data="employeeFaceManager({
        faceStatus: @js($employees->pluck('has_face_data', 'id')),
        csrfToken: @js(csrf_token())
    })" class="space-y-6">

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form action="{{ route('employees.index') }}" method="GET" class="relative w-full max-w-md">
                <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari karyawan..."
                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm focus:ring-2 focus:ring-sage dark:border-slate-800 dark:bg-card-dark dark:text-white" />
                @if(request()->filled('face_status'))
                    <input type="hidden" name="face_status" value="{{ request('face_status') }}">
                @endif
                @if(request()->filled('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                @if(request()->filled('jabatan'))
                    <input type="hidden" name="jabatan" value="{{ request('jabatan') }}">
                @endif
            </form>

            <div class="flex w-full gap-2 md:w-auto">
                <a href="{{ route('employees.create') }}"
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 font-bold text-white shadow-lg shadow-slate-900/20 transition-all hover:opacity-90 active:scale-95 dark:bg-white dark:text-slate-900 md:flex-none">
                    <span class="material-icons-round text-lg">add</span>
                    Tambah
                </a>
            </div>
        </div>

        <div class="flex gap-2 w-full md:w-auto">
            <button onclick="window.location='{{ route('employees.create') }}'"
                class="flex-1 md:flex-none bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95">
                <span class="material-icons-round text-lg">add</span>
                Tambah
            </button>
            <button onclick="window.location='{{ route('admin.export.employees') }}'"
                class="flex-1 md:flex-none bg-sky/20 text-sky-700 dark:text-sky-300 border border-sky/20 px-4 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-sky/30 transition-all shadow-sm active:scale-95">
                <span class="material-icons-round text-lg">download</span>
                Export
            </button>
            <button
                class="flex-1 md:flex-none bg-white dark:bg-card-dark text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 px-4 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95">
                <span class="material-icons-round text-lg">filter_list</span>
                Filter
            </button>

        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($employees as $employee)
                <div
                    class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 transition-all duration-300 hover:border-sage hover:shadow-xl hover:shadow-slate-200/50 dark:border-slate-800 dark:bg-card-dark dark:hover:shadow-slate-900/50">
                    <div class="absolute -mr-4 -mt-4 h-24 w-24 rounded-bl-full bg-sage/10 transition-transform group-hover:scale-110"></div>

                    <div class="relative mb-6">
                        <div class="mx-auto h-20 w-20 overflow-hidden rounded-2xl bg-slate-100 shadow-md">
                            @if($employee->foto)
                                <img src="{{ asset('storage/' . $employee->foto) }}" alt="{{ $employee->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-slate-200 text-slate-400">
                                    <span class="material-icons-round text-4xl">person</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6 text-center">
                        <h3 class="mb-1 text-lg font-bold text-slate-900 dark:text-white">{{ $employee->name }}</h3>
                        <p class="text-sm font-medium uppercase tracking-wide text-sage">{{ $employee->jabatan }}</p>
                        <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold"
                            x-bind:class="faceStatus[{{ $employee->id }}] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200'"
                            x-text="faceStatus[{{ $employee->id }}] ? 'Face: Terdaftar' : 'Face: Belum'"></span>
                    </div>

                    <div class="mb-6 space-y-3">
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <span class="material-icons-round text-lg text-slate-300">email</span>
                            <span class="truncate">{{ $employee->email }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <span class="material-icons-round text-lg text-slate-300">phone</span>
                            <span>{{ $employee->no_hp }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <span class="material-icons-round text-lg text-slate-300">badge</span>
                            <span>NIP: {{ $employee->nip ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                        <button type="button"
                            data-id="{{ $employee->id }}"
                            data-name="{{ $employee->name }}"
                            data-nip="{{ $employee->nip }}"
                            data-email="{{ $employee->email }}"
                            data-role="{{ $employee->role }}"
                            data-jabatan="{{ $employee->jabatan }}"
                            data-username="{{ $employee->username }}"
                            data-foto-url="{{ $employee->foto ? asset('storage/' . $employee->foto) : '' }}"
                            @click="openFaceModalFromDataset($el.dataset)"
                            class="flex w-full items-center justify-center gap-2 rounded-xl border border-primary/40 bg-primary/15 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-primary/30 dark:text-slate-100">
                            <span class="material-icons-round text-base">face_retouching_natural</span>
                            Kelola Data Wajah
                        </button>

                        <div class="flex gap-2">
                            <a href="{{ route('employees.edit', $employee->id) }}"
                                class="flex-1 rounded-xl bg-slate-50 py-2 text-xs font-bold text-slate-600 transition-colors hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                Edit
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $employee->id }}', '{{ $employee->name }}')"
                                class="flex-1 rounded-xl bg-rose-50 py-2 text-xs font-bold text-rose-500 transition-colors hover:bg-rose-100 dark:bg-rose-900/20 dark:hover:bg-rose-900/40">
                                Hapus
                            </button>
                        </div>

                        <form id="delete-form-{{ $employee->id }}" action="{{ route('employees.destroy', $employee->id) }}"
                            method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $employees->links() }}
        </div>

        <div x-show="faceModalOpen" x-cloak
            class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-md md:left-64 md:p-6"
            x-transition.opacity>
            <div @click.outside="closeFaceModal()" class="flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-[32px] border border-white bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between border-b border-slate-100 px-8 py-6 dark:border-slate-800">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Kelola Data Wajah Karyawan</h2>
                        <p class="mt-1 text-slate-500 dark:text-slate-400">Pendaftaran dan Pengelolaan Data Wajah Untuk Absensi</p>
                    </div>
                    <button type="button" @click="closeFaceModal()"
                        class="rounded-full p-2 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-icons-round text-slate-400">close</span>
                    </button>
                </div>

                <div class="space-y-8 overflow-y-auto p-8">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6 dark:border-slate-700/50 dark:bg-slate-800/50">
                        <div class="flex flex-col gap-6 md:flex-row md:items-center">
                            <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                <template x-if="selectedEmployee?.foto_url">
                                    <img :src="selectedEmployee.foto_url" alt="Foto Karyawan" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!selectedEmployee?.foto_url">
                                    <span class="material-icons-round text-5xl text-slate-300">account_circle</span>
                                </template>
                            </div>
                            <div class="grid flex-1 grid-cols-1 gap-x-8 gap-y-3 sm:grid-cols-2 md:grid-cols-3">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Nama Lengkap</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="selectedEmployee?.name || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">NIP / Karyawan</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="selectedEmployee?.nip || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Email</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="selectedEmployee?.email || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Role Sistem</p>
                                    <p class="font-semibold capitalize text-slate-700 dark:text-slate-200" x-text="selectedEmployee?.role || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jabatan</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="selectedEmployee?.jabatan || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div class="flex flex-col items-center justify-center rounded-2xl border border-primary/20 bg-primary/10 p-6 text-center dark:border-primary/10 dark:bg-primary/5">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary shadow-sm shadow-primary/30">
                                <span class="material-icons-round text-white">verified</span>
                            </div>
                            <p class="text-lg font-bold text-primary" x-text="dataset.registered ? 'Terdaftar' : 'Belum Terdaftar'"></p>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Jumlah Foto: <span class="font-bold text-slate-800 dark:text-slate-200" x-text="dataset.photo_count"></span></p>
                        </div>

                        <label
                            class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-sky/40 bg-sky/5 p-6 transition-colors hover:border-sky md:col-span-2 dark:border-sky/20">
                            <input type="file" accept="image/jpeg,image/png,image/jpg" multiple class="hidden" @change="onFilesSelected($event)">
                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-sky/20 transition-transform group-hover:scale-110 dark:bg-sky/10">
                                <span class="material-icons-round text-3xl text-sky-600">cloud_upload</span>
                            </div>
                            <span class="mb-3 rounded-xl border border-slate-200 bg-white px-6 py-2.5 font-bold text-slate-700 shadow-sm transition-colors group-hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:group-hover:bg-slate-700">
                                Pilih Foto Wajah
                            </span>
                            <p class="text-center text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                                Unggah Maksimal 6 Foto Wajah<br>Dengan Posisi Sesuai Instruksi Admin
                            </p>
                            <template x-if="selectedFiles.length">
                                <p class="mt-3 text-xs font-semibold text-slate-700 dark:text-slate-300" x-text="selectedFiles.length + ' foto siap diproses'"></p>
                            </template>
                        </label>
                    </div>

                    <template x-if="modalMessage.text">
                        <div class="rounded-xl border px-4 py-3 text-sm"
                            :class="modalMessage.type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700'"
                            x-text="modalMessage.text"></div>
                    </template>

                    <div class="space-y-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h3 class="flex items-center gap-2 font-bold text-slate-700 dark:text-slate-200">
                                <span class="h-6 w-1.5 rounded-full bg-lavender"></span>
                                Dataset Wajah Tersimpan
                            </h3>
                            <button type="button" @click="deleteDataset()" :disabled="loadingDeleteDataset || dataset.photo_count === 0"
                                class="flex items-center gap-2 rounded-xl bg-peach px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition-all hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50">
                                <span class="material-icons-round text-[18px]">delete_sweep</span>
                                <span x-text="loadingDeleteDataset ? 'Menghapus...' : 'Hapus Dataset Wajah'"></span>
                            </button>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-slate-100 shadow-sm dark:border-slate-800">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:bg-slate-800/80 dark:text-slate-400">
                                    <tr>
                                        <th class="px-6 py-4">No</th>
                                        <th class="px-6 py-4">Foto</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600 dark:divide-slate-800 dark:text-slate-300">
                                    <template x-if="!dataset.photos.length">
                                        <tr>
                                            <td colspan="4" class="px-6 py-6 text-center text-sm text-slate-500 dark:text-slate-400">Belum ada dataset wajah tersimpan.</td>
                                        </tr>
                                    </template>
                                    <template x-for="(photo, index) in dataset.photos" :key="photo.filename">
                                        <tr class="transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                            <td class="px-6 py-3 font-medium" x-text="index + 1"></td>
                                            <td class="px-6 py-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg bg-slate-200 dark:bg-slate-700">
                                                        <template x-if="photo.photo_url">
                                                            <img :src="photo.photo_url" alt="Face photo" class="h-full w-full object-cover">
                                                        </template>
                                                        <template x-if="!photo.photo_url">
                                                            <span class="material-icons-round text-sm text-slate-400">image</span>
                                                        </template>
                                                    </div>
                                                    <span class="max-w-[180px] truncate text-xs" x-text="photo.filename"></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-3">
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-primary/20 bg-primary/20 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-primary/10 dark:text-slate-200">
                                                    <span class="material-icons-round text-[14px]">check_circle</span>
                                                    Valid
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                <button type="button" @click="deletePhoto(photo.filename)"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-peach/30 text-rose-600 transition-colors hover:bg-peach/50">
                                                    <span class="material-icons-round text-[18px]">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center border-t border-slate-100 bg-slate-50 px-8 py-6 dark:border-slate-800 dark:bg-slate-800/30">
                    <button type="button" @click="processAndSaveFaceData()"
                        :disabled="loadingSave || !selectedFiles.length"
                        class="flex items-center gap-3 rounded-2xl bg-primary px-10 py-4 font-bold text-white shadow-lg shadow-primary/30 transition-all hover:-translate-y-0.5 hover:shadow-primary/40 disabled:cursor-not-allowed disabled:opacity-60">
                        <span class="material-icons-round" x-text="loadingSave ? 'hourglass_top' : 'sync'"></span>
                        <span x-text="loadingSave ? 'Memproses...' : 'Proses & Simpan Data Wajah'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(id, name) {
            if (confirm('Apakah Anda yakin ingin menghapus karyawan ' + name + '?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        function employeeFaceManager(config) {
            return {
                faceStatus: config.faceStatus || {},
                csrfToken: config.csrfToken,
                faceModalOpen: false,
                selectedEmployee: null,
                selectedFiles: [],
                loadingDataset: false,
                loadingSave: false,
                loadingDeleteDataset: false,
                modalMessage: {
                    type: '',
                    text: ''
                },
                dataset: {
                    registered: false,
                    photo_count: 0,
                    photos: []
                },

                openFaceModal(employeePayload) {
                    this.modalMessage = { type: '', text: '' };

                    let employee = employeePayload;
                    if (typeof employeePayload === 'string') {
                        employee = JSON.parse(employeePayload);
                    }

                    this.selectedEmployee = employee;
                    this.selectedFiles = [];
                    this.faceModalOpen = true;
                    this.loadDataset();
                },

                openFaceModalFromDataset(dataset) {
                    this.openFaceModal({
                        id: Number(dataset.id || 0),
                        name: dataset.name || '',
                        nip: dataset.nip || '',
                        email: dataset.email || '',
                        role: dataset.role || '',
                        jabatan: dataset.jabatan || '',
                        username: dataset.username || '',
                        foto_url: dataset.fotoUrl || null
                    });
                },

                closeFaceModal() {
                    this.faceModalOpen = false;
                    this.selectedEmployee = null;
                    this.selectedFiles = [];
                    this.dataset = { registered: false, photo_count: 0, photos: [] };
                    this.modalMessage = { type: '', text: '' };
                },

                async loadDataset() {
                    if (!this.selectedEmployee?.id) {
                        return;
                    }

                    this.loadingDataset = true;
                    try {
                        const response = await fetch(`/admin/employees/${this.selectedEmployee.id}/face-data`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const result = await response.json();
                        if (!response.ok || !result.success) {
                            const detail = result.error ? ` (${result.error})` : '';
                            throw new Error((result.message || 'Gagal mengambil dataset wajah.') + detail);
                        }

                        this.dataset = {
                            registered: !!result.data.registered,
                            photo_count: Number(result.data.photo_count || 0),
                            photos: result.data.photos || []
                        };

                        this.faceStatus[this.selectedEmployee.id] = this.dataset.registered;
                    } catch (error) {
                        this.modalMessage = {
                            type: 'error',
                            text: error.message || 'Terjadi kesalahan saat memuat dataset wajah.'
                        };
                    } finally {
                        this.loadingDataset = false;
                    }
                },

                onFilesSelected(event) {
                    const files = Array.from(event.target.files || []);
                    if (!files.length) {
                        return;
                    }

                    if (files.length > 6) {
                        this.modalMessage = {
                            type: 'error',
                            text: 'Maksimal 6 foto untuk setiap proses pendaftaran wajah.'
                        };
                        this.selectedFiles = files.slice(0, 6);
                    } else {
                        this.modalMessage = { type: '', text: '' };
                        this.selectedFiles = files;
                    }
                },

                async processAndSaveFaceData() {
                    if (!this.selectedEmployee?.id) {
                        return;
                    }

                    if (!this.selectedFiles.length) {
                        this.modalMessage = {
                            type: 'error',
                            text: 'Pilih minimal 1 foto wajah terlebih dahulu.'
                        };
                        return;
                    }

                    this.loadingSave = true;
                    this.modalMessage = { type: '', text: '' };

                    const formData = new FormData();
                    formData.append('user_id', this.selectedEmployee.id);
                    this.selectedFiles.forEach((file) => {
                        formData.append('photos[]', file);
                    });

                    try {
                        const response = await fetch('/api/face/register', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const result = await response.json();
                        if (!response.ok) {
                            const detail = result.error ? ` (${result.error})` : '';
                            throw new Error((result.message || 'Gagal memproses data wajah.') + detail);
                        }

                        this.modalMessage = {
                            type: 'success',
                            text: result.message || 'Data wajah berhasil diproses.'
                        };

                        this.selectedFiles = [];
                        await this.loadDataset();
                    } catch (error) {
                        this.modalMessage = {
                            type: 'error',
                            text: error.message || 'Terjadi kesalahan saat memproses data wajah.'
                        };
                    } finally {
                        this.loadingSave = false;
                    }
                },

                async deleteDataset() {
                    if (!this.selectedEmployee?.id) {
                        return;
                    }

                    if (!confirm('Hapus seluruh dataset wajah karyawan ini?')) {
                        return;
                    }

                    this.loadingDeleteDataset = true;
                    this.modalMessage = { type: '', text: '' };

                    try {
                        const response = await fetch(`/admin/employees/${this.selectedEmployee.id}/face-data`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const result = await response.json();
                        if (!response.ok || !result.success) {
                            const detail = result.error ? ` (${result.error})` : '';
                            throw new Error((result.message || 'Gagal menghapus dataset wajah.') + detail);
                        }

                        this.dataset = {
                            registered: false,
                            photo_count: 0,
                            photos: []
                        };
                        this.faceStatus[this.selectedEmployee.id] = false;
                        this.modalMessage = {
                            type: 'success',
                            text: result.message || 'Dataset wajah berhasil dihapus.'
                        };
                    } catch (error) {
                        this.modalMessage = {
                            type: 'error',
                            text: error.message || 'Terjadi kesalahan saat menghapus dataset wajah.'
                        };
                    } finally {
                        this.loadingDeleteDataset = false;
                    }
                },

                async deletePhoto(filename) {
                    if (!this.selectedEmployee?.id || !filename) {
                        return;
                    }

                    if (!confirm('Hapus foto ini dari dataset wajah?')) {
                        return;
                    }

                    this.modalMessage = { type: '', text: '' };

                    try {
                        const response = await fetch(`/admin/employees/${this.selectedEmployee.id}/face-data/photos/${encodeURIComponent(filename)}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const result = await response.json();
                        if (!response.ok || !result.success) {
                            const detail = result.error ? ` (${result.error})` : '';
                            throw new Error((result.message || 'Gagal menghapus foto wajah.') + detail);
                        }

                        this.modalMessage = {
                            type: 'success',
                            text: result.message || 'Foto wajah berhasil dihapus.'
                        };

                        await this.loadDataset();
                    } catch (error) {
                        this.modalMessage = {
                            type: 'error',
                            text: error.message || 'Terjadi kesalahan saat menghapus foto wajah.'
                        };
                    }
                }
            };
        }
    </script>
@endpush
