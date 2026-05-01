<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lamar — {{ $jobPosition->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { sage: '#C8D5B9', sky: '#B8D4E3', peach: '#F5D5CB', lavender: '#D4C5E2', cream: '#FAF8F5', warm: '#F5F3F0' },
                fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
            }}
        }
    </script>
</head>
<body class="font-sans antialiased bg-cream text-slate-900">

    <nav class="bg-white/80 backdrop-blur-lg border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center">
            <a href="{{ route('career.show', $jobPosition) }}" class="flex items-center gap-2 text-slate-600 hover:text-slate-900 transition-colors">
                <span class="material-icons-round text-[20px]">arrow_back</span>
                <span class="font-bold text-sm">Kembali ke Detail</span>
            </a>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-slate-900 mb-1">Lamar Posisi</h1>
            <p class="text-slate-500">
                <span class="font-bold text-slate-700">{{ $jobPosition->title }}</span>
                @if($jobPosition->department)
                    — {{ $jobPosition->department->name }}
                @endif
            </p>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <div class="flex items-start gap-2">
                <span class="material-icons-round text-red-500 text-[18px] mt-0.5">error</span>
                <div>
                    <p class="text-sm font-bold text-red-700 mb-1">Terjadi kesalahan:</p>
                    <ul class="text-sm text-red-600 space-y-0.5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('career.store', $jobPosition) }}" enctype="multipart/form-data"
            class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-6">
            @csrf

            {{-- Honeypot --}}
            <div style="display:none">
                <input type="text" name="honeypot" value="" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all"
                        placeholder="Masukkan nama lengkap">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all"
                        placeholder="email@contoh.com">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">No. Telepon</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all"
                        placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Sumber Informasi <span class="text-red-500">*</span></label>
                    <select name="source" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all">
                        <option value="">Pilih sumber</option>
                        <option value="website" {{ old('source') === 'website' ? 'selected' : '' }}>Website Perusahaan</option>
                        <option value="linkedin" {{ old('source') === 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                        <option value="jobstreet" {{ old('source') === 'jobstreet' ? 'selected' : '' }}>JobStreet</option>
                        <option value="referral" {{ old('source') === 'referral' ? 'selected' : '' }}>Referensi Teman/Karyawan</option>
                        <option value="other" {{ old('source') === 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Upload CV <span class="text-red-500">*</span></label>
                <input type="file" name="resume" required accept=".pdf,.doc,.docx"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                <p class="text-xs text-slate-400 mt-1">Format: PDF, DOC, DOCX. Maksimal 5MB.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Surat Lamaran (Opsional)</label>
                <textarea name="cover_letter" rows="4"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-all resize-none"
                    placeholder="Ceritakan mengapa Anda tertarik dengan posisi ini...">{{ old('cover_letter') }}</textarea>
                <p class="text-xs text-slate-400 mt-1">Maksimal 2000 karakter.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-slate-400">
                    <span class="material-icons-round text-[14px] align-middle">lock</span>
                    Data Anda aman dan hanya digunakan untuk proses rekrutmen.
                </p>
                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg">
                    <span class="material-icons-round text-[18px]">send</span>
                    Kirim Lamaran
                </button>
            </div>
        </form>
    </div>

    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Sistem Absensi') }}
        </div>
    </footer>

</body>
</html>
