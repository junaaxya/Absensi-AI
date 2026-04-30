@extends('layouts.admin')

@section('header-title', 'Manajemen Cuti')
@section('header-subtitle', 'Kelola tipe cuti dan saldo karyawan')

@section('content')

<div x-data="{ activeTab: '{{ $tab }}' }" class="space-y-6">

    {{-- Tab Navigation --}}
    <div class="flex items-center gap-1 bg-white dark:bg-gray-800 rounded-2xl p-1.5 shadow-sm border border-slate-200 dark:border-slate-700 w-fit">
        <button @click="activeTab = 'saldo'"
            :class="activeTab === 'saldo' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            <span class="material-icons-round text-[18px]">account_balance_wallet</span>
            Saldo Karyawan
        </button>
        <button @click="activeTab = 'tipe'"
            :class="activeTab === 'tipe' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            <span class="material-icons-round text-[18px]">category</span>
            Tipe Cuti
            <span class="min-w-[20px] h-5 px-1.5 flex items-center justify-center rounded-full text-[10px] font-bold"
                :class="activeTab === 'tipe' ? 'bg-white/20 text-white dark:bg-slate-900/20 dark:text-slate-900' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400'">
                {{ $leaveTypes->count() }}
            </span>
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-sage/20 border border-sage/40 rounded-xl px-4 py-3 flex items-center gap-2">
            <span class="material-icons-round text-emerald-600 text-[18px]">check_circle</span>
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose/20 border border-rose/40 rounded-xl px-4 py-3 flex items-center gap-2">
            <span class="material-icons-round text-red-600 text-[18px]">error</span>
            <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB 1: SALDO KARYAWAN --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'saldo'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Header Actions --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Saldo Cuti — {{ $year }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Edit kolom "Sisa" untuk menyesuaikan saldo manual</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('admin.leave-balances.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="tab" value="saldo">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama..."
                        class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sage focus:border-sage dark:text-white w-36">
                    <select name="year" onchange="this.form.submit()"
                        class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sage dark:text-white">
                        @for($y = now()->year + 1; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>

                @if(!$hasBalances)
                    <form method="POST" action="{{ route('admin.leave-balances.initialize') }}" class="inline">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" onclick="return confirm('Inisialisasi saldo cuti untuk semua karyawan aktif tahun {{ $year }}? Ini akan membuat saldo berdasarkan kuota di Tipe Cuti.')"
                            class="px-4 py-2 bg-sage text-slate-900 rounded-xl text-sm font-bold hover:brightness-95 transition flex items-center gap-1.5 shadow-sm">
                            <span class="material-icons-round text-[16px]">play_arrow</span>
                            Inisialisasi {{ $year }}
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.leave-balances.initialize') }}" class="inline">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" onclick="return confirm('Reset saldo cuti tahun {{ $year }}? Saldo yang sudah diedit manual akan dikembalikan ke kuota default.')"
                            class="px-3 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                            <span class="material-icons-round text-[14px]">refresh</span>
                            Reset
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Saldo Table --}}
        @if($leaveTypes->where('is_active', true)->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                                <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider sticky left-0 bg-slate-50 dark:bg-slate-800/50 z-10">Karyawan</th>
                                @foreach($leaveTypes->where('is_active', true) as $lt)
                                    <th class="px-3 py-3.5 text-center min-w-[140px]">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">{{ $lt->name }}</span>
                                        <div class="flex justify-center gap-3 mt-1.5">
                                            <span class="text-[9px] font-medium text-slate-400 w-8">Kuota</span>
                                            <span class="text-[9px] font-medium text-slate-400 w-8">Pakai</span>
                                            <span class="text-[9px] font-medium text-sage w-16">Sisa ✎</span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($employees as $employee)
                                @php $empBalances = $balances[$employee->id] ?? collect(); @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-5 py-3 sticky left-0 bg-white dark:bg-gray-800 z-10">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden shrink-0">
                                                <img src="{{ $employee->profile_photo_url }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-slate-900 dark:text-white leading-tight">{{ $employee->name }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $employee->getRoleNames()->first() ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($leaveTypes->where('is_active', true) as $lt)
                                        @php $bal = $empBalances->firstWhere('leave_type_id', $lt->id); @endphp
                                        <td class="px-3 py-3 text-center">
                                            @if($bal)
                                                <div class="flex items-center justify-center gap-3">
                                                    <span class="text-xs text-slate-500 w-8">{{ $bal->quota }}</span>
                                                    <span class="text-xs text-slate-400 w-8">{{ $bal->used }}</span>
                                                    <form method="POST" action="{{ route('admin.leave-balances.update', $bal->id) }}" class="inline-flex items-center gap-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="number" name="remaining" value="{{ $bal->remaining }}" min="0"
                                                            class="w-12 text-center text-xs font-bold rounded-lg border border-sage/40 bg-sage/5 dark:bg-sage/10 dark:text-white py-1 focus:ring-2 focus:ring-sage focus:border-sage transition-all">
                                                        <button type="submit" class="w-5 h-5 rounded bg-sage/30 text-emerald-700 dark:text-emerald-400 hover:bg-sage/50 flex items-center justify-center transition-all" title="Simpan">
                                                            <span class="material-icons-round text-[12px]">check</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-[10px] text-slate-300">belum diinisialisasi</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 1 + $leaveTypes->where('is_active', true)->count() }}" class="px-6 py-12 text-center">
                                        <span class="material-icons-round text-slate-300 text-4xl mb-2 block">groups</span>
                                        <p class="text-slate-500 font-medium text-sm">Tidak ada karyawan aktif.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($employees->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-12 text-center">
                <span class="material-icons-round text-slate-300 text-5xl mb-3 block">category</span>
                <h3 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Belum Ada Tipe Cuti</h3>
                <p class="text-sm text-slate-400 mb-4">Buat tipe cuti terlebih dahulu di tab "Tipe Cuti" sebelum menginisialisasi saldo.</p>
                <button @click="activeTab = 'tipe'" class="px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-sm font-bold hover:opacity-90 transition-all">
                    Buat Tipe Cuti
                </button>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB 2: TIPE CUTI --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'tipe'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-data="{ showCreate: false, showEdit: false, showDelete: false, editData: {}, deleteData: {} }">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Tipe Cuti</h2>
                <p class="text-xs text-slate-500 mt-0.5">Jenis cuti yang tersedia beserta kuota tahunan</p>
            </div>
            <button @click="showCreate = true"
                class="px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-sm font-bold hover:opacity-90 transition-all shadow-md flex items-center gap-2">
                <span class="material-icons-round text-[16px]">add</span>
                Tambah Tipe
            </button>
        </div>

        {{-- Leave Types Grid --}}
        @if($leaveTypes->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($leaveTypes as $lt)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-md transition-all group">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-[10px] font-mono font-bold text-slate-600 dark:text-slate-400 mb-1.5">{{ $lt->code }}</span>
                                <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ $lt->name }}</h3>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="editData = { id: {{ $lt->id }}, name: '{{ addslashes($lt->name) }}', code: '{{ $lt->code }}', days_quota: {{ $lt->days_quota }}, is_paid: {{ $lt->is_paid ? 'true' : 'false' }}, is_active: {{ $lt->is_active ? 'true' : 'false' }} }; showEdit = true"
                                    class="w-7 h-7 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-all">
                                    <span class="material-icons-round text-[14px]">edit</span>
                                </button>
                                <button @click="deleteData = { id: {{ $lt->id }}, name: '{{ addslashes($lt->name) }}' }; showDelete = true"
                                    class="w-7 h-7 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center justify-center text-slate-400 hover:text-red-600 transition-all">
                                    <span class="material-icons-round text-[14px]">delete</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-3">
                            <div class="flex items-center gap-1.5">
                                <span class="material-icons-round text-[14px] text-slate-400">event</span>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $lt->days_quota }} hari</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $lt->is_paid ? 'bg-sage/20 text-emerald-700 dark:text-emerald-400' : 'bg-peach/20 text-orange-700 dark:text-orange-400' }}">
                                {{ $lt->is_paid ? 'Berbayar' : 'Tidak Berbayar' }}
                            </span>
                            @if(!$lt->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-500">
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center">
                <span class="material-icons-round text-slate-300 text-5xl mb-3 block">beach_access</span>
                <h3 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Belum Ada Tipe Cuti</h3>
                <p class="text-sm text-slate-400 mb-4">Tambahkan tipe cuti seperti "Cuti Tahunan", "Cuti Sakit", dll.</p>
                <button @click="showCreate = true" class="px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-sm font-bold hover:opacity-90 transition-all">
                    Tambah Tipe Cuti Pertama
                </button>
            </div>
        @endif

        {{-- CREATE MODAL --}}
        <div x-show="showCreate" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showCreate = false"></div>
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-md shadow-2xl">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tambah Tipe Cuti</h3>
                    <button @click="showCreate = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-icons-round text-slate-400">close</span>
                    </button>
                </div>
                <form action="{{ route('admin.leave-balances.leave-types.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Nama Cuti</label>
                                <input type="text" name="name" required placeholder="Cuti Tahunan"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sage focus:border-sage dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Kode</label>
                                <input type="text" name="code" required maxlength="10" placeholder="CT"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono uppercase focus:ring-2 focus:ring-sage focus:border-sage dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Kuota per Tahun (hari)</label>
                            <input type="number" name="days_quota" required min="0" placeholder="12"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sage focus:border-sage dark:text-white">
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_paid" value="0">
                                <input type="checkbox" name="is_paid" value="1" checked class="rounded border-slate-300 text-sage focus:ring-sage">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Berbayar</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-sage focus:ring-sage">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Aktif</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-md">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- EDIT MODAL --}}
        <div x-show="showEdit" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showEdit = false"></div>
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-md shadow-2xl">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Tipe Cuti</h3>
                    <button @click="showEdit = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-icons-round text-slate-400">close</span>
                    </button>
                </div>
                <form :action="'{{ route('admin.leave-balances.leave-types.store') }}/' + editData.id" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Nama Cuti</label>
                                <input type="text" name="name" x-model="editData.name" required
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sage focus:border-sage dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Kode</label>
                                <input type="text" name="code" x-model="editData.code" required maxlength="10"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono uppercase focus:ring-2 focus:ring-sage focus:border-sage dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Kuota per Tahun (hari)</label>
                            <input type="number" name="days_quota" x-model="editData.days_quota" required min="0"
                                class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sage focus:border-sage dark:text-white">
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_paid" value="0">
                                <input type="checkbox" name="is_paid" value="1" :checked="editData.is_paid" class="rounded border-slate-300 text-sage focus:ring-sage">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Berbayar</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" :checked="editData.is_active" class="rounded border-slate-300 text-sage focus:ring-sage">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Aktif</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-md">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- DELETE MODAL --}}
        <div x-show="showDelete" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDelete = false"></div>
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl text-center">
                <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                    <span class="material-icons-round text-red-500 text-2xl">delete_forever</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1">Hapus Tipe Cuti</h3>
                <p class="text-sm text-slate-500 mb-5">Hapus <strong x-text="deleteData.name"></strong>?</p>
                <div class="flex gap-3">
                    <button @click="showDelete = false" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-bold text-sm hover:bg-slate-200 transition-all">
                        Batal
                    </button>
                    <form :action="'{{ url('/admin/leave-balances/leave-types') }}/' + deleteData.id" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 bg-red-500 text-white rounded-xl font-bold text-sm hover:bg-red-600 transition-all">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
