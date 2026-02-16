@extends('layouts.admin')

@section('header-title', 'Absensi')
@section('header-subtitle', 'Menampilkan Rekap & Data Absensi Masuk Seluruh Karyawan')

@section('content')

    <!-- FILTERS -->
    <form method="GET" action="{{ route('admin.attendance') }}"
        class="mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-white dark:bg-card-dark p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="md:col-span-3 relative">
            <span
                class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input name="q" value="{{ request('q') }}"
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white"
                placeholder="Cari karyawan..." type="text" />
        </div>
        <div class="md:col-span-2">
            <select name="status"
                class="w-full py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">
                <option value="">Semua Status</option>
                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat
                </option>
                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="dinas" {{ request('status') == 'dinas' ? 'selected' : '' }}>Dinas</option>
            </select>
        </div>
        <div class="md:col-span-4 flex items-center gap-2">
            <div class="flex-1 relative">
                <span
                    class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">calendar_today</span>
                <input name="start_date"
                    value="{{ request('start_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white"
                    type="date" />
            </div>
            <span class="text-slate-400">/</span>
            <div class="flex-1 relative">
                <span
                    class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">calendar_today</span>
                <input name="end_date"
                    value="{{ request('end_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white"
                    type="date" />
            </div>
        </div>
        <div class="md:col-span-3 flex justify-end gap-2">
            <button type="submit"
                class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-all shadow-sm active:scale-95">
                <span class="material-icons-round">filter_alt</span>
                Filter
            </button>
            <!-- TODO: Implement Export -->
            <button type="button"
                class="bg-sage hover:bg-[#b8c6a7] text-slate-800 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-all shadow-md active:scale-95 opacity-50 cursor-not-allowed">
                <span class="material-icons-round">file_download</span>
                Export
            </button>
        </div>
    </form>

    <!-- TABLE -->
    <div
        class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            No</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Nama</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Tanggal</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Jam Masuk</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Jam Keluar</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Jam Kerja</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">
                            Status</th>
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    {{-- 1. RENDER IZIN ROWS (PINNED TOP) --}}
                    @foreach($izins as $izin)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors bg-orange-50/50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-500 dark:text-slate-400">-</td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">
                                {{ $izin->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }}
                                @if($izin->tanggal_mulai != $izin->tanggal_selesai)
                                    - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-400">-</td>
                            <td class="px-6 py-4 text-sm text-slate-400">-</td>
                            <td class="px-6 py-4 text-sm text-slate-400">-</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-lavender text-slate-800 px-4 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                        <span class="material-icons-round text-sm">history_edu</span>
                                        {{ ucfirst($izin->jenis) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300 italic font-medium">
                                {{ $izin->alasan }}</td>
                        </tr>
                    @endforeach

                    {{-- 2. RENDER ATTENDANCE ROWS --}}
                    @forelse($attendances as $index => $row)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                                {{ $attendances->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">
                                {{ $row->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>

                            {{-- Jam Masuk --}}
                            <td class="px-6 py-4">
                                @if($row->jam_masuk)
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') }}
                                            WIB</span>
                                        @if($row->status === 'terlambat')
                                            <span
                                                class="inline-block mt-1 px-2 py-0.5 bg-peach text-slate-800 text-[10px] font-bold rounded uppercase tracking-tighter w-fit">Terlambat</span>
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Jam Keluar --}}
                            <td class="px-6 py-4">
                                @if($row->jam_keluar)
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') }}
                                            WIB</span>
                                        @if($row->kegiatan === 'hadir_lembur')
                                            <span
                                                class="inline-block mt-1 px-2 py-0.5 bg-sky text-slate-800 text-[10px] font-bold rounded uppercase tracking-tighter w-fit">Lembur</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>

                            {{-- Jam Kerja --}}
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">
                                @if($row->jam_masuk && $row->jam_keluar)
                                    @php
                                        $start = \Carbon\Carbon::parse($row->jam_masuk);
                                        $end = \Carbon\Carbon::parse($row->jam_keluar);
                                        if ($end->lessThan($start))
                                            $end->addDay();
                                        $diff = $start->diff($end);
                                    @endphp
                                    {{ $diff->h }} Jam {{ $diff->i }} menit
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                <div class="flex justify-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-sage text-slate-800 px-4 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                        <span class="material-icons-round text-sm">check_circle</span>
                                        Hadir
                                    </span>
                                </div>
                            </td>

                            {{-- Keterangan --}}
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">-</td>
                        </tr>
                    @empty
                        @if($izins->isEmpty())
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-slate-500 italic">
                                    Tidak ada data absensi untuk periode ini.
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="p-6">
            {{ $attendances->links() }}
        </div>
    </div>
@endsection