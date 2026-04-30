@extends('layouts.admin')

@section('header-title', 'Saldo Cuti')
@section('header-subtitle', 'Kelola Saldo Cuti Karyawan')

@section('content')

    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Saldo Cuti Karyawan</h2>
                <p class="text-sm text-gray-500 mt-1">Tahun {{ $year }}</p>
            </div>

            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('admin.leave-balances.index') }}" class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari karyawan..."
                        class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-lavender dark:text-white">
                    <select name="year" onchange="this.form.submit()"
                        class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-lavender dark:text-white">
                        @for($y = now()->year + 1; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary text-slate-900 rounded-xl text-sm font-bold hover:brightness-95 transition">
                        Filter
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.leave-balances.initialize') }}" class="inline">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" onclick="return confirm('Inisialisasi saldo cuti untuk semua karyawan aktif tahun {{ $year }}?')"
                        class="px-4 py-2 bg-lavender text-slate-900 rounded-xl text-sm font-bold hover:brightness-95 transition flex items-center gap-2">
                        <span class="material-icons-round text-base">refresh</span>
                        Inisialisasi {{ $year }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Info Banner --}}
        <div class="bg-sky/10 border border-sky/30 rounded-2xl p-4 flex items-start gap-3">
            <span class="material-icons-round text-blue-600 text-[20px] mt-0.5 shrink-0">info</span>
            <div>
                <p class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-1">Cara Menggunakan</p>
                <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-1 leading-relaxed">
                    <li>1. Klik <strong>"Inisialisasi {{ $year }}"</strong> untuk membuat saldo cuti semua karyawan aktif (hanya perlu sekali per tahun)</li>
                    <li>2. Kolom <strong>"Sisa"</strong> bisa diedit langsung — ubah angka lalu klik tombol <span class="inline-flex items-center"><span class="material-icons-round text-[12px] text-emerald-600">save</span></span> untuk menyimpan</li>
                    <li>3. Saldo otomatis berkurang saat pengajuan cuti karyawan disetujui</li>
                </ul>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-800/50">Karyawan</th>
                            @foreach($leaveTypes as $lt)
                                <th class="px-4 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center" colspan="3">
                                    {{ $lt->name }}
                                    <div class="flex justify-center gap-2 mt-1 text-[9px] font-medium text-gray-300">
                                        <span>Kuota</span>
                                        <span>Pakai</span>
                                        <span>Sisa</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($employees as $employee)
                            @php
                                $empBalances = $balances[$employee->id] ?? collect();
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4 sticky left-0 bg-white dark:bg-gray-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                            <img src="{{ $employee->profile_photo_url }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $employee->name }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $employee->jabatan ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                @foreach($leaveTypes as $lt)
                                    @php
                                        $bal = $empBalances->firstWhere('leave_type_id', $lt->id);
                                    @endphp
                                    <td class="px-2 py-4 text-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $bal ? $bal->quota : '-' }}
                                    </td>
                                    <td class="px-2 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ $bal ? $bal->used : '-' }}
                                    </td>
                                    <td class="px-2 py-4 text-center">
                                        @if($bal)
                                            <form method="POST" action="{{ route('admin.leave-balances.update', $bal->id) }}" class="inline-flex items-center gap-1.5">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="remaining" value="{{ $bal->remaining }}" min="0"
                                                    class="w-16 text-center text-sm font-bold rounded-lg border-2 border-sage/50 dark:border-sage/30 bg-sage/5 dark:bg-sage/10 dark:text-white py-1.5 focus:ring-2 focus:ring-sage focus:border-sage transition-all">
                                                <button type="submit" class="w-7 h-7 rounded-lg bg-sage/30 text-emerald-700 dark:text-emerald-400 hover:bg-sage/50 flex items-center justify-center transition-all shadow-sm" title="Simpan perubahan sisa cuti">
                                                    <span class="material-icons-round text-[16px]">save</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-300 text-xs">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 1 + ($leaveTypes->count() * 3) }}" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                            <span class="material-icons-round text-3xl">event_available</span>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada data saldo cuti.</p>
                                        <p class="text-xs text-gray-400 mt-1">Klik "Inisialisasi" untuk membuat saldo cuti karyawan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $employees->links() }}
            </div>
        </div>
    </div>

@endsection
