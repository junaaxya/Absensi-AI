@extends('layouts.absensi')

@section('content')
    <div x-data="{ izinOpen: false }" class="pb-20 md:pb-0">

        <!-- ANNOUNCEMENTS SECTION -->
        @if(isset($activeAnnouncements) && $activeAnnouncements->count() > 0)
            <div class="mb-6 space-y-4">
                @foreach($activeAnnouncements as $announcement)
                    @php
                        $bgColor = match($announcement->type) {
                            'info' => 'bg-blue-50 border-blue-100 text-blue-800',
                            'warning' => 'bg-yellow-50 border-yellow-100 text-yellow-800',
                            'danger' => 'bg-red-50 border-red-100 text-red-800',
                            default => 'bg-slate-50 border-slate-100 text-slate-800',
                        };
                        $icon = match($announcement->type) {
                            'info' => 'info',
                            'warning' => 'warning',
                            'danger' => 'error',
                            default => 'campaign',
                        };
                        $iconColor = match($announcement->type) {
                            'info' => 'text-blue-500',
                            'warning' => 'text-yellow-500',
                            'danger' => 'text-red-500',
                            default => 'text-slate-500',
                        };
                    @endphp
                    <div class="{{ $bgColor }} border rounded-2xl p-4 flex items-start gap-3 shadow-sm relative overflow-hidden">
                        <div class="flex-shrink-0 mt-0.5">
                            <span class="material-icons-round {{ $iconColor }}">{{ $icon }}</span>
                        </div>
                        <div class="flex-1 z-10">
                            <h4 class="font-bold text-sm mb-1">{{ $announcement->title }}</h4>
                            <p class="text-xs opacity-90 leading-relaxed">{{ $announcement->content }}</p>
                            <p class="text-[10px] mt-2 opacity-70 font-medium">
                                {{ \Carbon\Carbon::parse($announcement->start_date)->format('d M Y') }}
                            </p>
                        </div>
                        <!-- Decorative Circle -->
                        <div class="absolute -right-4 -bottom-4 w-16 h-16 rounded-full bg-white opacity-20 z-0"></div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- COMPACT PROFILE HEADER (Gojek/Shopee Style) -->
        <div class="md:hidden flex items-center justify-between mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold border border-emerald-200">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="leading-tight">
                    <p class="text-xs text-gray-500 font-medium">Selamat Pagi,</p>
                    <h2 class="text-sm font-bold text-gray-800">{{ explode(' ', $user->name)[0] }}</h2>
                </div>
            </div>
            <div class="flex items-center gap-2">
                 <div class="text-right hidden sm:block">
                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::now()->translatedFormat('l, d M') }}</p>
                </div>
                <button class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Desktop Header (Kept simple) -->
        <div class="hidden md:flex items-center justify-between mb-10 mt-6 px-6">
            <div>
                 <h1 class="text-2xl font-bold text-gray-800 mb-2">Hallo, {{ explode(' ', $user->name)[0] }}! 👋</h1>
                 <p class="text-gray-500 text-sm">Akses cepat menu presensi Anda.</p>
            </div>
             <div class="text-right">
                <h3 class="text-2xl font-bold text-emerald-600 mb-1">{{ \Carbon\Carbon::now()->format('H:i') }}</h3>
                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>

        <!-- "GOJEK" STYLE ICON GRID (4 Columns) -->
        <div class="bg-white rounded-[24px] p-5 shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 mb-6">
            <div class="grid grid-cols-4 gap-y-6 gap-x-2 md:gap-8">
                
                <!-- ABSEN MASUK (Green) -->
                <button @click="startAttendance('masuk')" class="flex flex-col items-center gap-1.5 group active:scale-95 transition-transform duration-200">
                    <div class="relative w-12 h-12 md:w-16 md:h-16 rounded-full bg-green-500 shadow-md shadow-green-200 flex items-center justify-center transition-all group-hover:bg-green-600">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        @if($attendanceToday && $attendanceToday->jam_masuk)
                            <div class="absolute -right-1 -top-1 w-4 h-4 bg-white rounded-full flex items-center justify-center border border-green-100">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        @endif
                    </div>
                    <span class="text-[10px] md:text-sm font-medium text-gray-700 text-center leading-tight">Masuk</span>
                </button>

                <!-- ABSEN KELUAR (Red) -->
                <button @click="startAttendance('pulang')" class="flex flex-col items-center gap-1.5 group active:scale-95 transition-transform duration-200">
                    <div class="relative w-12 h-12 md:w-16 md:h-16 rounded-full bg-red-500 shadow-md shadow-red-200 flex items-center justify-center transition-all group-hover:bg-red-600">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                         @if($attendanceToday && $attendanceToday->jam_keluar)
                            <div class="absolute -right-1 -top-1 w-4 h-4 bg-white rounded-full flex items-center justify-center border border-red-100">
                                <svg class="w-2.5 h-2.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        @endif
                    </div>
                    <span class="text-[10px] md:text-sm font-medium text-gray-700 text-center leading-tight">Keluar</span>
                </button>

                <!-- IZIN / SAKIT (Amber) -->
                <button @click="$dispatch('open-modal', 'izin')" class="flex flex-col items-center gap-1.5 group active:scale-95 transition-transform duration-200">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-amber-500 shadow-md shadow-amber-200 flex items-center justify-center transition-all group-hover:bg-amber-600">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-[10px] md:text-sm font-medium text-gray-700 text-center leading-tight">Izin</span>
                </button>

                <!-- RIWAYAT (Blue) -->
                <a href="#history-section" class="flex flex-col items-center gap-1.5 group active:scale-95 transition-transform duration-200">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-blue-500 shadow-md shadow-blue-200 flex items-center justify-center transition-all group-hover:bg-blue-600">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                     <span class="text-[10px] md:text-sm font-medium text-gray-700 text-center leading-tight">Riwayat</span>
                </a>

                <!-- JADWAL (Purple) -->
                <button class="flex flex-col items-center gap-1.5 group active:scale-95 transition-transform duration-200">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-purple-500 shadow-md shadow-purple-200 flex items-center justify-center transition-all group-hover:bg-purple-600">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-[10px] md:text-sm font-medium text-gray-700 text-center leading-tight">Jadwal</span>
                </button>

                <!-- PROFIL (Indigo) -->
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-1.5 group active:scale-95 transition-transform duration-200">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-indigo-500 shadow-md shadow-indigo-200 flex items-center justify-center transition-all group-hover:bg-indigo-600">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="text-[10px] md:text-sm font-medium text-gray-700 text-center leading-tight">Profil</span>
                </a>

                 <!-- ADMIN (Teal) - CONDITIONAL -->
                 @if(Auth::user()->role === 'admin')
                    <a href="{{ route('employees.index') }}" class="flex flex-col items-center gap-1.5 group active:scale-95 transition-transform duration-200">
                        <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-teal-500 shadow-md shadow-teal-200 flex items-center justify-center transition-all group-hover:bg-teal-600">
                             <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-[10px] md:text-sm font-medium text-gray-700 text-center leading-tight">Admin</span>
                    </a>
                 @endif

                 <!-- More Item (Gray) -->
                 <button class="flex flex-col items-center gap-1.5 group active:scale-95 transition-transform duration-200">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center transition-all group-hover:bg-gray-200">
                         <svg class="w-6 h-6 md:w-8 md:h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                    </div>
                    <span class="text-[10px] md:text-sm font-medium text-gray-500 text-center leading-tight">Lainnya</span>
                </button>

            </div>
        </div>

        <!-- STATUS CARD (Below Grid) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <!-- ABSEN TODAY CARD -->
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                
                <h3 class="font-semibold text-emerald-100 text-sm mb-4">Status Hari Ini</h3>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-emerald-100 mb-1">Masuk</p>
                        <p class="text-2xl font-bold font-mono tracking-wide">
                            {{ $attendanceToday && $attendanceToday->jam_masuk ? \Carbon\Carbon::parse($attendanceToday->jam_masuk)->format('H:i') : '--:--' }}
                        </p>
                    </div>
                    <div class="h-8 w-px bg-white/30"></div>
                     <div>
                        <p class="text-xs text-emerald-100 mb-1">Keluar</p>
                        <p class="text-2xl font-bold font-mono tracking-wide text-white/90">
                            {{ $attendanceToday && $attendanceToday->jam_keluar ? \Carbon\Carbon::parse($attendanceToday->jam_keluar)->format('H:i') : '--:--' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- QUOTE / INFO CARD -->
             <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                     <h4 class="font-bold text-gray-800 text-sm">Tetap Semangat!</h4>
                     <p class="text-xs text-gray-500 mt-1">Kehadiran tepat waktu mencerminkan profesionalisme Anda.</p>
                </div>
            </div>
        </div>

        <!-- ATTENDANCE HISTORY LIST -->
        <div id="history-section" class="scroll-mt-24">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-text-primary">Riwayat Aktivitas</h3>
                <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                    <select name="bulan"
                        class="rounded-lg border-none bg-white shadow-sm text-sm py-2 pl-3 pr-8 focus:ring-pastel-sage"
                        onchange="this.form.submit()">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('bulan', date('n')) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-neutral-stone/50 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-neutral-warm/50 border-b border-neutral-stone/50">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-text-secondary">Tanggal</th>
                                <th class="px-6 py-4 font-semibold text-text-secondary text-center">Jam</th>
                                <th class="px-6 py-4 font-semibold text-text-secondary text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-text-secondary text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-stone/30">
                            @forelse($attendanceHistory as $row)
                                <tr class="hover:bg-neutral-warm/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-text-primary">
                                            {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</div>
                                        <div class="text-xs text-text-secondary">
                                            {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <span
                                                class="bg-pastel-sage/20 text-pastel-sage-dark px-2 py-1 rounded text-xs font-bold">{{ $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') : '-' }}</span>
                                            <span class="text-text-muted">➜</span>
                                            <span
                                                class="bg-pastel-rose/20 text-pastel-rose-dark px-2 py-1 rounded text-xs font-bold">{{ $row->jam_keluar ? \Carbon\Carbon::parse($row->jam_keluar)->format('H:i') : '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if(in_array($row->status, ['izin', 'sakit', 'cuti', 'dinas']))
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-pastel-peach/30 text-amber-800">
                                                {{ ucfirst($row->status) }}
                                            </span>
                                        @elseif($row->status === 'terlambat')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-pastel-rose/30 text-red-800">
                                                Terlambat
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-pastel-sage/30 text-green-800">
                                                Hadir
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($row->lat_in && $row->long_in)
                                            <a href="https://www.google.com/maps?q={{ $row->lat_in }},{{ $row->long_in }}"
                                                target="_blank" class="text-pastel-sky-dark hover:underline text-xs">
                                                Lihat Lokasi
                                            </a>
                                        @else
                                            <span class="text-text-muted text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-text-secondary italic">
                                        Belum ada data absensi bulan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- LEAVE REQUEST MODAL -->
        <x-pastel-modal name="izin" title="Pengajuan Ketidakhadiran" maxWidth="lg">
            <form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-1">Jenis Pengajuan</label>
                    <select name="jenis" required
                        class="w-full px-4 py-2 rounded-xl bg-white border border-neutral-stone focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition">
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                        <option value="dinas">Dinas</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required
                            class="w-full px-4 py-2 rounded-xl bg-white border border-neutral-stone focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required
                            class="w-full px-4 py-2 rounded-xl bg-white border border-neutral-stone focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-1">Alasan</label>
                    <textarea name="alasan" required rows="3"
                        class="w-full px-4 py-2 rounded-xl bg-white border border-neutral-stone focus:border-pastel-sage focus:ring-4 focus:ring-pastel-sage/20 transition"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-1">Lampirkan Foto atau Dokumen</label>
                    <input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full text-sm text-text-secondary
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-xl file:border-0
                            file:text-sm file:font-semibold
                            file:bg-pastel-sage/20 file:text-pastel-sage-dark
                            hover:file:bg-pastel-sage/30
                        ">
                    <p class="mt-1 text-xs text-text-secondary">Format: PDF, JPG, PNG, DOCX (Max 5MB)</p>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-text-secondary hover:bg-neutral-stone/30 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary rounded-lg transition shadow-soft">
                        Ajukan
                    </button>
                </div>
            </form>
        </x-pastel-modal>

    </div>
    <!-- CAMERA MODAL -->
    <x-pastel-modal name="camera-modal" title="Ambil Foto Selfie" maxWidth="lg">
        <div x-data="cameraHandler()" @open-camera.window="initCamera($event.detail.type)" class="space-y-4">

            <!-- Video Preview -->
            <div class="relative w-full aspect-[4/5] bg-black rounded-xl overflow-hidden shadow-inner">
                <video x-ref="video" class="w-full h-full object-cover transform scale-x-[-1]" autoplay playsinline
                    muted></video>
                <canvas x-ref="canvas" class="hidden"></canvas>

                <!-- Overlay Loading -->
                <div x-show="loading"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-black/50 text-white z-10">
                    <svg class="animate-spin h-8 w-8 text-white mb-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="loadingText" class="text-sm font-medium"></span>
                </div>

                <!-- Error Message -->
                <div x-show="errorMessage"
                    class="absolute bottom-4 left-4 right-4 bg-red-500/90 text-white px-4 py-2 rounded-xl text-sm text-center"
                    x-text="errorMessage"></div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center px-2">
                <button type="button" @click="$dispatch('close-modal', 'camera-modal')"
                    class="px-4 py-2 text-text-secondary hover:bg-neutral-stone/20 rounded-xl transition">
                    Batal
                </button>

                <button @click="takePicture" :disabled="loading"
                    class="h-16 w-16 rounded-full border-4 border-pastel-sage bg-white flex items-center justify-center hover:bg-pastel-sage/20 transition disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 active:scale-95 shadow-md">
                    <div class="h-12 w-12 rounded-full bg-pastel-sage"></div>
                </button>

                <div class="w-16"></div> <!-- Spacer for center alignment -->
            </div>
        </div>
    </x-pastel-modal>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cameraHandler', () => ({
                stream: null,
                loading: true,
                loadingText: 'Menyiapkan kamera...',
                errorMessage: '',
                attendanceType: '', // 'masuk' or 'pulang'

                async initCamera(type) {
                    this.attendanceType = type;
                    this.loading = true;
                    this.loadingText = 'Menyalakan kamera...';
                    this.errorMessage = '';

                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                        this.$refs.video.srcObject = this.stream;
                        this.loading = false;
                    } catch (err) {
                        console.error(err);
                        this.loading = false;
                        this.errorMessage = 'Gagal akses kamera: ' + err.message;
                    }
                },

                async takePicture() {
                    this.loading = true;
                    this.loadingText = 'Mengambil lokasi...';
                    this.errorMessage = '';

                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;

                    // Capture Frame
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    // Flip context horizontally if using front camera (optional, consistent with preview)
                    // ctx.translate(canvas.width, 0);
                    // ctx.scale(-1, 1);
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Convert to Blob
                    canvas.toBlob(async (blob) => {
                        if (!blob) {
                            this.loading = false;
                            this.errorMessage = 'Gagal mengambil gambar.';
                            return;
                        }

                        // Get Geolocation
                        if (!navigator.geolocation) {
                            this.loading = false;
                            this.errorMessage = 'Browser tidak support Geolocation.';
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(
                            async (position) => {
                                await this.submitAttendance(blob, position.coords.latitude, position.coords.longitude);
                            },
                            (err) => {
                                this.loading = false;
                                this.errorMessage = 'Gagal dapat lokasi: ' + err.message;
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    }, 'image/jpeg', 0.8);
                },

                async submitAttendance(photoBlob, lat, long) {
                    this.loadingText = 'Memproses Absensi...';

                    const formData = new FormData();
                    formData.append('photo', photoBlob, 'selfie.jpg');
                    formData.append('type', this.attendanceType);
                    formData.append('latitude', lat);
                    formData.append('longitude', long);

                    try {
                        // Get CSRF Token
                        const token = document.querySelector('meta[name="csrf-token"]').content;

                        const response = await fetch('/api/attendance/auto', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || 'Terjadi kesalahan sistem.');
                        }

                        // Success
                        // Reload page to update dashboard data
                        window.location.href = "{{ route('dashboard') }}?status=success&message=" + encodeURIComponent(result.message);

                    } catch (error) {
                        this.loading = false;
                        this.errorMessage = error.message;
                    }
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                }
            }));
        });

        // Helper function outside Alpine component to interact with it
        function startAttendance(type) {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'camera-modal' }));
            // Wait for modal to open then init camera
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-camera', { detail: { type: type } }));
            }, 100);
        }
    </script>
@endsection