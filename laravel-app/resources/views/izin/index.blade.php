@extends('layouts.employee-dashboard')

@section('content')
    <div x-data="{}" class="space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Riwayat Izin & Cuti</h2>
                <p class="text-slate-500 dark:text-slate-400">Daftar pengajuan ketidakhadiran Anda.</p>
            </div>
            <div class="flex items-center gap-4">
                <button @click="$dispatch('open-modal', 'izin-modal')" class="flex items-center gap-2 rounded-xl bg-primary px-4 py-2 font-bold text-white shadow-sm hover:bg-emerald-600 transition-colors">
                    <span class="material-symbols-outlined">add</span>
                    Ajukan Izin
                </button>
                <button onclick="document.documentElement.classList.toggle('dark')" class="w-10 h-10 rounded-full flex items-center justify-center bg-card-light dark:bg-card-dark border border-slate-200 dark:border-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-800">
                    <span class="material-symbols-outlined">dark_mode</span>
                </button>
            </div>
        </div>

        <!-- MAIN CARD -->
        <section class="bg-card-light dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Tanggal</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Jenis</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Alasan</th>
                            <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @forelse($riwayat as $izin)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-slate-800 dark:text-slate-200">
                                    {{ $izin->tanggal_mulai }} <span class="text-slate-400 font-normal">s/d</span> {{ $izin->tanggal_selesai }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 capitalize">
                                    {{ $izin->jenis }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 max-w-xs truncate">
                                    {{ $izin->alasan }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($izin->status == 'approved')
                                        <span class="px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase">Disetujui</span>
                                    @elseif($izin->status == 'rejected')
                                        <span class="px-2 py-1 rounded-full bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 text-[10px] font-bold uppercase">Ditolak</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-[10px] font-bold uppercase">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($izin->dokumen)
                                        <a href="{{ asset('storage/' . $izin->dokumen) }}" target="_blank"
                                            class="text-primary hover:underline font-medium">Lihat</a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada riwayat pengajuan izin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($riwayat->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $riwayat->links() }}
                </div>
            @endif
        </section>

        <!-- Modal Pengajuan Izin (Alpine) -->
        <div x-data="{ show: false }"
            @open-modal.window="if ($event.detail === 'izin-modal') { show = true }"
            @close-modal.window="if ($event.detail === 'izin-modal') { show = false }"
            x-show="show" x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/80 p-4 backdrop-blur-sm">
            
            <div x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.outside="$dispatch('close-modal', 'izin-modal')"
                class="relative w-full max-w-lg transform overflow-hidden rounded-3xl bg-card-light dark:bg-card-dark text-left shadow-xl transition-all border border-slate-200 dark:border-slate-700">
                
                <div class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Form Pengajuan Izin</h3>
                    <button @click="$dispatch('close-modal', 'izin-modal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 p-6">
                    @csrf
                    <div>
                        <label for="jenis" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Izin</label>
                        <select id="jenis" name="jenis"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm">
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="cuti">Cuti</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai</label>
                            <input id="tanggal_mulai" type="date" name="tanggal_mulai" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm" required />
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai</label>
                            <input id="tanggal_selesai" type="date" name="tanggal_selesai" class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm" required />
                        </div>
                    </div>
                    <div>
                        <label for="alasan" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan</label>
                        <textarea id="alasan" name="alasan" rows="3"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 focus:border-primary focus:ring focus:ring-primary/20 sm:text-sm"
                            required></textarea>
                    </div>
                    <div>
                        <label for="dokumen" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Bukti Pendukung (Opsional)</label>
                        <input id="dokumen" type="file" name="dokumen"
                            class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 dark:file:bg-primary/20 dark:hover:file:bg-primary/30 transition-colors" />
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="$dispatch('close-modal', 'izin-modal')"
                            class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">Kirim
                            Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
