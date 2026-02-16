@extends('layouts.absensi')

@section('content')
    <div class="space-y-6">
        <!-- HEADER -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-text-primary">Riwayat Izin/Sakit</h1>
                <p class="text-text-secondary mt-1">Daftar pengajuan ketidakhadiran Anda.</p>
            </div>
            <button @click="$dispatch('open-modal', 'izin-modal')"
                class="px-4 py-2 bg-pastel-rose hover:bg-pastel-rose-dark text-text-primary font-bold rounded-xl transition shadow-soft flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Ajukan Izin Total
            </button>
        </div>

        <!-- MAIN CARD -->
        <x-pastel-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-cream-dark border-b border-neutral-stone">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Alasan</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">
                                Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-stone">
                        @forelse($riwayat as $izin)
                            <tr class="hover:bg-neutral-cream-dark/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-text-primary">
                                    {{ $izin->tanggal_mulai }} s/d {{ $izin->tanggal_selesai }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-text-primary capitalize">
                                    {{ $izin->jenis }}
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-text-secondary">
                                    Belum ada riwayat pengajuan izin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $riwayat->links() }}
            </div>
        </x-pastel-card>
    </div>

    <!-- MODAL PENGAJUAN IZIN (Reusing logic from dashboard if needed or creating new) -->
    <x-pastel-modal name="izin-modal" title="Form Pengajuan Izin" maxWidth="lg">
        <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <x-input-label for="jenis" value="Jenis Izin" />
                <select id="jenis" name="jenis"
                    class="block w-full mt-1 border-neutral-stone rounded-xl shadow-sm focus:border-pastel-mint focus:ring focus:ring-pastel-mint/20 bg-white">
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="cuti">Cuti</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="tanggal_mulai" value="Tanggal Mulai" />
                    <x-text-input id="tanggal_mulai" type="date" name="tanggal_mulai" class="block w-full mt-1" required />
                </div>
                <div>
                    <x-input-label for="tanggal_selesai" value="Tanggal Selesai" />
                    <x-text-input id="tanggal_selesai" type="date" name="tanggal_selesai" class="block w-full mt-1"
                        required />
                </div>
            </div>
            <div>
                <x-input-label for="alasan" value="Alasan" />
                <textarea id="alasan" name="alasan" rows="3"
                    class="block w-full mt-1 border-neutral-stone rounded-xl shadow-sm focus:border-pastel-mint focus:ring focus:ring-pastel-mint/20 bg-white"
                    required></textarea>
            </div>
            <div>
                <x-input-label for="dokumen" value="Bukti Pendukung (Opsional)" />
                <input id="dokumen" type="file" name="dokumen"
                    class="block w-full mt-1 text-sm text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pastel-lavender file:text-pastel-lavender-dark hover:file:bg-pastel-lavender-dark/10" />
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="$dispatch('close-modal', 'izin-modal')"
                    class="px-4 py-2 text-text-secondary hover:bg-neutral-stone/20 rounded-xl transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-pastel-rose hover:bg-pastel-rose-dark text-text-primary font-bold rounded-xl shadow-soft">Kirim
                    Pengajuan</button>
            </div>
        </form>
    </x-pastel-modal>
@endsection