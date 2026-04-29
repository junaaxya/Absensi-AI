@extends('layouts.admin')

@section('header-title', 'Edit Data Karyawan')
@section('header-subtitle', 'Memperbarui Informasi Perangkat Desa')

@section('content')

    <div class="max-w-4xl mx-auto" x-data="{
        status_pernikahan: '{{ old('status_pernikahan', $employee->status_pernikahan ?? '') }}',
        jumlah_tanggungan: {{ old('jumlah_tanggungan', $employee->jumlah_tanggungan ?? 0) }},
        get ptkpStatus() {
            if (!this.status_pernikahan) return '-';
            return this.status_pernikahan + '/' + this.jumlah_tanggungan;
        },
        get terCategory() {
            const map = {'TK/0':'A','TK/1':'A','K/0':'A','TK/2':'B','TK/3':'B','K/1':'B','K/2':'B','K/3':'C'};
            return map[this.ptkpStatus] || '-';
        }
    }">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white">Form Edit Karyawan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Perbarui informasi karyawan di bawah ini.</p>
            </div>
            <button onclick="history.back()"
                class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-icons-round">arrow_back</span>
            </button>
        </div>

        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Section 1: Data Pribadi --}}
            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">person</span> Data Pribadi
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Foto Profil --}}
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Foto Profil</label>
                        <div class="flex items-center gap-4">
                            <div class="h-16 w-16 overflow-hidden rounded-xl bg-slate-100 shadow-sm flex-shrink-0">
                                <img src="{{ $employee->profile_photo_url }}" alt="{{ $employee->name }}" class="h-full w-full object-cover">
                            </div>
                            <input type="file" name="foto" accept="image/*"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sage/10 file:text-sage hover:file:bg-sage/20" />
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah foto.</p>
                        @error('foto')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('name') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Contoh: Budi Santoso" />
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NIK --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik', $employee->nik) }}" maxlength="16"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('nik') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="16 digit NIK" />
                        @error('nik')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tempat Lahir --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $employee->tempat_lahir) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('tempat_lahir') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Contoh: Jakarta" />
                        @error('tempat_lahir')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $employee->tanggal_lahir?->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('tanggal_lahir') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400" />
                        @error('tanggal_lahir')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('jenis_kelamin') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin', $employee->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $employee->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No. Telepon --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. Telepon</label>
                        <input type="text" name="no_telepon" value="{{ old('no_telepon', $employee->no_telepon) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('no_telepon') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="08xxxxxxxxxx" />
                        @error('no_telepon')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alamat</label>
                        <textarea name="alamat" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('alamat') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Alamat lengkap">{{ old('alamat', $employee->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Data Kepegawaian --}}
            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">work</span> Data Kepegawaian
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Jabatan --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $employee->jabatan) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('jabatan') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Contoh: Staff Administrasi" />
                        @error('jabatan')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Departemen --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Departemen</label>
                        <select name="department_id"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('department_id') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Shift Kerja --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Shift Kerja</label>
                        <select name="shift_id"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('shift_id') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('shift_id')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Perusahaan --}}
                    <div x-data="{ companyId: '{{ old('company_id', $employee->company_id ?? '') }}', branches: @json($branches ?? []) }" x-init="
                        if (companyId && branches.length === 0) {
                            fetch(`/api/companies/${companyId}/branches`)
                                .then(r => r.json())
                                .then(d => branches = d)
                                .catch(() => branches = []);
                        }
                    ">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Perusahaan</label>
                        <select name="company_id" x-model="companyId" @change="
                            if (companyId) {
                                fetch(`/api/companies/${companyId}/branches`)
                                    .then(r => r.json())
                                    .then(d => branches = d)
                                    .catch(() => branches = []);
                            } else { branches = []; }
                        "
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('company_id') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="">-- Pilih Perusahaan --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $employee->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }} ({{ $company->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror

                        <div class="mt-4" x-show="companyId">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Cabang</label>
                            <select name="branch_id"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('branch_id') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                                <option value="">-- Tidak ditugaskan ke cabang --</option>
                                <template x-for="branch in branches" :key="branch.id">
                                    <option :value="branch.id" :selected="branch.id == {{ old('branch_id', $employee->branch_id ?? 0) }}" x-text="`${branch.name} (${branch.code})`"></option>
                                </template>
                            </select>
                            @error('branch_id')
                                <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Role Pengguna</label>
                        <select name="role"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('role') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="">-- Pilih Role --</option>
                            @php $currentRole = old('role', $employee->getRoleNames()->first()); @endphp
                            @foreach(['Direktur', 'Vice President', 'Manager', 'Supervisor', 'Team Leader', 'Staf', 'Magang'] as $role)
                                <option value="{{ $role }}" {{ $currentRole == $role ? 'selected' : '' }}>{{ $role }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Karyawan --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Status Karyawan</label>
                        <select name="status_karyawan"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('status_karyawan') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="tetap" {{ old('status_karyawan', $employee->status_karyawan) == 'tetap' ? 'selected' : '' }}>Tetap</option>
                            <option value="kontrak" {{ old('status_karyawan', $employee->status_karyawan) == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                            <option value="magang" {{ old('status_karyawan', $employee->status_karyawan) == 'magang' ? 'selected' : '' }}>Magang</option>
                        </select>
                        @error('status_karyawan')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Masuk --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $employee->tanggal_masuk?->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('tanggal_masuk') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400" />
                        @error('tanggal_masuk')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Keluar --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Keluar <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <input type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', $employee->tanggal_keluar?->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('tanggal_keluar') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400" />
                        @error('tanggal_keluar')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gaji Pokok --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Gaji Pokok</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                            <input type="number" name="gaji_pokok" value="{{ old('gaji_pokok', $employee->gaji_pokok) }}" step="1000" min="0"
                                class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('gaji_pokok') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                                placeholder="0" />
                        </div>
                        @error('gaji_pokok')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 3: Data Pajak & BPJS --}}
            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">receipt_long</span> Data Pajak & BPJS
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- NPWP --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">NPWP</label>
                        <input type="text" name="npwp" value="{{ old('npwp', $employee->npwp) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('npwp') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="XX.XXX.XXX.X-XXX.XXX" />
                        @error('npwp')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Pernikahan --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Status Pernikahan</label>
                        <select name="status_pernikahan" x-model="status_pernikahan"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('status_pernikahan') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="">-- Pilih --</option>
                            <option value="TK" {{ old('status_pernikahan', $employee->status_pernikahan) == 'TK' ? 'selected' : '' }}>Tidak Kawin</option>
                            <option value="K" {{ old('status_pernikahan', $employee->status_pernikahan) == 'K' ? 'selected' : '' }}>Kawin</option>
                        </select>
                        @error('status_pernikahan')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah Tanggungan --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jumlah Tanggungan</label>
                        <select name="jumlah_tanggungan" x-model="jumlah_tanggungan"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('jumlah_tanggungan') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            @for($i = 0; $i <= 3; $i++)
                                <option value="{{ $i }}" {{ old('jumlah_tanggungan', $employee->jumlah_tanggungan ?? 0) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('jumlah_tanggungan')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PTKP & TER Category --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">PTKP & Kategori TER</label>
                        <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl min-h-[50px]">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-sage/20 text-sage-700 dark:text-sage-300"
                                x-text="ptkpStatus"></span>
                            <span class="text-slate-400">—</span>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-sky/20 text-sky-700 dark:text-sky-300"
                                x-text="terCategory !== '-' ? 'Kategori ' + terCategory : '-'"></span>
                        </div>
                    </div>

                    {{-- No. BPJS Kesehatan --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. BPJS Kesehatan</label>
                        <input type="text" name="no_bpjs_kesehatan" value="{{ old('no_bpjs_kesehatan', $employee->no_bpjs_kesehatan) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('no_bpjs_kesehatan') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Nomor BPJS Kesehatan" />
                        @error('no_bpjs_kesehatan')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No. BPJS Ketenagakerjaan --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. BPJS Ketenagakerjaan</label>
                        <input type="text" name="no_bpjs_ketenagakerjaan" value="{{ old('no_bpjs_ketenagakerjaan', $employee->no_bpjs_ketenagakerjaan) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('no_bpjs_ketenagakerjaan') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Nomor BPJS Ketenagakerjaan" />
                        @error('no_bpjs_ketenagakerjaan')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 4: Data Bank --}}
            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">account_balance</span> Data Bank
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Bank --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Bank</label>
                        <input type="text" name="nama_bank" value="{{ old('nama_bank', $employee->nama_bank) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('nama_bank') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Contoh: BCA, BRI, Mandiri" />
                        @error('nama_bank')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No. Rekening --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. Rekening</label>
                        <input type="text" name="no_rekening" value="{{ old('no_rekening', $employee->no_rekening) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('no_rekening') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Nomor rekening bank" />
                        @error('no_rekening')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 5: Kontak Darurat --}}
            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">emergency</span> Kontak Darurat
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Kontak Darurat --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Kontak Darurat</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('emergency_contact_name') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Nama keluarga/kerabat" />
                        @error('emergency_contact_name')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No. Telepon Darurat --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. Telepon Darurat</label>
                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('emergency_contact_phone') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="08xxxxxxxxxx" />
                        @error('emergency_contact_phone')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 6: Akun & Keamanan --}}
            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">lock</span> Akun & Keamanan
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $employee->email) }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('email') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="nama@email.com" />
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username', $employee->username) }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('username') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="username_karyawan" />
                        @error('username')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 p-5 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                    <h5 class="font-bold text-sm text-slate-900 dark:text-white mb-1">Ubah Password</h5>
                    <p class="text-xs text-slate-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border {{ $errors->has('password') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                                placeholder="********" />
                            @error('password')
                                <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                                placeholder="********" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-end gap-3">
                <button type="button" onclick="history.back()"
                    class="px-6 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 dark:shadow-none active:scale-95">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

@endsection
