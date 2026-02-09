@extends('layouts.absensi')

@section('content')
<div x-data="{ izinOpen: false }">

    <!-- HEADER CARD -->
    <div class="bg-gradient-to-r from-pastel-sage/20 to-pastel-sky/20 p-6 rounded-2xl border border-neutral-stone/30 mb-6">
        <h1 class="text-2xl font-bold text-text-primary">Hallo, {{ $user->name }}</h1>
        <p class="text-text-secondary mt-1">Ringkasan Absensi hari ini</p>
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-[2fr_420px] gap-6 mb-6">
        
        <!-- LEFT: Activity Timeline -->
        <x-pastel-card header="Aktivitas Hari Ini">
            <div class="relative pl-8 space-y-6">
                <!-- Vertical Line -->
                <div class="absolute left-[7px] top-2 bottom-2 w-0.5 bg-neutral-stone/50"></div>

                <!-- TIMELINE: ABSENSI MASUK -->
                <div class="relative">
                    <!-- Dot -->
                    <div class="absolute left-[-23px] top-1 w-4 h-4 rounded-full bg-pastel-sage border-2 border-white shadow-sm z-10"></div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center min-w-[50px] h-7 px-3 text-sm font-medium rounded-lg bg-neutral-stone/50 text-text-primary">
                                @if($attendanceToday && $attendanceToday->jam_masuk)
                                    @php
                                        $rawMasuk = trim($attendanceToday->jam_masuk);
                                        if (strlen($rawMasuk) > 8) {
                                            $jamMasukText = substr($rawMasuk, 11, 5);
                                        } else {
                                            $jamMasukText = substr($rawMasuk, 0, 5);
                                        }
                                    @endphp
                                    {{ $jamMasukText }}
                                @else
                                    −
                                @endif
                            </span>
                            <strong class="text-text-primary">Absensi Masuk</strong>
                        </div>

                        <div class="px-4 py-2 rounded-xl text-sm font-medium
                            @if($attendanceToday && $attendanceToday->jam_masuk)
                                {{ $attendanceToday->status === 'terlambat' ? 'bg-pastel-rose/30 text-red-700' : 'bg-pastel-sage/20 text-green-800' }}
                            @else
                                bg-pastel-peach/30 text-amber-700
                            @endif
                        ">
                            @if(!$attendanceToday || !$attendanceToday->jam_masuk)
                                ⚠️ Absen Masuk Belum dilakukan
                            @else
                                @if($attendanceToday->status === 'terlambat')
                                    Terlambat ({{ $jamMasukText }})
                                @else
                                    Tepat waktu ({{ $jamMasukText }})
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TIMELINE: ABSEN KELUAR -->
                <div class="relative">
                    <!-- Dot -->
                    <div class="absolute left-[-23px] top-1 w-4 h-4 rounded-full bg-pastel-sage border-2 border-white shadow-sm z-10"></div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center min-w-[50px] h-7 px-3 text-sm font-medium rounded-lg bg-neutral-stone/50 text-text-primary">−</span>
                            <strong class="text-text-primary">Absen Keluar</strong>
                        </div>

                        <div class="px-4 py-2 rounded-xl text-sm font-medium bg-neutral-stone/30 text-text-secondary">
                            ⏳ Belum dilakukan
                        </div>
                    </div>
                </div>

            </div>
        </x-pastel-card>
        
        <!-- RIGHT: Work Hours + Actions -->
        <div class="space-y-4">
            <x-pastel-card header="Informasi Jam Kerja">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-xl border border-neutral-stone/30 text-center shadow-sm">
                        <span class="text-xs text-text-secondary uppercase tracking-wide font-semibold">Jam Masuk</span>
                        <span class="block text-2xl font-bold text-text-primary mt-1">08:00</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-neutral-stone/30 text-center shadow-sm">
                        <span class="text-xs text-text-secondary uppercase tracking-wide font-semibold">Jam Keluar</span>
                        <span class="block text-2xl font-bold text-text-primary mt-1">17:00</span>
                    </div>
                </div>
            </x-pastel-card>
            
            <x-pastel-card>
                <div class="space-y-3">
                    <!-- ABSEN MASUK -->
                    <form method="POST" action="{{ route('absen.masuk') }}">
                        @csrf
                        <button type="submit" class="w-full py-3.5 bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary font-semibold rounded-xl transition transform hover:-translate-y-0.5 shadow-soft">
                            Absen Masuk
                        </button>
                    </form>

                    <!-- ABSEN KELUAR -->
                    <form method="POST" action="{{ route('absen.keluar') }}">
                        @csrf
                        <button type="submit" class="w-full py-3.5 bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary font-semibold rounded-xl transition transform hover:-translate-y-0.5 shadow-soft">
                            Absen Keluar
                        </button>
                    </form>

                    <!-- PENGAJUAN IZIN -->
                    <button type="button" @click="$dispatch('open-modal', 'izin')" class="w-full py-3.5 bg-white border-2 border-pastel-sage text-pastel-sage-dark font-semibold rounded-xl transition transform hover:-translate-y-0.5 hover:bg-pastel-sage/10">
                        Pengajuan Ketidakhadiran
                    </button>
                </div>
            </x-pastel-card>
        </div>
    </div>

    <!-- ATTENDANCE HISTORY -->
    <x-pastel-card>
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-lg font-bold text-text-primary">Riwayat Absensi</h3>
            <form method="GET" action="{{ route('dashboard') }}" class="w-full sm:w-auto">
                <input type="date" name="tanggal" value="{{ $tanggal }}" max="{{ date('Y-m-d') }}" 
                       class="w-full sm:w-auto px-4 py-2 rounded-xl border border-neutral-stone bg-white focus:ring-2 focus:ring-pastel-sage/50 focus:border-pastel-sage transition text-sm">
            </form>
        </div>

        <div class="overflow-x-auto -mx-6 px-6">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-neutral-stone">
                        <th class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30 first:rounded-tl-lg last:rounded-tr-lg">No</th>
                        <th class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30">Nama</th>
                        <th class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30 text-center">Jam Masuk</th>
                        <th class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30 text-center">Jam Keluar</th>
                        <th class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30 text-center">Jam Kerja</th>
                        <th class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30 text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-text-secondary uppercase tracking-wider bg-neutral-stone/30">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-stone/30">
                @if(isset($attendanceHistory) && $attendanceHistory->count() > 0)
                    @foreach($attendanceHistory as $i => $row)
                        <tr class="hover:bg-neutral-warm/50 transition">
                            <td class="px-4 py-3 text-center">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                            
                            <!-- Jam Masuk -->
                            <td class="px-4 py-3 text-center">
                                @if($row->jam_masuk)
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') }}</div>
                                    @if($row->status === 'terlambat')
                                        <x-pastel-badge type="late" class="mt-1">Terlambat</x-pastel-badge>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Jam Keluar -->
                            <td class="px-4 py-3 text-center">
                                @if($row->jam_keluar)
                                    @php
                                        $raw = trim($row->jam_keluar);
                                        if (strlen($raw) > 8) {
                                            $jamKeluar = substr($raw, 11, 5);
                                        } else {
                                            $jamKeluar = substr($raw, 0, 5);
                                        }
                                        $isLembur = $jamKeluar >= '17:00';
                                    @endphp
                                    <div class="font-medium">{{ $jamKeluar }}</div>
                                    @if($isLembur)
                                        <x-pastel-badge type="overtime" class="mt-1">Lembur</x-pastel-badge>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Jam Kerja -->
                            <td class="px-4 py-3 text-center">
                                @if($row->jam_masuk && $row->jam_keluar)
                                    @php
                                        try {
                                            $rawMasuk = trim($row->jam_masuk);
                                            $jamMasuk = strlen($rawMasuk) > 8 ? substr($rawMasuk, 11, 5) : substr($rawMasuk, 0, 5);
                                            
                                            $rawKeluar = trim($row->jam_keluar);
                                            $jamKeluar = strlen($rawKeluar) > 8 ? substr($rawKeluar, 11, 5) : substr($rawKeluar, 0, 5);
                                            
                                            $start = \Carbon\Carbon::createFromFormat('H:i', $jamMasuk);
                                            $end   = \Carbon\Carbon::createFromFormat('H:i', $jamKeluar);
                                            
                                            if ($end->lessThan($start)) {
                                                $end->addDay();
                                            }
                                            
                                            $diffMinutes = $start->diffInMinutes($end);
                                            $hours   = intdiv($diffMinutes, 60);
                                            $minutes = $diffMinutes % 60;
                                            
                                            $jamKerjaText = $hours . 'j ' . $minutes . 'm';
                                        } catch (\Exception $e) {
                                            $jamKerjaText = '-';
                                        }
                                    @endphp
                                    {{ $jamKerjaText }}
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3 text-center">
                                @if(in_array($row->status, ['izin','sakit','cuti','dinas']))
                                    <x-pastel-badge type="danger">Tidak Hadir</x-pastel-badge>
                                @elseif($row->jam_keluar)
                                    <x-pastel-badge type="success">Hadir</x-pastel-badge>
                                @else
                                    <x-pastel-badge type="info">Belum Hadir</x-pastel-badge>
                                @endif
                            </td>

                            <!-- Keterangan -->
                            <td class="px-4 py-3 text-center">
                                @if(in_array($row->status, ['izin','sakit','cuti','dinas']))
                                    {{ ucfirst($row->status) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-text-secondary italic">
                            Belum ada data absensi
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </x-pastel-card>

    <!-- LEAVE REQUEST MODAL -->
    <x-pastel-modal name="izin" title="Pengajuan Ketidakhadiran" maxWidth="lg">
        <form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-1">Jenis Pengajuan</label>
                <select name="jenis" required class="w-full px-4 py-2 rounded-xl bg-white border border-neutral-stone focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition">
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="cuti">Cuti</option>
                    <option value="dinas">Dinas</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required class="w-full px-4 py-2 rounded-xl bg-white border border-neutral-stone focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" required class="w-full px-4 py-2 rounded-xl bg-white border border-neutral-stone focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-secondary mb-1">Alasan</label>
                <textarea name="alasan" required rows="3" class="w-full px-4 py-2 rounded-xl bg-white border border-neutral-stone focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-secondary mb-1">Lampirkan Foto atau Dokumen</label>
                <input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full text-sm text-text-secondary
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-xl file:border-0
                    file:text-sm file:font-semibold
                    file:bg-pastel-sage/20 file:text-pastel-sage-dark
                    hover:file:bg-pastel-sage/30
                ">
                <p class="mt-1 text-xs text-text-secondary">Format: PDF, JPG, PNG, DOCX (Max 5MB)</p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="show = false" class="px-4 py-2 text-sm font-medium text-text-secondary hover:bg-neutral-stone/30 rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary rounded-lg transition shadow-soft">
                    Ajukan
                </button>
            </div>
        </form>
    </x-pastel-modal>

</div>
@endsection
