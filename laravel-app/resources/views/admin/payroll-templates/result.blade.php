@extends('layouts.admin')

@section('header-title', 'Template Diterapkan')
@section('header-subtitle', 'Template berhasil diterapkan ke komponen gaji')

@section('content')

<div x-cloak>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-sage/20 flex items-center justify-center mx-auto mb-5">
                <span class="material-icons-round text-emerald-600 text-3xl">check_circle</span>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Template Berhasil Diterapkan!</h1>
            <p class="text-slate-500 dark:text-slate-400 mb-6">Template <strong>{{ $template->name }}</strong> telah diterapkan ke komponen gaji Anda.</p>

            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-sage/10 rounded-xl p-4">
                    <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $application->components_created }}</div>
                    <div class="text-xs text-slate-500 mt-1">Dibuat</div>
                </div>
                <div class="bg-sky/10 rounded-xl p-4">
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $application->components_skipped }}</div>
                    <div class="text-xs text-slate-500 mt-1">Dilewati</div>
                </div>
                <div class="bg-lavender/10 rounded-xl p-4">
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $application->components_updated }}</div>
                    <div class="text-xs text-slate-500 mt-1">Diperbarui</div>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 mb-6 text-left">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Detail Penerapan</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Mode</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white capitalize">{{ $application->mode }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Waktu</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white">{{ $application->applied_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Template</dt>
                        <dd class="font-semibold text-slate-900 dark:text-white">{{ $template->name }}</dd>
                    </div>
                </dl>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('admin.salary-components.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-slate-900/20">
                    <span class="material-icons-round text-[18px]">tune</span>
                    Lihat Komponen Gaji
                </a>
                <form action="{{ route('admin.payroll-templates.rollback', $application) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin membatalkan penerapan template ini? Komponen yang dibuat akan dihapus.')">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 rounded-xl font-semibold text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                        <span class="material-icons-round text-[18px]">undo</span>
                        Batalkan (Rollback)
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-6 bg-sky/10 dark:bg-sky/5 rounded-2xl border border-sky/30 p-5">
            <h3 class="font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                <span class="material-icons-round text-[20px] text-blue-600">lightbulb</span>
                Langkah Selanjutnya
            </h3>
            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                <li class="flex items-start gap-2">
                    <span class="material-icons-round text-[16px] text-sage mt-0.5">check_circle</span>
                    <span>Review dan sesuaikan nominal <strong>Gaji Pokok</strong> dan <strong>Tunjangan</strong> di halaman Komponen Gaji</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="material-icons-round text-[16px] text-sage mt-0.5">check_circle</span>
                    <span>Assign komponen gaji ke masing-masing karyawan</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="material-icons-round text-[16px] text-sage mt-0.5">check_circle</span>
                    <span>Verifikasi formula BPJS dan PPh 21 sesuai dengan tarif terbaru di <a href="{{ route('admin.rate-management.index') }}" class="text-blue-600 dark:text-blue-400 underline">Tarif & Pajak</a></span>
                </li>
            </ul>
        </div>
    </div>

</div>

@endsection
