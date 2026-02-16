                                                                @extends('layouts.absensi')

@section('content')
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-text-primary">Persetujuan Izin/Sakit</h1>
                <p class="text-text-secondary mt-1">Kelola pengajuan ketidakhadiran karyawan.</p>
            </div>
        </div>

        <!-- MAIN CARD -->
        <x-pastel-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-cream-dark border-b border-neutral-stone">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Jenis & Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Alasan</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Dokumen</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-stone">
                        @forelse($izins as $izin)
                            <tr class="hover:bg-neutral-cream-dark/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-text-primary">{{ $izin->user->name }}</div>
                                    <div class="text-xs text-text-secondary">{{ $izin->user->jabatan ?? 'Karyawan' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-text-primary">
                                    <span class="capitalize font-medium block">{{ $izin->jenis }}</span>
                                    <span class="text-xs text-text-secondary">{{ $izin->tanggal_mulai }} s/d
                                        {{ $izin->tanggal_selesai }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text-secondary max-w-xs truncate">
                                    {{ $izin->alasan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($izin->status == 'approved')
                                        <span
                                            class="px-3 py-1 text-xs font-bold text-pastel-sage-dark bg-pastel-sage/20 rounded-full">Disetujui</span>
                                    @elseif($izin->status == 'rejected')
                                        <span
                                            class="px-3 py-1 text-xs font-bold text-pastel-rose-dark bg-pastel-rose/20 rounded-full">Ditolak</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs font-bold text-pastel-sun-dark bg-pastel-sun/20 rounded-full">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($izin->dokumen)
                                        <a href="{{ asset('storage/' . $izin->dokumen) }}" target="_blank"
                                            class="text-pastel-sky-dark hover:underline font-medium">Lihat</a>
                                    @else
                                        <span class="text-text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($izin->status == 'pending')
                                        <div class="flex justify-end gap-2">
                                            <form action="{{ route('admin.izin.approve', $izin->id) }}" method="POST"
                                                onsubmit="return confirm('Setujui pengajuan ini?');">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1 bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary text-xs font-bold rounded-lg transition shadow-sm">
                                                    ✓ Setujui
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.izin.reject', $izin->id) }}" method="POST"
                                                onsubmit="return confirm('Tolak pengajuan ini?');">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1 bg-pastel-rose hover:bg-pastel-rose-dark text-text-primary text-xs font-bold rounded-lg transition shadow-sm">
                                                    ✗ Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-text-muted text-xs">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-text-secondary">
                                    Belum ada pengajuan izin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $izins->links() }}
            </div>
        </x-pastel-card>
    </div>
@endsection