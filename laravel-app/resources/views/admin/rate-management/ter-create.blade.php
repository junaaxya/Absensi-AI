@extends('layouts.admin')

@section('header-title', 'Tambah Regulasi TER')
@section('header-subtitle', 'Publikasikan regulasi tarif efektif rata-rata baru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.rate-management.ter') }}"
       class="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
        <span class="material-icons-round text-[18px]">arrow_back</span>
        Kembali ke Tarif TER
    </a>
</div>

<div x-data="terForm()" class="space-y-6">
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <h3 class="font-bold text-slate-800 dark:text-white">Informasi Regulasi</h3>
        </div>
        <div class="p-6 space-y-4">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kode Regulasi</label>
                    <input type="text" x-model="regulationCode" required
                           placeholder="PP_58_2023"
                           class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">
                    <p class="text-xs text-slate-400 mt-1">Contoh: PP_58_2023</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Berlaku Dari</label>
                    <input type="date" x-model="effectiveFrom" required
                           class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">
                </div>
            </div>

            @if($latestCode)
            <button type="button" @click="prefillFromExisting()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-lavender text-purple-700 dark:text-purple-300 text-sm font-medium hover:bg-lavender/10 transition-colors">
                <span class="material-icons-round text-[16px]">content_copy</span>
                Salin dari Regulasi Sebelumnya ({{ $latestCode }})
            </button>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <h3 class="font-bold text-slate-800 dark:text-white">Tabel Tarif</h3>
        </div>
        <div class="p-6">
            <div class="flex gap-2 mb-4">
                <template x-for="cat in ['A', 'B', 'C']" :key="cat">
                    <button type="button" @click="activeTab = cat"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                            :class="activeTab === cat ? 'bg-lavender/30 text-purple-800 dark:text-purple-300' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'">
                        <span x-text="'Kategori ' + cat"></span>
                        <span class="ml-1 text-xs text-slate-400" x-text="'(' + rates[cat].length + ')'"></span>
                    </button>
                </template>
            </div>

            <template x-for="cat in ['A', 'B', 'C']" :key="'tab-' + cat">
                <div x-show="activeTab === cat" x-cloak>
                    <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-slate-500 uppercase tracking-wider bg-slate-50 dark:bg-slate-800/50">
                                    <th class="px-4 py-2.5 w-8">#</th>
                                    <th class="px-4 py-2.5">
                                        <span>Penghasilan Min (Rp)</span>
                                        <p class="text-[10px] font-normal normal-case tracking-normal text-slate-400 mt-0.5">Batas bawah gaji bruto bulanan</p>
                                    </th>
                                    <th class="px-4 py-2.5">
                                        <span>Penghasilan Maks (Rp)</span>
                                        <p class="text-[10px] font-normal normal-case tracking-normal text-slate-400 mt-0.5">Kosongkan untuk tak terbatas</p>
                                    </th>
                                    <th class="px-4 py-2.5">
                                        <span>Tarif (desimal)</span>
                                        <p class="text-[10px] font-normal normal-case tracking-normal text-slate-400 mt-0.5">Contoh: 5% &rarr; 0.05</p>
                                    </th>
                                    <th class="px-4 py-2.5 w-12"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, idx) in rates[cat]" :key="cat + '-' + idx">
                                    <tr class="border-t border-slate-100 dark:border-slate-800">
                                        <td class="px-4 py-2 text-slate-400" x-text="idx + 1"></td>
                                        <td class="px-4 py-2">
                                            <input type="number" x-model.number="row.min_income" min="0" step="1"
                                                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-sm py-1.5 px-2">
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="number" x-model.number="row.max_income" min="0" step="1"
                                                   placeholder="Kosong = ∞"
                                                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-sm py-1.5 px-2">
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="number" x-model.number="row.rate" min="0" max="1" step="0.00001"
                                                   placeholder="0.05"
                                                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-sm py-1.5 px-2">
                                        </td>
                                        <td class="px-4 py-2">
                                            <button type="button" @click="removeRow(cat, idx)"
                                                    class="p-1 rounded-lg text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                <span class="material-icons-round text-[18px]">close</span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" @click="addRow(cat)"
                            class="mt-3 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm text-primary font-medium hover:bg-primary/10 transition-colors">
                        <span class="material-icons-round text-[16px]">add</span>
                        Tambah Baris
                    </button>
                </div>
            </template>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="button" @click="submitForm()"
                class="px-6 py-2.5 rounded-xl bg-primary text-slate-900 font-semibold text-sm hover:bg-primary/80 shadow-sm transition-colors"
                :disabled="submitting"
                :class="submitting ? 'opacity-50 cursor-not-allowed' : ''">
            <span x-show="!submitting">Publikasikan Regulasi</span>
            <span x-show="submitting">Menyimpan...</span>
        </button>
        <a href="{{ route('admin.rate-management.ter') }}"
           class="px-6 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 font-semibold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            Batal
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function terForm() {
    return {
        regulationCode: '{{ old("regulation_code", "") }}',
        effectiveFrom: '{{ old("effective_from", "") }}',
        activeTab: 'A',
        submitting: false,
        rates: {
            A: [{ min_income: 0, max_income: null, rate: null }],
            B: [{ min_income: 0, max_income: null, rate: null }],
            C: [{ min_income: 0, max_income: null, rate: null }],
        },

        addRow(cat) {
            this.rates[cat].push({ min_income: 0, max_income: null, rate: null });
        },

        removeRow(cat, idx) {
            if (this.rates[cat].length > 1) {
                this.rates[cat].splice(idx, 1);
            }
        },

        prefillFromExisting() {
            const existing = @json($existingRates);
            for (const cat of ['A', 'B', 'C']) {
                if (existing[cat] && existing[cat].length > 0) {
                    this.rates[cat] = existing[cat].map(r => ({
                        min_income: parseFloat(r.min_income) || 0,
                        max_income: r.max_income ? parseFloat(r.max_income) : null,
                        rate: parseFloat(r.rate) || null,
                    }));
                }
            }
        },

        submitForm() {
            if (!this.regulationCode || !this.effectiveFrom) {
                alert('Kode regulasi dan tanggal berlaku wajib diisi.');
                return;
            }

            this.submitting = true;

            const allRates = [];
            for (const cat of ['A', 'B', 'C']) {
                for (const row of this.rates[cat]) {
                    if (row.rate !== null && row.rate !== '') {
                        allRates.push({
                            category: cat,
                            min_income: row.min_income || 0,
                            max_income: row.max_income || null,
                            rate: row.rate,
                        });
                    }
                }
            }

            if (allRates.length === 0) {
                alert('Minimal harus ada 1 baris tarif.');
                this.submitting = false;
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.rate-management.ter.store") }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrf);

            const codeInput = document.createElement('input');
            codeInput.type = 'hidden';
            codeInput.name = 'regulation_code';
            codeInput.value = this.regulationCode;
            form.appendChild(codeInput);

            const dateInput = document.createElement('input');
            dateInput.type = 'hidden';
            dateInput.name = 'effective_from';
            dateInput.value = this.effectiveFrom;
            form.appendChild(dateInput);

            allRates.forEach((r, i) => {
                ['category', 'min_income', 'max_income', 'rate'].forEach(field => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `rates[${i}][${field}]`;
                    input.value = r[field] !== null ? r[field] : '';
                    form.appendChild(input);
                });
            });

            document.body.appendChild(form);
            form.submit();
        }
    };
}
</script>
@endpush
