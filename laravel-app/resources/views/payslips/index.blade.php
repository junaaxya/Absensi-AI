@extends('layouts.absensi')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-sage/30 flex items-center justify-center">
            <svg class="w-6 h-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Slip Gaji Saya</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Riwayat slip gaji yang telah dibayarkan.</p>
        </div>
    </div>

    @if($payslips->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($payslips as $payslip)
                @php
                    $periodDate = \Carbon\Carbon::parse($payslip->payrollPeriod->period_month . '-01');
                    $statusClass = $payslip->payrollPeriod->status === 'paid'
                        ? 'bg-sage/50 text-green-700 dark:bg-sage/20 dark:text-green-400'
                        : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300';
                    $statusLabel = $payslip->payrollPeriod->status === 'paid' ? 'Dibayar' : 'Terkunci';
                @endphp
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-slate-900 dark:text-white">
                            {{ $periodDate->translatedFormat('F Y') }}
                        </h3>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Gaji Kotor</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($payslip->total_earnings, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Total Potongan</span>
                            <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($payslip->total_deductions, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-700 pt-2 flex justify-between">
                            <span class="text-slate-700 dark:text-slate-300 font-bold">Gaji Bersih</span>
                            <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($payslip->net_salary, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <a href="{{ route('payslips.show', $payslip) }}"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-sky/20 text-sky-700 dark:bg-sky/10 dark:text-sky-400 rounded-xl text-xs font-bold hover:bg-sky/30 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Detail
                        </a>
                        <a href="{{ route('payslips.download', $payslip) }}"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-sage/20 text-green-700 dark:bg-sage/10 dark:text-green-400 rounded-xl text-xs font-bold hover:bg-sage/30 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download PDF
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $payslips->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="font-bold text-slate-600 dark:text-slate-400">Belum ada slip gaji</p>
                <p class="text-sm text-slate-500 dark:text-slate-500">Slip gaji akan muncul setelah periode payroll dibayarkan.</p>
            </div>
        </div>
    @endif
</div>
@endsection
