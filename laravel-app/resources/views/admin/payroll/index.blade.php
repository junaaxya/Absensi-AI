@extends('layouts.admin')

@section('header-title', 'Payroll')
@section('header-subtitle', 'Kelola penggajian karyawan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Periode Payroll</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Kelola perhitungan gaji per periode</p>
    </div>
    <a href="{{ route('admin.payroll.create') }}"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
        <span class="material-icons-round text-[18px]">add</span>
        Buat Periode Baru
    </a>
</div>

<div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                    <th class="px-6 py-4 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Periode</th>
                    <th class="px-6 py-4 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Total Karyawan</th>
                    <th class="px-6 py-4 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Total Bruto</th>
                    <th class="px-6 py-4 text-right font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Total Netto</th>
                    <th class="px-6 py-4 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($periods as $period)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $period->period_month)->translatedFormat('F Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'draft' => 'bg-sky/40 text-sky-800 dark:text-sky-200',
                                'processing' => 'bg-lavender/40 text-purple-800 dark:text-purple-200',
                                'calculated' => 'bg-peach/40 text-orange-800 dark:text-orange-200',
                                'approved' => 'bg-primary/40 text-green-800 dark:text-green-200',
                                'paid' => 'bg-green-200/60 text-green-800 dark:text-green-200',
                                'locked' => 'bg-gray-300/60 text-gray-700 dark:text-gray-300',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$period->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($period->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-slate-700 dark:text-slate-300 font-medium">
                        {{ number_format($period->total_employees) }}
                    </td>
                    <td class="px-6 py-4 text-right text-slate-700 dark:text-slate-300 font-medium">
                        Rp {{ number_format($period->total_gross, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right text-slate-900 dark:text-white font-bold">
                        Rp {{ number_format($period->total_net, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if($period->status === 'draft')
                            <form method="POST" action="{{ route('admin.payroll.calculate', $period) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-peach/50 text-orange-800 rounded-lg text-xs font-bold hover:bg-peach transition-all">
                                    <span class="material-icons-round text-[14px]">calculate</span>
                                    Hitung
                                </button>
                            </form>
                            @endif

                            @if($period->status === 'calculated')
                            <form method="POST" action="{{ route('admin.payroll.approve', $period) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary/50 text-green-800 rounded-lg text-xs font-bold hover:bg-primary transition-all">
                                    <span class="material-icons-round text-[14px]">check_circle</span>
                                    Setujui
                                </button>
                            </form>
                            @endif

                            @if($period->status === 'approved')
                            <form method="POST" action="{{ route('admin.payroll.mark-paid', $period) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-200/60 text-green-800 rounded-lg text-xs font-bold hover:bg-green-200 transition-all">
                                    <span class="material-icons-round text-[14px]">paid</span>
                                    Tandai Lunas
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('admin.payroll.show', $period) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition-all">
                                <span class="material-icons-round text-[14px]">visibility</span>
                                Lihat
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2 block">receipt_long</span>
                        Belum ada periode payroll
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($periods->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
        {{ $periods->links() }}
    </div>
    @endif
</div>
@endsection
