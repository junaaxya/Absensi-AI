<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $jobPosition->title }} — Karir</title>
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('career.index') }}" class="flex items-center gap-2 text-slate-600 hover:text-slate-900 transition-colors">
                <span class="material-icons-round text-[20px]">arrow_back</span>
                <span class="font-bold text-sm">Semua Lowongan</span>
            </a>
            <a href="{{ route('career.apply', $jobPosition) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg">
                <span class="material-icons-round text-[16px]">send</span>
                Lamar Sekarang
            </a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-8 sm:p-10">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-2">{{ $jobPosition->title }}</h1>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                            @if($jobPosition->department)
                            <span class="flex items-center gap-1.5">
                                <span class="material-icons-round text-[16px]">business</span>
                                {{ $jobPosition->department->name }}
                            </span>
                            @endif
                            <span class="flex items-center gap-1.5">
                                <span class="material-icons-round text-[16px]">badge</span>
                                @php
                                    $typeLabels = ['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Kontrak', 'internship' => 'Magang'];
                                @endphp
                                {{ $typeLabels[$jobPosition->employment_type] ?? ucfirst($jobPosition->employment_type) }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="material-icons-round text-[16px]">event_seat</span>
                                {{ $jobPosition->openings }} lowongan
                            </span>
                        </div>
                    </div>
                    @if($jobPosition->salary_range_min || $jobPosition->salary_range_max)
                    <div class="bg-sage/20 rounded-xl px-4 py-2.5 text-center shrink-0">
                        <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider mb-0.5">Gaji</p>
                        <p class="text-sm font-bold text-emerald-800">
                            @if($jobPosition->salary_range_min && $jobPosition->salary_range_max)
                                Rp {{ number_format($jobPosition->salary_range_min, 0, ',', '.') }} - {{ number_format($jobPosition->salary_range_max, 0, ',', '.') }}
                            @elseif($jobPosition->salary_range_min)
                                Mulai Rp {{ number_format($jobPosition->salary_range_min, 0, ',', '.') }}
                            @endif
                        </p>
                    </div>
                    @endif
                </div>

                @if($jobPosition->description)
                <div class="mb-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="material-icons-round text-[20px] text-slate-400">description</span>
                        Deskripsi Pekerjaan
                    </h2>
                    <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600">
                        {!! nl2br(e($jobPosition->description)) !!}
                    </div>
                </div>
                @endif

                @if($jobPosition->requirements)
                <div class="mb-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="material-icons-round text-[20px] text-slate-400">checklist</span>
                        Persyaratan
                    </h2>
                    <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600">
                        {!! nl2br(e($jobPosition->requirements)) !!}
                    </div>
                </div>
                @endif

                <div class="mt-10 pt-8 border-t border-slate-100 text-center">
                    <p class="text-slate-500 text-sm mb-4">Tertarik dengan posisi ini?</p>
                    <a href="{{ route('career.apply', $jobPosition) }}"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-slate-900 text-white rounded-xl font-bold text-base hover:bg-slate-800 transition-all shadow-lg">
                        <span class="material-icons-round text-[20px]">send</span>
                        Lamar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Sistem Absensi') }}
        </div>
    </footer>

</body>
</html>
