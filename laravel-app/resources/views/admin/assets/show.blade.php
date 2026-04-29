@extends('layouts.admin')

@section('header-title', $asset->name)
@section('header-subtitle', 'Detail Aset - ' . $asset->asset_code)

@section('content')

    <div x-data="{ showReturnModal: false, showMaintenanceModal: false }">

        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <a href="{{ route('admin.assets.index') }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium">
                <span class="material-icons-round text-lg">arrow_back</span>
                Kembali ke Daftar Aset
            </a>
            <div class="flex gap-2">
                @if(!$asset->assigned_to)
                    <a href="{{ route('admin.assets.assign', $asset) }}"
                        class="bg-sky-500 text-white px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:bg-sky-600 transition-all text-sm shadow-lg shadow-sky-500/20 active:scale-95">
                        <span class="material-icons-round text-lg">person_add</span>
                        Tugaskan
                    </a>
                @else
                    <button @click="showReturnModal = true"
                        class="bg-amber-500 text-white px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:bg-amber-600 transition-all text-sm shadow-lg shadow-amber-500/20 active:scale-95">
                        <span class="material-icons-round text-lg">assignment_return</span>
                        Kembalikan
                    </button>
                @endif
                <button @click="showMaintenanceModal = true"
                    class="bg-white dark:bg-card-dark text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-sm">
                    <span class="material-icons-round text-lg">build</span>
                    Pemeliharaan
                </button>
                <a href="{{ route('admin.assets.edit', $asset) }}"
                    class="bg-white dark:bg-card-dark text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-sm">
                    <span class="material-icons-round text-lg">edit</span>
                    Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Asset Detail Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                    @if($asset->photo)
                        <img src="{{ asset('storage/' . $asset->photo) }}" alt="{{ $asset->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <span class="material-icons-round text-6xl text-slate-300 dark:text-slate-600">inventory_2</span>
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="font-mono text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg text-slate-700 dark:text-slate-300">{{ $asset->asset_code }}</span>
                            @php
                                $conditionStyle = match($asset->condition) {
                                    'baik' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                    'rusak_ringan' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                    'rusak_berat' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
                                    'hilang' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    'dihapuskan' => 'bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-500',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                                $conditionLabel = match($asset->condition) {
                                    'baik' => 'Baik',
                                    'rusak_ringan' => 'Rusak Ringan',
                                    'rusak_berat' => 'Rusak Berat',
                                    'hilang' => 'Hilang',
                                    'dihapuskan' => 'Dihapuskan',
                                    default => $asset->condition,
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $conditionStyle }}">{{ $conditionLabel }}</span>
                        </div>

                        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">{{ $asset->name }}</h2>
                        <p class="text-sm text-slate-500 mb-4">{{ $asset->category->name ?? '-' }}</p>

                        <div class="space-y-3 text-sm">
                            @if($asset->serial_number)
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Nomor Seri</span>
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $asset->serial_number }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-slate-500">Tgl Pembelian</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $asset->purchase_date->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Harga Beli</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">Rp {{ number_format($asset->purchase_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nilai Saat Ini</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($asset->current_value, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nilai Depresiasi</span>
                                <span class="font-bold text-sky-600 dark:text-sky-400">Rp {{ number_format($currentDepreciatedValue, 0, ',', '.') }}</span>
                            </div>
                            @if($asset->location)
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Lokasi</span>
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $asset->location }}</span>
                                </div>
                            @endif
                        </div>

                        @if($asset->assignedUser)
                            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-2">Ditugaskan Kepada</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <img src="{{ $asset->assignedUser->profile_photo_url }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $asset->assignedUser->name }}</p>
                                        <p class="text-xs text-slate-500">Sejak {{ $asset->assigned_at?->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($asset->description)
                            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-2">Deskripsi</p>
                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $asset->description }}</p>
                            </div>
                        @endif

                        @if($asset->notes)
                            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-2">Catatan</p>
                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $asset->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Depreciation Chart --}}
                @if(count($depreciationSchedule) > 0)
                    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="material-icons-round text-sky-500">trending_down</span>
                            Grafik Depresiasi
                        </h3>
                        <div id="depreciation-chart" class="h-64"></div>
                    </div>
                @endif

                {{-- Assignment History --}}
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-icons-round text-lavender">history</span>
                        Riwayat Penugasan
                    </h3>

                    @if($asset->assignments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800">
                                        <th class="text-left py-3 px-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase">Karyawan</th>
                                        <th class="text-left py-3 px-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase">Ditugaskan</th>
                                        <th class="text-left py-3 px-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase">Dikembalikan</th>
                                        <th class="text-left py-3 px-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase">Kondisi</th>
                                        <th class="text-left py-3 px-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase">Oleh</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($asset->assignments->sortByDesc('assigned_at') as $assignment)
                                        <tr>
                                            <td class="py-3 px-4 font-medium text-slate-700 dark:text-slate-300">{{ $assignment->user->name ?? '-' }}</td>
                                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400">{{ $assignment->assigned_at->format('d M Y') }}</td>
                                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400">{{ $assignment->returned_at?->format('d M Y') ?? '-' }}</td>
                                            <td class="py-3 px-4">
                                                <span class="text-xs">{{ $assignment->condition_on_assign }}</span>
                                                @if($assignment->condition_on_return)
                                                    <span class="text-slate-400 mx-1">&rarr;</span>
                                                    <span class="text-xs">{{ $assignment->condition_on_return }}</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400 text-xs">{{ $assignment->assignedByUser->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <span class="material-icons-round text-3xl text-slate-300 dark:text-slate-600">person_off</span>
                            <p class="text-slate-500 mt-2 text-sm">Belum ada riwayat penugasan</p>
                        </div>
                    @endif
                </div>

                {{-- Maintenance Log --}}
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-icons-round text-amber-500">build</span>
                        Log Pemeliharaan
                    </h3>

                    @if($asset->maintenances->count() > 0)
                        <div class="space-y-4">
                            @foreach($asset->maintenances->sortByDesc('performed_at') as $maintenance)
                                <div class="border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            @php
                                                $typeStyle = match($maintenance->maintenance_type) {
                                                    'preventive' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
                                                    'corrective' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                                    'upgrade' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                                    default => 'bg-slate-100 text-slate-700',
                                                };
                                                $typeLabel = match($maintenance->maintenance_type) {
                                                    'preventive' => 'Preventif',
                                                    'corrective' => 'Korektif',
                                                    'upgrade' => 'Upgrade',
                                                    default => $maintenance->maintenance_type,
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $typeStyle }}">{{ $typeLabel }}</span>
                                            <span class="text-xs text-slate-500">{{ $maintenance->performed_at->format('d M Y') }}</span>
                                        </div>
                                        @if($maintenance->cost > 0)
                                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $maintenance->description }}</p>
                                    @if($maintenance->performed_by)
                                        <p class="text-xs text-slate-500 mt-2">Oleh: {{ $maintenance->performed_by }}</p>
                                    @endif
                                    @if($maintenance->next_maintenance_at)
                                        <p class="text-xs text-sky-600 dark:text-sky-400 mt-1">Pemeliharaan berikutnya: {{ $maintenance->next_maintenance_at->format('d M Y') }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <span class="material-icons-round text-3xl text-slate-300 dark:text-slate-600">build_circle</span>
                            <p class="text-slate-500 mt-2 text-sm">Belum ada log pemeliharaan</p>
                        </div>
                    @endif
                </div>

                {{-- Depreciation Schedule Table --}}
                @if(count($depreciationSchedule) > 0)
                    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="material-icons-round text-emerald-500">table_chart</span>
                            Jadwal Depresiasi
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800">
                                        <th class="text-left py-3 px-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase">Tahun</th>
                                        <th class="text-right py-3 px-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase">Depresiasi</th>
                                        <th class="text-right py-3 px-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase">Akumulasi</th>
                                        <th class="text-right py-3 px-4 font-bold text-slate-600 dark:text-slate-400 text-xs uppercase">Nilai Buku</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($depreciationSchedule as $row)
                                        <tr>
                                            <td class="py-3 px-4 font-medium text-slate-700 dark:text-slate-300">{{ $row['date'] }}</td>
                                            <td class="py-3 px-4 text-right text-slate-600 dark:text-slate-400">Rp {{ number_format($row['depreciation'], 0, ',', '.') }}</td>
                                            <td class="py-3 px-4 text-right text-slate-600 dark:text-slate-400">Rp {{ number_format($row['accumulated'], 0, ',', '.') }}</td>
                                            <td class="py-3 px-4 text-right font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($row['book_value'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Return Modal --}}
        <div x-show="showReturnModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="showReturnModal = false"
                class="bg-white dark:bg-card-dark rounded-2xl p-6 w-full max-w-md border border-slate-200 dark:border-slate-800 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-amber-500">assignment_return</span>
                    Kembalikan Aset
                </h3>
                <form method="POST" action="{{ route('admin.assets.return', $asset) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kondisi Saat Dikembalikan <span class="text-rose-500">*</span></label>
                            <select name="condition_on_return" required
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">
                                <option value="baik">Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                                <option value="hilang">Hilang</option>
                                <option value="dihapuskan">Dihapuskan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan</label>
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="showReturnModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-amber-500 text-white font-bold text-sm hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">
                            Kembalikan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Maintenance Modal --}}
        <div x-show="showMaintenanceModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="showMaintenanceModal = false"
                class="bg-white dark:bg-card-dark rounded-2xl p-6 w-full max-w-lg border border-slate-200 dark:border-slate-800 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-amber-500">build</span>
                    Tambah Log Pemeliharaan
                </h3>
                <form method="POST" action="{{ route('admin.assets.maintenance.store', $asset) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tipe Pemeliharaan <span class="text-rose-500">*</span></label>
                            <select name="maintenance_type" required
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">
                                <option value="preventive">Preventif</option>
                                <option value="corrective">Korektif</option>
                                <option value="upgrade">Upgrade</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi <span class="text-rose-500">*</span></label>
                            <textarea name="description" rows="3" required
                                class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Biaya</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                                    <input type="number" name="cost" min="0" step="0.01" value="0"
                                        class="w-full pl-12 pr-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Dilakukan Oleh</label>
                                <input type="text" name="performed_by"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Dilakukan <span class="text-rose-500">*</span></label>
                                <input type="date" name="performed_at" required value="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pemeliharaan Berikutnya</label>
                                <input type="date" name="next_maintenance_at"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="showMaintenanceModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
@if(count($depreciationSchedule) > 0)
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var schedule = @json($depreciationSchedule);
        var options = {
            chart: {
                type: 'line',
                height: 256,
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif',
            },
            series: [{
                name: 'Nilai Buku',
                data: schedule.map(function(row) { return row.book_value; })
            }],
            xaxis: {
                categories: schedule.map(function(row) { return row.date; }),
                labels: { style: { fontSize: '11px', colors: '#94a3b8' } }
            },
            yaxis: {
                labels: {
                    formatter: function(val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); },
                    style: { fontSize: '11px', colors: '#94a3b8' }
                }
            },
            colors: ['#B8D4E3'],
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 4, colors: ['#B8D4E3'], strokeColors: '#fff', strokeWidth: 2 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 100] }
            },
            grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
            tooltip: {
                y: { formatter: function(val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); } }
            }
        };
        var chart = new ApexCharts(document.querySelector("#depreciation-chart"), options);
        chart.render();
    });
</script>
@endif
@endpush
