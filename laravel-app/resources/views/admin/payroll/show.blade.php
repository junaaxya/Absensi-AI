@extends('layouts.admin')

@section('header-title', 'Detail Payroll — ' . \Carbon\Carbon::createFromFormat('Y-m', $period->period_month)->translatedFormat('F Y'))
@section('header-subtitle', 'Rincian perhitungan gaji periode ini')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.payroll.index') }}"
        class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium transition-colors">
        <span class="material-icons-round text-[18px]">arrow_back</span>
        Kembali ke Daftar
    </a>

    <div class="flex items-center gap-2">
        @if($period->status === 'draft')
        <form method="POST" action="{{ route('admin.payroll.calculate', $period) }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-peach/50 text-orange-800 rounded-xl text-sm font-bold hover:bg-peach transition-all">
                <span class="material-icons-round text-[16px]">calculate</span>
                Hitung Payroll
            </button>
        </form>
        @endif

        @if($period->status === 'calculated')
        <form method="POST" action="{{ route('admin.payroll.approve', $period) }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-primary/50 text-green-800 rounded-xl text-sm font-bold hover:bg-primary transition-all">
                <span class="material-icons-round text-[16px]">check_circle</span>
                Setujui
            </button>
        </form>
        @endif

        @if($period->status === 'approved')
        <form method="POST" action="{{ route('admin.payroll.mark-paid', $period) }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-200/60 text-green-800 rounded-xl text-sm font-bold hover:bg-green-200 transition-all">
                <span class="material-icons-round text-[16px]">paid</span>
                Tandai Lunas
            </button>
        </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl bg-sky/30 flex items-center justify-center">
                <span class="material-icons-round text-blue-700 text-[18px]">groups</span>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Karyawan</p>
        </div>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($period->total_employees) }}</p>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl bg-primary/30 flex items-center justify-center">
                <span class="material-icons-round text-green-700 text-[18px]">trending_up</span>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Bruto</p>
        </div>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($period->total_gross, 0, ',', '.') }}</p>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl bg-peach/30 flex items-center justify-center">
                <span class="material-icons-round text-orange-700 text-[18px]">trending_down</span>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Potongan</p>
        </div>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($period->total_deductions, 0, ',', '.') }}</p>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl bg-lavender/30 flex items-center justify-center">
                <span class="material-icons-round text-purple-700 text-[18px]">account_balance_wallet</span>
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Netto</p>
        </div>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($period->total_net, 0, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden" x-data="{ expandedRow: null }">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-bold text-slate-900 dark:text-white">Detail Karyawan</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                    <th class="px-4 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Dept</th>
                    <th class="px-4 py-3 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Gaji Pokok</th>
                    <th class="px-4 py-3 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Earnings</th>
                    <th class="px-4 py-3 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Deductions</th>
                    <th class="px-4 py-3 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">BPJS</th>
                    <th class="px-4 py-3 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">PPh 21</th>
                    <th class="px-4 py-3 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Netto</th>
                    <th class="px-4 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($period->payrollDetails as $detail)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
                    @click="expandedRow = expandedRow === {{ $detail->id }} ? null : {{ $detail->id }}">
                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">{{ $detail->user->name }}</td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $detail->user->department->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-right text-slate-700 dark:text-slate-300">Rp {{ number_format($detail->gaji_pokok, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-green-700 dark:text-green-400 font-medium">Rp {{ number_format($detail->total_earnings, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-red-600 dark:text-red-400 font-medium">Rp {{ number_format($detail->total_deductions, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-slate-700 dark:text-slate-300">Rp {{ number_format($detail->total_bpjs_employee, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-slate-700 dark:text-slate-300">Rp {{ number_format($detail->pph21_amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center" @click.stop>
                        <a href="{{ route('admin.payroll.slip', [$period, $detail->user]) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-lavender/40 text-purple-800 dark:text-purple-200 rounded-lg text-xs font-bold hover:bg-lavender/60 transition-all">
                            <span class="material-icons-round text-[14px]">receipt</span>
                            Slip
                        </a>
                    </td>
                </tr>

                <tr x-show="expandedRow === {{ $detail->id }}" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100">
                    <td colspan="9" class="px-4 py-4 bg-slate-50/80 dark:bg-slate-800/50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                            <div>
                                <p class="font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Pendapatan</p>
                                @foreach($detail->items->where('component_type', 'earning') as $item)
                                <div class="flex justify-between py-1">
                                    <span class="text-slate-600 dark:text-slate-400">{{ $item->component_name }}</span>
                                    <span class="font-medium text-slate-900 dark:text-white">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div>
                                <p class="font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Potongan</p>
                                @foreach($detail->items->where('component_type', 'deduction') as $item)
                                <div class="flex justify-between py-1">
                                    <span class="text-slate-600 dark:text-slate-400">{{ $item->component_name }}</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                @foreach($detail->items->where('component_type', 'bpjs_employee') as $item)
                                <div class="flex justify-between py-1">
                                    <span class="text-slate-600 dark:text-slate-400">{{ $item->component_name }}</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                @foreach($detail->items->where('component_type', 'tax') as $item)
                                <div class="flex justify-between py-1">
                                    <span class="text-slate-600 dark:text-slate-400">{{ $item->component_name }}</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div>
                                <p class="font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">BPJS Perusahaan</p>
                                @foreach($detail->items->where('component_type', 'bpjs_company') as $item)
                                <div class="flex justify-between py-1">
                                    <span class="text-slate-600 dark:text-slate-400">{{ $item->component_name }}</span>
                                    <span class="font-medium text-slate-900 dark:text-white">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-700">
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">Info Kehadiran</p>
                                    <p class="text-slate-600 dark:text-slate-400">Hari kerja: {{ $detail->working_days }} | Hadir: {{ $detail->present_days }}</p>
                                    <p class="text-slate-600 dark:text-slate-400">Terlambat: {{ $detail->late_count }}x ({{ $detail->late_minutes_total }} menit)</p>
                                    <p class="text-slate-600 dark:text-slate-400">Lembur: {{ $detail->overtime_hours }} jam</p>
                                    @if($detail->prorate_reason)
                                    <p class="text-orange-600 dark:text-orange-400 mt-1">Prorata: {{ $detail->prorate_reason }} ({{ number_format($detail->prorate_factor * 100, 2) }}%)</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2 block">receipt_long</span>
                        Belum ada data payroll. Klik "Hitung Payroll" untuk memulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
