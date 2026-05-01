<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima Kasih — Karir</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { sage: '#C8D5B9', sky: '#B8D4E3', peach: '#F5D5CB', lavender: '#D4C5E2', cream: '#FAF8F5' },
                fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
            }}
        }
    </script>
</head>
<body class="font-sans antialiased bg-cream text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full text-center">
            <div class="w-20 h-20 rounded-full bg-sage/30 flex items-center justify-center mx-auto mb-6">
                <span class="material-icons-round text-emerald-600 text-[40px]">check_circle</span>
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900 mb-3">Lamaran Terkirim!</h1>
            <p class="text-slate-500 mb-2">
                Terima kasih telah melamar posisi <strong class="text-slate-700">{{ $jobPosition->title }}</strong>.
            </p>
            <p class="text-sm text-slate-400 mb-8">
                Tim HR kami akan meninjau lamaran Anda dan menghubungi melalui email jika Anda lolos ke tahap selanjutnya.
            </p>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-8 text-left">
                <h3 class="font-bold text-sm text-slate-700 mb-3">Apa selanjutnya?</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-sky/30 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[10px] font-bold text-blue-700">1</span>
                        </div>
                        <p class="text-sm text-slate-600">Tim HR akan mereview CV dan surat lamaran Anda</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-lavender/30 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[10px] font-bold text-purple-700">2</span>
                        </div>
                        <p class="text-sm text-slate-600">Jika lolos screening, Anda akan dihubungi untuk interview</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-sage/30 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="text-[10px] font-bold text-emerald-700">3</span>
                        </div>
                        <p class="text-sm text-slate-600">Proses seleksi biasanya memakan waktu 1-2 minggu</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('career.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg">
                <span class="material-icons-round text-[16px]">arrow_back</span>
                Lihat Lowongan Lain
            </a>
        </div>
    </div>

</body>
</html>
