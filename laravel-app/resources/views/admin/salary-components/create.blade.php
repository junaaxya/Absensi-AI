@extends('layouts.admin')

@section('header-title', 'Tambah Komponen Gaji')
@section('header-subtitle', 'Buat komponen penggajian baru')

@section('content')

<div x-data="wizardForm()" x-cloak>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.salary-components.index') }}"
            class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
            <span class="material-icons-round">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tambah Komponen Gaji</h1>
            <p class="text-sm text-slate-500 mt-0.5">Buat komponen penggajian baru</p>
        </div>
    </div>

    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center gap-0">
            <template x-for="(stepItem, idx) in steps" :key="idx">
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                            :class="step > idx + 1 ? 'bg-sage text-emerald-800' : (step === idx + 1 ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 ring-4 ring-sage/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400')">
                            <span x-show="step > idx + 1" class="material-icons-round text-[18px]">check</span>
                            <span x-show="step <= idx + 1" x-text="idx + 1"></span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider mt-2 hidden sm:block"
                            :class="step === idx + 1 ? 'text-slate-900 dark:text-white' : 'text-slate-400'" x-text="stepItem"></span>
                    </div>
                    <div x-show="idx < steps.length - 1" class="w-12 sm:w-20 h-0.5 mx-2 transition-all duration-300"
                        :class="step > idx + 1 ? 'bg-sage' : 'bg-slate-200 dark:bg-slate-700'"></div>
                </div>
            </template>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-2xl">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-icons-round text-[18px]">error</span>
                <span class="font-bold text-sm">Terdapat kesalahan:</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-0.5 ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.salary-components.store') }}" method="POST" @submit="handleSubmit">
        @csrf

        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Informasi Dasar</h2>
                <p class="text-sm text-slate-500 mb-6">Tentukan nama, kode, dan tipe komponen gaji</p>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Komponen <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="form.name" @input="autoGenerateCode"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-sm dark:text-white transition-all"
                            placeholder="contoh: Gaji Pokok, Tunjangan Transport" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kode Komponen <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" x-model="form.code"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-sm dark:text-white font-mono uppercase transition-all"
                            placeholder="BASIC_SALARY" required>
                        <p class="text-xs text-slate-400 mt-1">Kode unik, otomatis dari nama (huruf besar + underscore)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Tipe Komponen <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="earning" x-model="form.type" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-sage peer-checked:bg-sage/10 border-slate-200 dark:border-slate-700 hover:border-sage/50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-sage/30 flex items-center justify-center">
                                            <span class="material-icons-round text-emerald-700 dark:text-emerald-400 text-[18px]">trending_up</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900 dark:text-white">Pendapatan</p>
                                            <p class="text-[11px] text-slate-500">Gaji, tunjangan, bonus</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="deduction" x-model="form.type" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-rose-300 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-900/10 border-slate-200 dark:border-slate-700 hover:border-rose-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-rose-100 dark:bg-rose-900/20 flex items-center justify-center">
                                            <span class="material-icons-round text-rose-600 dark:text-rose-400 text-[18px]">trending_down</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900 dark:text-white">Potongan</p>
                                            <p class="text-[11px] text-slate-500">Pajak, BPJS, pinjaman</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="benefit" x-model="form.type" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-sky peer-checked:bg-sky/10 border-slate-200 dark:border-slate-700 hover:border-sky/50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-sky/30 flex items-center justify-center">
                                            <span class="material-icons-round text-blue-600 dark:text-blue-400 text-[18px]">health_and_safety</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900 dark:text-white">Benefit</p>
                                            <p class="text-[11px] text-slate-500">Asuransi, fasilitas</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <div>
                            <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Kena Pajak</p>
                            <p class="text-xs text-slate-500">Komponen ini dikenakan pajak penghasilan</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_taxable" value="1" x-model="form.is_taxable" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:ring-2 peer-focus:ring-sage/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sage"></div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Deskripsi</label>
                        <textarea name="description" x-model="form.description" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-sm dark:text-white transition-all resize-none"
                            placeholder="Deskripsi singkat tentang komponen ini (opsional)"></textarea>
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <button type="button" @click="nextStep"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all">
                        Selanjutnya
                        <span class="material-icons-round text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Nilai & Rumus</h2>
                <p class="text-sm text-slate-500 mb-6">Tentukan cara perhitungan komponen ini</p>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Tipe Nilai <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="value_type" value="flat" x-model="form.value_type" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-sage peer-checked:bg-sage/10 border-slate-200 dark:border-slate-700 hover:border-sage/50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-sage/30 flex items-center justify-center">
                                            <span class="material-icons-round text-emerald-700 dark:text-emerald-400 text-[18px]">pin</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900 dark:text-white">Nilai Tetap</p>
                                            <p class="text-[11px] text-slate-500">Nominal rupiah yang sama setiap bulan</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="value_type" value="formula" x-model="form.value_type" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-lavender peer-checked:bg-lavender/10 border-slate-200 dark:border-slate-700 hover:border-lavender/50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-lavender/30 flex items-center justify-center">
                                            <span class="material-icons-round text-purple-700 dark:text-purple-400 text-[18px]">functions</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900 dark:text-white">Rumus (Formula)</p>
                                            <p class="text-[11px] text-slate-500">Dihitung berdasarkan variabel</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <input type="hidden" name="is_fixed" :value="form.value_type === 'flat' ? '1' : '0'">
                    </div>

                    <div x-show="form.value_type === 'flat'" x-transition>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nilai Default</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                            <input type="number" name="default_amount" x-model="form.default_amount"
                                class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-sm dark:text-white transition-all"
                                placeholder="0" min="0" step="1000">
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Nilai default yang bisa di-override per karyawan</p>
                    </div>

                    <div x-show="form.value_type === 'formula'" x-transition>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Rumus</label>
                        <textarea name="formula" x-model="form.formula" @input="debouncedValidateFormula" rows="4" x-ref="formulaInput"
                            class="w-full px-4 py-3 bg-slate-900 dark:bg-slate-950 border border-slate-700 rounded-xl focus:ring-2 focus:ring-lavender focus:border-lavender text-sm text-emerald-400 transition-all resize-none"
                            style="font-family: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace;"
                            placeholder="contoh: basic_salary * 0.05"></textarea>

                        <div class="mt-2 flex items-center gap-2" x-show="form.formula.length > 0">
                            <template x-if="formulaValidation.loading">
                                <span class="flex items-center gap-1.5 text-xs text-slate-400">
                                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Memvalidasi...
                                </span>
                            </template>
                            <template x-if="!formulaValidation.loading && formulaValidation.valid === true">
                                <span class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                    <span class="material-icons-round text-[16px]">check_circle</span>
                                    Rumus valid!
                                </span>
                            </template>
                            <template x-if="!formulaValidation.loading && formulaValidation.valid === false">
                                <span class="flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium">
                                    <span class="material-icons-round text-[16px]">error</span>
                                    <span x-text="formulaValidation.error"></span>
                                </span>
                            </template>
                        </div>

                        <div class="mt-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-3">Variabel Tersedia</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <template x-for="v in availableVariables" :key="v.name">
                                    <button type="button" @click="insertVariable(v.name)"
                                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-left hover:bg-white dark:hover:bg-slate-700 transition-all group">
                                        <code class="text-xs font-mono text-lavender bg-lavender/10 px-2 py-0.5 rounded font-bold group-hover:bg-lavender/20 transition-all" x-text="v.name"></code>
                                        <span class="text-[11px] text-slate-500" x-text="v.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="mt-4 bg-lavender/10 dark:bg-lavender/5 rounded-xl border border-lavender/30 p-4" x-show="form.formula.length > 0 && formulaValidation.valid">
                            <p class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider mb-2">Preview Hasil</p>
                            <p class="text-xs text-slate-500 mb-1">Jika gaji Rp 8.000.000, hadir 22 hari:</p>
                            <p class="text-lg font-bold text-purple-700 dark:text-purple-300">
                                &rarr; <span x-text="formulaValidation.previewFormatted || 'Rp 550.000'"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <button type="button" @click="prevStep"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                        <span class="material-icons-round text-[18px]">arrow_back</span>
                        Kembali
                    </button>
                    <button type="button" @click="nextStep"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all">
                        Selanjutnya
                        <span class="material-icons-round text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Batasan & Pengaturan</h2>
                <p class="text-sm text-slate-500 mb-6">Atur batasan nilai dan urutan eksekusi</p>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nilai Minimum</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                                <input type="number" name="min_value" x-model="form.min_value"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-sm dark:text-white transition-all"
                                    placeholder="Opsional" min="0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nilai Maksimum</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                                <input type="number" name="max_value" x-model="form.max_value"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-sm dark:text-white transition-all"
                                    placeholder="Opsional" min="0">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Urutan Eksekusi</label>
                        <input type="number" name="execution_order" x-model="form.execution_order"
                            class="w-full sm:w-48 px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage text-sm dark:text-white transition-all"
                            placeholder="100" min="0">
                        <p class="text-xs text-slate-400 mt-1">Angka lebih kecil = dihitung lebih dulu</p>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <div>
                            <p class="font-bold text-sm text-slate-700 dark:text-slate-300">Status Aktif</p>
                            <p class="text-xs text-slate-500">Komponen ini aktif dan digunakan dalam perhitungan</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:ring-2 peer-focus:ring-sage/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sage"></div>
                        </label>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-3">Ringkasan Komponen</p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-slate-400 text-xs">Nama</span>
                                <p class="font-bold text-slate-900 dark:text-white" x-text="form.name || '-'"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs">Kode</span>
                                <p class="font-bold text-slate-900 dark:text-white font-mono" x-text="form.code || '-'"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs">Tipe</span>
                                <p class="font-bold text-slate-900 dark:text-white" x-text="typeLabels[form.type] || '-'"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs">Nilai</span>
                                <p class="font-bold text-slate-900 dark:text-white" x-text="form.value_type === 'flat' ? ('Rp ' + Number(form.default_amount || 0).toLocaleString('id-ID')) : 'Formula'"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <button type="button" @click="prevStep"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl font-bold text-sm transition-all">
                        <span class="material-icons-round text-[18px]">arrow_back</span>
                        Kembali
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-slate-900/20">
                        <span class="material-icons-round text-[18px]">save</span>
                        Simpan Komponen
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function wizardForm() {
    return {
        step: 1,
        steps: ['Informasi Dasar', 'Nilai & Rumus', 'Pengaturan'],
        typeLabels: { earning: 'Pendapatan', deduction: 'Potongan', benefit: 'Benefit' },
        form: {
            name: '{{ old("name", "") }}',
            code: '{{ old("code", "") }}',
            type: '{{ old("type", "earning") }}',
            is_taxable: {{ old('is_taxable', false) ? 'true' : 'false' }},
            description: '{{ old("description", "") }}',
            value_type: '{{ old("value_type", "flat") }}',
            default_amount: '{{ old("default_amount", "") }}',
            formula: '{{ old("formula", "") }}',
            min_value: '{{ old("min_value", "") }}',
            max_value: '{{ old("max_value", "") }}',
            execution_order: '{{ old("execution_order", "100") }}',
            is_active: {{ old('is_active', true) ? 'true' : 'false' }},
        },
        formulaValidation: {
            loading: false,
            valid: null,
            error: null,
            previewFormatted: null,
        },
        validateTimeout: null,
        availableVariables: [
            { name: 'basic_salary', label: 'Gaji pokok' },
            { name: 'present_days', label: 'Hari hadir' },
            { name: 'working_days', label: 'Total hari kerja' },
            { name: 'overtime_hours', label: 'Jam lembur' },
            { name: 'late_count', label: 'Jumlah keterlambatan' },
            { name: 'absent_days', label: 'Hari tidak hadir' },
        ],

        autoGenerateCode() {
            if (!this.form.code || this.form.code === this._lastAutoCode) {
                this.form.code = this.form.name
                    .toUpperCase()
                    .replace(/[^A-Z0-9\s]/g, '')
                    .replace(/\s+/g, '_')
                    .substring(0, 50);
                this._lastAutoCode = this.form.code;
            }
        },

        nextStep() {
            if (this.step === 1) {
                if (!this.form.name || !this.form.code || !this.form.type) {
                    return;
                }
            }
            if (this.step < 3) this.step++;
        },

        prevStep() {
            if (this.step > 1) this.step--;
        },

        insertVariable(varName) {
            const textarea = this.$refs.formulaInput;
            if (!textarea) return;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const before = this.form.formula.substring(0, start);
            const after = this.form.formula.substring(end);
            this.form.formula = before + varName + after;
            this.$nextTick(() => {
                textarea.focus();
                textarea.selectionStart = textarea.selectionEnd = start + varName.length;
            });
            this.debouncedValidateFormula();
        },

        debouncedValidateFormula() {
            clearTimeout(this.validateTimeout);
            if (!this.form.formula.trim()) {
                this.formulaValidation = { loading: false, valid: null, error: null, previewFormatted: null };
                return;
            }
            this.formulaValidation.loading = true;
            this.validateTimeout = setTimeout(() => this.runValidateFormula(), 500);
        },

        async runValidateFormula() {
            try {
                const res = await fetch('{{ route("admin.salary-components.validate-formula") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ formula: this.form.formula }),
                });
                const data = await res.json();
                this.formulaValidation.loading = false;
                this.formulaValidation.valid = data.valid;
                this.formulaValidation.error = data.error;

                if (data.valid) {
                    const previewRes = await fetch('{{ route("admin.salary-components.preview-formula") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            formula: this.form.formula,
                            context: { basic_salary: 8000000, present_days: 22, working_days: 22, overtime_hours: 0, late_count: 0, absent_days: 0 }
                        }),
                    });
                    const previewData = await previewRes.json();
                    this.formulaValidation.previewFormatted = previewData.formatted;
                }
            } catch (e) {
                this.formulaValidation.loading = false;
                this.formulaValidation.valid = false;
                this.formulaValidation.error = 'Gagal memvalidasi rumus.';
            }
        },

        handleSubmit(e) {
            if (this.step !== 3) {
                e.preventDefault();
                this.nextStep();
            }
        },
    };
}
</script>
@endpush
