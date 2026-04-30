<x-guest-layout>
    <div class="text-center">
        <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1 class="text-2xl font-extrabold text-slate-900 mb-3">Menunggu Persetujuan</h1>

        <p class="text-sm text-slate-500 leading-relaxed mb-6">
            Akun Anda telah berhasil dibuat. Silakan tunggu hingga Admin/HRD menyetujui akun Anda sebelum dapat mengakses sistem.
        </p>

        <div class="bg-blue-50 rounded-xl p-4 mb-6 text-left border border-blue-100">
            <p class="text-xs font-bold text-blue-700 mb-2">Apa yang terjadi selanjutnya?</p>
            <ul class="text-xs text-slate-600 space-y-1.5 leading-relaxed">
                <li>1. Admin akan mereview data pendaftaran Anda</li>
                <li>2. Admin akan menetapkan departemen, jabatan, dan shift kerja</li>
                <li>3. Setelah disetujui, Anda bisa login dan menggunakan sistem</li>
            </ul>
        </div>

        <div class="bg-slate-50 rounded-xl p-4 mb-6 border border-slate-100">
            <p class="text-xs text-slate-500">
                Terdaftar sebagai: <strong class="text-slate-700">{{ auth()->user()->name }}</strong>
                <br>
                Email: <strong class="text-slate-700">{{ auth()->user()->email }}</strong>
            </p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </div>
</x-guest-layout>
