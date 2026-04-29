@extends('layouts.absensi')

@section('content')
    <div x-data class="space-y-6">
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
                Ajukan Izin
            </button>
        </div>

        @if(isset($balances) && $balances->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($balances as $balance)
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm">
                    <p class="text-xs font-bold text-text-secondary uppercase tracking-wider">{{ $balance->leaveType->name ?? '-' }}</p>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-text-primary">{{ $balance->remaining }}</span>
                        <span class="text-sm text-text-secondary">/ {{ $balance->quota }} hari</span>
                    </div>
                    <div class="mt-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $balance->remaining > 0 ? 'bg-pastel-sage-dark' : 'bg-pastel-rose-dark' }}"
                            style="width: {{ $balance->quota > 0 ? min(100, ($balance->remaining / $balance->quota) * 100) : 0 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <x-pastel-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-cream-dark border-b border-neutral-stone">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">Alasan</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-text-secondary uppercase tracking-wider">Status Approval</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase tracking-wider">Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-stone">
                        @forelse($riwayat as $izin)
                            <tr class="hover:bg-neutral-cream-dark/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-text-primary">
                                    {{ $izin->tanggal_mulai?->format('d M') }} s/d {{ $izin->tanggal_selesai?->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-text-primary">
                                    <span class="capitalize font-medium">{{ $izin->jenis }}</span>
                                    @if($izin->jenis === 'wfa' && $izin->wfa_location)
                                        <span class="block text-xs text-text-secondary">{{ $izin->wfa_location }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-text-secondary max-w-xs truncate">
                                    {{ $izin->alasan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        @php
                                            $steps = ['pending' => 'Pending', 'approved_l1' => 'L1', 'approved_l2' => 'L2', 'approved_final' => 'Final'];
                                            $statusOrder = ['pending', 'approved_l1', 'approved_l2', 'approved_final'];
                                            $currentIdx = array_search($izin->approval_status, $statusOrder);
                                            $isRejected = $izin->approval_status === 'rejected';
                                        @endphp

                                        @if($isRejected)
                                            <span class="px-3 py-1 text-xs font-bold text-pastel-rose-dark bg-pastel-rose/20 rounded-full">Ditolak</span>
                                        @else
                                            @foreach($statusOrder as $idx => $step)
                                                @if($idx <= $currentIdx)
                                                    <span class="w-6 h-6 rounded-full bg-pastel-sage-dark text-white text-[10px] font-bold flex items-center justify-center">
                                                        @if($step === 'approved_final')
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                        @else
                                                            {{ $idx + 1 }}
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-600 text-slate-400 text-[10px] font-bold flex items-center justify-center">{{ $idx + 1 }}</span>
                                                @endif
                                                @if(!$loop->last)
                                                    <span class="w-4 h-0.5 {{ $idx < $currentIdx ? 'bg-pastel-sage-dark' : 'bg-slate-200 dark:bg-slate-600' }}"></span>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    @if($izin->currentApprover && !$isRejected && $izin->approval_status !== 'approved_final')
                                        <p class="text-[10px] text-text-secondary mt-1">Menunggu: {{ $izin->currentApprover->name }}</p>
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

    <x-pastel-modal name="izin-modal" title="Form Pengajuan Izin" maxWidth="lg">
        <div x-data="{
            selectedJenis: 'izin',
            selectedLeaveTypeId: '',
            leaveBalance: null,
            loadingBalance: false,
            async fetchBalance() {
                if (!this.selectedLeaveTypeId) { this.leaveBalance = null; return; }
                this.loadingBalance = true;
                try {
                    const res = await fetch('{{ route('izin.balance') }}?leave_type_id=' + this.selectedLeaveTypeId);
                    this.leaveBalance = await res.json();
                } catch (e) { this.leaveBalance = null; }
                this.loadingBalance = false;
            }
        }">
            <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="jenis" value="Jenis Izin" />
                    <select id="jenis" name="jenis" x-model="selectedJenis"
                        class="block w-full mt-1 border-neutral-stone rounded-xl shadow-sm focus:border-pastel-mint focus:ring focus:ring-pastel-mint/20 bg-white">
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                        <option value="dinas">Dinas</option>
                        <option value="wfa">Work From Anywhere (WFA)</option>
                    </select>
                </div>

                <div x-show="selectedJenis === 'cuti'" x-transition>
                    <x-input-label value="Tipe Cuti" />
                    <select name="leave_type_id" x-model="selectedLeaveTypeId" @change="fetchBalance()"
                        class="block w-full mt-1 border-neutral-stone rounded-xl shadow-sm focus:border-pastel-mint focus:ring focus:ring-pastel-mint/20 bg-white">
                        <option value="">-- Pilih Tipe Cuti --</option>
                        @foreach($leaveTypes ?? [] as $lt)
                            <option value="{{ $lt->id }}">{{ $lt->name }} ({{ $lt->days_quota }} hari)</option>
                        @endforeach
                    </select>
                    <div x-show="leaveBalance" x-transition class="mt-2 rounded-xl border border-pastel-sage/30 bg-pastel-sage/10 p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-text-secondary">Sisa Cuti:</span>
                            <span class="font-bold text-text-primary" x-text="leaveBalance?.remaining + ' hari'"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-text-muted">Kuota: <span x-text="leaveBalance?.quota"></span></span>
                            <span class="text-text-muted">Terpakai: <span x-text="leaveBalance?.used"></span></span>
                        </div>
                    </div>
                </div>

                <div x-show="selectedJenis === 'wfa'" x-transition>
                    <x-input-label value="Lokasi WFA" />
                    <x-text-input type="text" name="wfa_location" placeholder="Contoh: Rumah, Cafe XYZ"
                        class="block w-full mt-1" :required="false" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="tanggal_mulai" value="Tanggal Mulai" />
                        <x-text-input id="tanggal_mulai" type="date" name="tanggal_mulai" class="block w-full mt-1" required />
                    </div>
                    <div>
                        <x-input-label for="tanggal_selesai" value="Tanggal Selesai" />
                        <x-text-input id="tanggal_selesai" type="date" name="tanggal_selesai" class="block w-full mt-1" required />
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
                        class="px-4 py-2 bg-pastel-rose hover:bg-pastel-rose-dark text-text-primary font-bold rounded-xl shadow-soft">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </x-pastel-modal>
@endsection
