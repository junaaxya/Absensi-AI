@extends('layouts.absensi')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <a href="{{ route('payslips.index') }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Slip Gaji
    </a>

    @php
        $periodDate = \Carbon\Carbon::parse($detail->payrollPeriod->period_month . '-01');
    @endphp

    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-sage/30 flex items-center justify-center">
            <svg class="w-6 h-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Slip Gaji — {{ $periodDate->translatedFormat('F Y') }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Detail komponen gaji Anda.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-sage/20 dark:bg-sage/10 rounded-2xl p-5 border border-sage/30 dark:border-sage/20">
            <p class="text-sm font-medium text-green-700 dark:text-green-400 mb-1">Gaji Kotor</p>
            <p class="text-xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($detail->total_earnings, 0, ',', '.') }}</p>
        </div>
        <div class="bg-rose-50 dark:bg-rose-900/10 rounded-2xl p-5 border border-rose-200 dark:border-rose-800/30">
            <p class="text-sm font-medium text-rose-600 dark:text-rose-400 mb-1">Total Potongan</p>
            <p class="text-xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($detail->total_deductions, 0, ',', '.') }}</p>
        </div>
        <div class="bg-sky/20 dark:bg-sky/10 rounded-2xl p-5 border border-sky/30 dark:border-sky/20">
            <p class="text-sm font-medium text-sky-700 dark:text-sky-400 mb-1">Gaji Bersih</p>
            <p class="text-xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        @if(isset($items['earning']) && $items['earning']->count() > 0)
            <div class="border-b border-slate-200 dark:border-slate-700">
                <div class="bg-green-50 dark:bg-green-900/20 px-6 py-3">
                    <h3 class="font-bold text-green-700 dark:text-green-400 text-sm uppercase tracking-wider">Pendapatan</h3>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($items['earning'] as $item)
                            <tr>
                                <td class="px-6 py-3 text-slate-700 dark:text-slate-300">{{ $item->component_name }}</td>
                                <td class="px-6 py-3 text-right font-semibold text-slate-900 dark:text-white">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(isset($items['deduction']) && $items['deduction']->count() > 0)
            <div class="border-b border-slate-200 dark:border-slate-700">
                <div class="bg-rose-50 dark:bg-rose-900/20 px-6 py-3">
                    <h3 class="font-bold text-rose-600 dark:text-rose-400 text-sm uppercase tracking-wider">Potongan</h3>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($items['deduction'] as $item)
                            <tr>
                                <td class="px-6 py-3 text-slate-700 dark:text-slate-300">{{ $item->component_name }}</td>
                                <td class="px-6 py-3 text-right font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(isset($items['benefit']) && $items['benefit']->count() > 0)
            <div>
                <div class="bg-sky/10 dark:bg-sky/5 px-6 py-3">
                    <h3 class="font-bold text-sky-700 dark:text-sky-400 text-sm uppercase tracking-wider">Benefit Perusahaan</h3>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($items['benefit'] as $item)
                            <tr>
                                <td class="px-6 py-3 text-slate-700 dark:text-slate-300">{{ $item->component_name }}</td>
                                <td class="px-6 py-3 text-right font-semibold text-slate-900 dark:text-white">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="flex flex-wrap gap-3 mt-6">
        <a href="{{ route('payslips.download', $detail) }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-sage text-slate-900 rounded-2xl font-bold text-sm hover:brightness-95 transition-all shadow-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download PDF
        </a>
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-2xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak
        </button>
    </div>
</div>
@endsection
