@extends('layouts.absensi')

@section('content')
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-text-primary">Laporan Absensi</h1>
                <p class="text-text-secondary mt-1">Pantau kehadiran seluruh karyawan.</p>
            </div>
            <!-- EXPORT BUTTON (Placeholder for future) -->
            <!-- 
            <a href="#" class="px-4 py-2 bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary font-bold rounded-xl transition shadow-soft flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
            -->
        </div>

        <!-- MAIN CARD -->
        <x-pastel-card>
            <!-- FILTER BAR -->
            <form method="GET" action="{{ route('admin.attendance.index') }}"
                class="mb-6 p-4 bg-neutral-stone/10 rounded-xl border border-neutral-stone/20">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Mulai Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="w-full px-4 py-2 rounded-xl border border-neutral-stone bg-white focus:ring-2 focus:ring-pastel-sage/50 focus:border-pastel-sage transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="w-full px-4 py-2 rounded-xl border border-neutral-stone bg-white focus:ring-2 focus:ring-pastel-sage/50 focus:border-pastel-sage transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Karyawan</label>
                        <select name="user_id"
                            class="w-full px-4 py-2 rounded-xl border border-neutral-stone bg-white focus:ring-2 focus:ring-pastel-sage/50 focus:border-pastel-sage transition text-sm">
                            <option value="">Semua Karyawan</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full px-4 py-2 bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary font-bold rounded-xl transition shadow-soft flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter Data
                        </button>
                    </div>
                </div>
            </form>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-neutral-cream-dark border-b border-neutral-stone">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-text-secondary uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 font-semibold text-text-secondary uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-3 font-semibold text-text-secondary uppercase tracking-wider text-center">Jam
                                Masuk</th>
                            <th class="px-6 py-3 font-semibold text-text-secondary uppercase tracking-wider text-center">Jam
                                Keluar</th>
                            <th class="px-6 py-3 font-semibold text-text-secondary uppercase tracking-wider text-center">
                                Status</th>
                            <th class="px-6 py-3 font-semibold text-text-secondary uppercase tracking-wider text-center">
                                Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-stone">
                        @forelse($attendances as $row)
                            <tr class="hover:bg-neutral-cream-dark/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-text-primary">
                                    {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-text-primary">{{ $row->user->name ?? '-' }}</div>
                                    <div class="text-xs text-text-secondary">{{ $row->user->username ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($row->jam_masuk)
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') }}</div>
                                        @if($row->status === 'terlambat')
                                            <span
                                                class="text-xs text-pastel-rose-dark bg-pastel-rose/20 px-2 py-0.5 rounded-full">Terlambat</span>
                                        @endif
                                    @else - @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($row->jam_keluar)
                                        <div class="font-medium">
                                            {{ \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') }}
                                        </div>
                                    @else - @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if(in_array($row->status, ['izin', 'sakit', 'cuti', 'dinas']))
                                        <span
                                            class="px-3 py-1 text-xs font-bold text-pastel-rose-dark bg-pastel-rose/20 rounded-full capitalize">{{ $row->status }}</span>
                                    @elseif($row->jam_keluar)
                                        <span
                                            class="px-3 py-1 text-xs font-bold text-pastel-sage-dark bg-pastel-sage/20 rounded-full">Hadir</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs font-bold text-pastel-sun-dark bg-pastel-sun/20 rounded-full">Belum
                                            Pulang</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs">
                                    <div class="flex flex-col gap-1 items-center">
                                        @if($row->lat_in && $row->long_in)
                                            <a href="https://www.google.com/maps?q={{ $row->lat_in }},{{ $row->long_in }}"
                                                target="_blank"
                                                class="text-pastel-sky-dark hover:underline flex items-center gap-1 font-medium bg-pastel-sky/10 px-2 py-1 rounded-lg w-max">
                                                📍 Masuk
                                            </a>
                                        @endif
                                        @if($row->lat_out && $row->long_out)
                                            <a href="https://www.google.com/maps?q={{ $row->lat_out }},{{ $row->long_out }}"
                                                target="_blank"
                                                class="text-pastel-rose-dark hover:underline flex items-center gap-1 font-medium bg-pastel-rose/10 px-2 py-1 rounded-lg w-max">
                                                📍 Keluar
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-text-secondary">
                                    Tidak ada data absensi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        </x-pastel-card>
    </div>
@endsection