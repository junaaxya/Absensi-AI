@extends('layouts.admin')

@section('header-title', 'Buat Periode Payroll')
@section('header-subtitle', 'Buat periode penggajian baru')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-primary/30 flex items-center justify-center">
                <span class="material-icons-round text-green-800 text-[20px]">calendar_month</span>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Periode Baru</h3>
                <p class="text-xs text-slate-500">Pilih bulan dan tahun untuk periode payroll</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.payroll.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Bulan</label>
                    <select name="month"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/50 focus:border-primary">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $m == now()->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tahun</label>
                    <select name="year"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-primary/50 focus:border-primary">
                        @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            @error('month')
                <div class="mb-4 text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</div>
            @enderror
            @error('year')
                <div class="mb-4 text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</div>
            @enderror

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="flex-1 px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                    Buat Periode
                </button>
                <a href="{{ route('admin.payroll.index') }}"
                    class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-sm hover:bg-slate-200 dark:hover:bg-slate-600 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
