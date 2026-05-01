<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Karir — {{ config('app.name', 'Sistem Absensi') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sage: '#C8D5B9', sky: '#B8D4E3', peach: '#F5D5CB', lavender: '#D4C5E2',
                        cream: '#FAF8F5', warm: '#F5F3F0', stone: '#E8E4DF',
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-cream text-slate-900">

    {{-- Navbar --}}
    <nav class="bg-white/80 backdrop-blur-lg border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('career.index') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center">
                    <span class="material-icons-round text-white text-[18px]">work</span>
                </div>
                <span class="font-bold text-lg">Karir</span>
            </a>
            <a href="{{ route('login') }}"
                class="text-sm text-slate-500 hover:text-slate-700 transition-colors flex items-center gap-1">
                <span class="material-icons-round text-[16px]">login</span>
                Login Karyawan
            </a>
        </div>
    </nav>

    {{-- Hero --}}
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white py-16 sm:py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-5xl font-extrabold mb-4 leading-tight">Bergabung Bersama Kami</h1>
            <p class="text-lg text-slate-300 max-w-2xl mx-auto">
                Temukan peluang karir yang sesuai dengan keahlian dan passion Anda.
            </p>
            <div class="mt-8 flex items-center justify-center gap-3 text-sm text-slate-400">
                <span class="flex items-center gap-1.5">
                    <span class="material-icons-round text-sage text-[18px]">verified</span>
                    {{ $positions->total() }} Posisi Terbuka
                </span>
            </div>
        </div>
    </div>

    {{-- Job Listings --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($positions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($positions as $position)
            <a href="{{ route('career.show', $position) }}"
                class="block bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-bold text-lg text-slate-900 group-hover:text-blue-600 transition-colors leading-tight">
                            {{ $position->title }}
                        </h3>
                        <span class="material-icons-round text-slate-300 group-hover:text-blue-500 transition-colors text-[20px] shrink-0 ml-2">
                            arrow_forward
                        </span>
                    </div>

                    <div class="space-y-2 text-sm text-slate-500">
                        @if($position->department)
                        <div class="flex items-center gap-2">
                            <span class="material-icons-round text-[16px] text-slate-400">business</span>
                            {{ $position->department->name }}
                        </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <span class="material-icons-round text-[16px] text-slate-400">badge</span>
                            @php
                                $typeLabels = ['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Kontrak', 'internship' => 'Magang'];
                            @endphp
                            {{ $typeLabels[$position->employment_type] ?? ucfirst($position->employment_type) }}
                        </div>
                        @if($position->salary_range_min || $position->salary_range_max)
                        <div class="flex items-center gap-2">
                            <span class="material-icons-round text-[16px] text-slate-400">payments</span>
                            @if($position->salary_range_min && $position->salary_range_max)
                                Rp {{ number_format($position->salary_range_min, 0, ',', '.') }} - {{ number_format($position->salary_range_max, 0, ',', '.') }}
                            @elseif($position->salary_range_min)
                                Mulai Rp {{ number_format($position->salary_range_min, 0, ',', '.') }}
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                        <span>{{ $position->openings }} lowongan</span>
                        <span>{{ $position->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $positions->links() }}
        </div>
        @else
        <div class="text-center py-20">
            <span class="material-icons-round text-[64px] text-slate-300 mb-4 block">work_off</span>
            <h2 class="text-xl font-bold text-slate-700 mb-2">Belum Ada Lowongan Terbuka</h2>
            <p class="text-slate-500">Silakan cek kembali nanti. Kami akan membuka posisi baru segera.</p>
        </div>
        @endif
    </div>

    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Sistem Absensi') }}. All rights reserved.
        </div>
    </footer>

</body>
</html>
