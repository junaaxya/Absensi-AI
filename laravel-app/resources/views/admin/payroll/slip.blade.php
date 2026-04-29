@extends('layouts.admin')

@section('header-title', 'Slip Gaji — ' . $detail->user->name)
@section('header-subtitle', \Carbon\Carbon::createFromFormat('Y-m', $period->period_month)->translatedFormat('F Y'))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .mono { font-family: 'JetBrains Mono', monospace; }
    @media print {
        aside, header, nav, .no-print { display: none !important; }
        .md\:ml-64 { margin-left: 0 !important; }
        main { padding: 0 !important; }
        body { background: white !important; }
        .print-container { box-shadow: none !important; border: none !important; }
    }
</style>
@endpush

@section('content')
<div class="mb-4 flex items-center justify-between no-print">
    <a href="{{ route('admin.payroll.show', $period) }}"
        class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium transition-colors">
        <span class="material-icons-round text-[18px]">arrow_back</span>
        Kembali
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.payroll.slip.download', [$period, $detail->user]) }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition-all">
            <span class="material-icons-round text-[16px]">download</span>
            Download PDF
        </a>
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition-all">
            <span class="material-icons-round text-[16px]">print</span>
            Cetak
        </button>
    </div>
</div>

<div class="max-w-3xl mx-auto bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-8 print-container">
    <div class="text-center mb-6 pb-4 border-b-2 border-slate-900 dark:border-white">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white uppercase tracking-wide">
            {{ $settings->company_name ?? 'Perusahaan' }}
        </h1>
        @if($settings->company_address)
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $settings->company_address }}</p>
        @endif
        <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-3 uppercase tracking-widest">Slip Gaji</p>
        <p class="text-xs text-slate-500">Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $period->period_month)->translatedFormat('F Y') }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
        <div class="space-y-1">
            <div class="flex"><span class="w-28 text-slate-500 font-medium">Nama</span><span class="text-slate-900 dark:text-white font-bold">: {{ $detail->user->name }}</span></div>
            <div class="flex"><span class="w-28 text-slate-500 font-medium">NIK</span><span class="text-slate-900 dark:text-white">: {{ $detail->user->nik ?? '-' }}</span></div>
            <div class="flex"><span class="w-28 text-slate-500 font-medium">Departemen</span><span class="text-slate-900 dark:text-white">: {{ $detail->user->department->name ?? '-' }}</span></div>
        </div>
        <div class="space-y-1">
            <div class="flex"><span class="w-28 text-slate-500 font-medium">Jabatan</span><span class="text-slate-900 dark:text-white">: {{ $detail->user->jabatan ?? '-' }}</span></div>
            <div class="flex"><span class="w-28 text-slate-500 font-medium">Status</span><span class="text-slate-900 dark:text-white">: {{ ucfirst($detail->user->status_karyawan ?? '-') }}</span></div>
            <div class="flex"><span class="w-28 text-slate-500 font-medium">Hari Kerja</span><span class="text-slate-900 dark:text-white">: {{ $detail->present_days }}/{{ $detail->working_days }} hari</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 pb-1 border-b border-slate-200 dark:border-slate-700">Pendapatan</h4>
            <div class="space-y-2">
                @foreach($detail->items->where('component_type', 'earning') as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 dark:text-slate-400">{{ $item->component_name }}</span>
                    <span class="mono font-medium text-slate-900 dark:text-white">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                </div>
                @endforeach
                <div class="flex justify-between text-sm font-bold pt-2 border-t border-slate-200 dark:border-slate-700">
                    <span class="text-slate-900 dark:text-white">Total Pendapatan</span>
                    <span class="mono text-green-700 dark:text-green-400">Rp {{ number_format($detail->total_earnings, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 pb-1 border-b border-slate-200 dark:border-slate-700">Potongan</h4>
            <div class="space-y-2">
                @foreach($detail->items->where('component_type', 'deduction') as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 dark:text-slate-400">{{ $item->component_name }}</span>
                    <span class="mono font-medium text-red-600 dark:text-red-400">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                </div>
                @endforeach
                <div class="flex justify-between text-sm font-bold pt-2 border-t border-slate-200 dark:border-slate-700">
                    <span class="text-slate-900 dark:text-white">Total Potongan</span>
                    <span class="mono text-red-600 dark:text-red-400">Rp {{ number_format($detail->total_deductions, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 pb-1 border-b border-slate-200 dark:border-slate-700">BPJS</h4>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ditanggung Perusahaan</p>
                @foreach($detail->items->where('component_type', 'bpjs_company') as $item)
                <div class="flex justify-between text-sm py-0.5">
                    <span class="text-slate-600 dark:text-slate-400">{{ $item->component_name }}</span>
                    <span class="mono text-slate-700 dark:text-slate-300">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                </div>
                @endforeach
                <div class="flex justify-between text-sm font-bold pt-1 border-t border-slate-100 dark:border-slate-700 mt-1">
                    <span>Total</span>
                    <span class="mono">Rp {{ number_format($detail->total_bpjs_company, 0, ',', '.') }}</span>
                </div>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ditanggung Karyawan</p>
                @foreach($detail->items->where('component_type', 'bpjs_employee') as $item)
                <div class="flex justify-between text-sm py-0.5">
                    <span class="text-slate-600 dark:text-slate-400">{{ $item->component_name }}</span>
                    <span class="mono text-red-600 dark:text-red-400">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                </div>
                @endforeach
                <div class="flex justify-between text-sm font-bold pt-1 border-t border-slate-100 dark:border-slate-700 mt-1">
                    <span>Total</span>
                    <span class="mono text-red-600 dark:text-red-400">Rp {{ number_format($detail->total_bpjs_employee, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 pb-1 border-b border-slate-200 dark:border-slate-700">PPh 21</h4>
        <div class="flex justify-between text-sm">
            <span class="text-slate-600 dark:text-slate-400">Pajak Penghasilan Pasal 21</span>
            <span class="mono font-bold text-red-600 dark:text-red-400">Rp {{ number_format($detail->pph21_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="bg-slate-900 dark:bg-slate-700 rounded-2xl p-5 text-center">
        <p class="text-xs text-slate-400 uppercase tracking-widest mb-1">Gaji Bersih (Take Home Pay)</p>
        <p class="text-3xl font-bold text-white mono">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</p>
    </div>

    <div class="mt-6 text-center text-[10px] text-slate-400">
        Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} — Dokumen ini bersifat rahasia
    </div>
</div>
@endsection
