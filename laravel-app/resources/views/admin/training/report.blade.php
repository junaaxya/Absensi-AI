@extends('layouts.admin')

@section('header-title', 'Laporan Training')
@section('header-subtitle', 'Analisis penyelesaian pelatihan karyawan')

@section('content')
<div>
    <a href="{{ route('admin.training.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 mb-6 transition-colors">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>

    {{-- Department Completion Rates --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 mb-6">
        <h3 class="font-bold text-slate-900 dark:text-white mb-6">Tingkat Penyelesaian per Departemen</h3>
        <div class="space-y-4">
            @foreach($departmentStats as $dept)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $dept['name'] }}</span>
                        <div class="flex items-center gap-4 text-xs text-slate-500">
                            <span>{{ $dept['total_employees'] }} karyawan</span>
                            <span>{{ $dept['completed'] }}/{{ $dept['total_enrollments'] }} selesai</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $dept['completion_rate'] }}%</span>
                        </div>
                    </div>
                    <div class="w-full h-4 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                            style="width: {{ $dept['completion_rate'] }}%; background: linear-gradient(90deg, #C8D5B9, #B8D4E3);"></div>
                    </div>
                </div>
            @endforeach

            @if(empty($departmentStats))
                <p class="text-center text-slate-400 py-8">Belum ada data departemen</p>
            @endif
        </div>
    </div>

    {{-- Mandatory Course Compliance --}}
    @if($mandatoryCourses->count() > 0)
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white">Kepatuhan Kursus Wajib</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <th class="px-6 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Kursus</th>
                            <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Terdaftar</th>
                            <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Tingkat Kepatuhan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($mandatoryCourses as $mc)
                            @php
                                $complianceRate = $mc->enrollments_count > 0
                                    ? round(($mc->completed_count / $mc->enrollments_count) * 100, 1)
                                    : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-3">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $mc->title }}</span>
                                </td>
                                <td class="px-6 py-3 text-center text-slate-600 dark:text-slate-400">{{ $mc->enrollments_count }}</td>
                                <td class="px-6 py-3 text-center text-slate-600 dark:text-slate-400">{{ $mc->completed_count }}</td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-20 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full" style="width: {{ $complianceRate }}%; background: {{ $complianceRate >= 80 ? '#C8D5B9' : ($complianceRate >= 50 ? '#F5D5CB' : '#fca5a5') }};"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $complianceRate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- All Courses Overview --}}
    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-slate-900 dark:text-white">Ringkasan Semua Kursus</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <th class="px-6 py-3 text-left font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Kursus</th>
                        <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Terdaftar</th>
                        <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Sedang Belajar</th>
                        <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Selesai</th>
                        <th class="px-6 py-3 text-center font-bold text-slate-600 dark:text-slate-300 uppercase text-xs tracking-wider">Completion Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($courses as $course)
                        @php
                            $rate = $course->enrollments_count > 0
                                ? round(($course->completed_count / $course->enrollments_count) * 100, 1)
                                : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-3">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $course->title }}</span>
                                @if($course->is_mandatory)
                                    <span class="ml-2 px-2 py-0.5 bg-peach/50 text-orange-700 dark:text-orange-300 rounded-md text-[10px] font-bold">WAJIB</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center text-slate-600 dark:text-slate-400">{{ $course->enrollments_count }}</td>
                            <td class="px-6 py-3 text-center text-slate-600 dark:text-slate-400">{{ $course->in_progress_count }}</td>
                            <td class="px-6 py-3 text-center text-slate-600 dark:text-slate-400">{{ $course->completed_count }}</td>
                            <td class="px-6 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-20 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary rounded-full" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $rate }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada data kursus</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
