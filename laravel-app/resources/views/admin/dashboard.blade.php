@extends('layouts.admin')

@section('header-title', 'Dashboard')
@section('header-subtitle', $today->translatedFormat('l, d F Y'))

@section('content')

    <section class="mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-1">
            Selamat Datang, {{ Auth::user()->name }}
        </h2>
        <p class="text-slate-500 dark:text-slate-400">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/20 text-slate-700 dark:text-slate-300 mr-2">
                {{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}
            </span>
            Ringkasan Absensi Hari Ini
        </p>
    </section>

    {{-- ── Stat Cards (enhanced with trend indicators) ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-slate-400">groups</span>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Total Karyawan</h3>
                </div>
                <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ $totalKaryawan }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-sage">check_circle</span>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Hadir Hari Ini</h3>
                </div>
                <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ $totalHadir }}</p>
            </div>
            @if($trendData['hadir']['percent'] > 0)
                <div class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold
                    {{ $trendData['hadir']['direction'] === 'up' ? 'bg-sage/20 text-green-700 dark:text-green-400' : ($trendData['hadir']['direction'] === 'down' ? 'bg-pastel-rose/30 text-red-600 dark:text-red-400' : 'bg-slate-100 text-slate-500') }}">
                    <span class="material-icons-round text-sm">
                        {{ $trendData['hadir']['direction'] === 'up' ? 'trending_up' : ($trendData['hadir']['direction'] === 'down' ? 'trending_down' : 'trending_flat') }}
                    </span>
                    {{ $trendData['hadir']['percent'] }}%
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-peach">schedule</span>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Terlambat</h3>
                </div>
                <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ $hadirTerlambat }}</p>
            </div>
            @if($trendData['terlambat']['percent'] > 0)
                <div class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold
                    {{ $trendData['terlambat']['direction'] === 'up' ? 'bg-pastel-rose/30 text-red-600 dark:text-red-400' : ($trendData['terlambat']['direction'] === 'down' ? 'bg-sage/20 text-green-700 dark:text-green-400' : 'bg-slate-100 text-slate-500') }}">
                    <span class="material-icons-round text-sm">
                        {{ $trendData['terlambat']['direction'] === 'up' ? 'trending_up' : ($trendData['terlambat']['direction'] === 'down' ? 'trending_down' : 'trending_flat') }}
                    </span>
                    {{ $trendData['terlambat']['percent'] }}%
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-rose-400">close</span>
                    <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Alpha</h3>
                </div>
                <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ $alpha }}</p>
            </div>
        </div>
    </div>

    {{-- ── Detail Izin/Sakit/Dinas/Cuti ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-card-dark p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-sky/20 flex items-center justify-center">
                <span class="material-icons-round text-sky dark:text-sky-dark">assignment</span>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Izin</h4>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $izin }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-card-dark p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-peach/20 flex items-center justify-center">
                <span class="material-icons-round text-peach">medical_services</span>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sakit</h4>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $sakit }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-card-dark p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-lavender/20 flex items-center justify-center">
                <span class="material-icons-round text-lavender">business_center</span>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider leading-tight">Dinas Luar</h4>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $dinas }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-card-dark p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <span class="material-icons-round text-slate-400">event_busy</span>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cuti</h4>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $cuti }}</p>
            </div>
        </div>
    </div>

    {{-- ── 30-Day Attendance Trend (full width) ── --}}
    <div class="bg-white dark:bg-card-dark p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 mb-8">
        <h3 class="text-lg font-bold mb-6 text-slate-900 dark:text-white flex items-center gap-2">
            <span class="material-icons-round text-sky">show_chart</span>
            Tren Kehadiran 30 Hari Terakhir
        </h3>
        <div id="attendance-trend-chart" class="w-full" style="min-height: 320px;"></div>
    </div>

    {{-- ── Charts Row: Donut + Department Bar ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white dark:bg-card-dark p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold mb-6 text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-icons-round text-sage">pie_chart</span>
                Ringkasan Absen Hari Ini
            </h3>
            <div id="donut-chart" class="flex justify-center" style="min-height: 300px;"></div>
        </div>

        <div class="bg-white dark:bg-card-dark p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold mb-6 text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-icons-round text-lavender">bar_chart</span>
                Kehadiran per Departemen
            </h3>
            <div id="department-chart" class="w-full" style="min-height: 300px;"></div>
        </div>
    </div>

    {{-- ── Bottom Row: Anomalies + Sidebar Widgets ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            {{-- Anomali Terbaru --}}
            <div class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-bold mb-4 text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="material-icons-round text-peach">warning</span>
                    Anomali Terbaru
                </h3>
                @if($recentAnomalies->isEmpty())
                    <div class="flex items-center gap-3 p-4 bg-sage/10 rounded-xl">
                        <span class="material-icons-round text-sage">verified</span>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Tidak ada anomali terdeteksi.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentAnomalies as $anomaly)
                            <div class="flex items-center justify-between p-4 bg-pastel-rose/10 dark:bg-pastel-rose/5 rounded-xl border border-pastel-rose/20">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-pastel-rose/20 flex items-center justify-center flex-shrink-0">
                                        <span class="material-icons-round text-red-400 text-lg">gpp_maybe</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $anomaly->user->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $anomaly->tanggal->format('d M Y') }} &middot;
                                            @if($anomaly->anomaly_flags && is_array($anomaly->anomaly_flags))
                                                {{ implode(', ', array_slice($anomaly->anomaly_flags, 0, 2)) }}
                                            @else
                                                Anomali terdeteksi
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $anomaly->anomaly_score > 70 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                        Score: {{ $anomaly->anomaly_score }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Top Terlambat Bulan Ini --}}
            @if(count($topLateEmployees) > 0)
                <div class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                    <h3 class="text-lg font-bold mb-4 text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-peach">timer</span>
                        Top Terlambat Bulan Ini
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800">
                                    <th class="text-left py-3 px-2 text-xs font-bold text-slate-400 uppercase tracking-wider">#</th>
                                    <th class="text-left py-3 px-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama</th>
                                    <th class="text-left py-3 px-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Departemen</th>
                                    <th class="text-right py-3 px-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topLateEmployees as $idx => $emp)
                                    <tr class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="py-3 px-2 text-slate-500">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-2 font-medium text-slate-900 dark:text-white">{{ $emp['name'] }}</td>
                                        <td class="py-3 px-2 text-slate-500 dark:text-slate-400">{{ $emp['department'] }}</td>
                                        <td class="py-3 px-2 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-peach/30 text-orange-700 dark:text-orange-300">
                                                {{ $emp['late_count'] }}x
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar Widgets --}}
        <div class="flex flex-col gap-6">
            {{-- Pending Actions --}}
            <div class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-lavender">pending_actions</span>
                    Pending Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.absence.index') }}"
                       class="flex items-center justify-between p-3 rounded-xl bg-sky/10 hover:bg-sky/20 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-sky/20 flex items-center justify-center">
                                <span class="material-icons-round text-sky text-lg">assignment</span>
                            </div>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Pengajuan Izin</span>
                        </div>
                        <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full text-xs font-bold
                            {{ $pendingApprovals['izin'] > 0 ? 'bg-sky text-slate-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                            {{ $pendingApprovals['izin'] }}
                        </span>
                    </a>
                    <a href="{{ route('admin.payroll.index') }}"
                       class="flex items-center justify-between p-3 rounded-xl bg-lavender/10 hover:bg-lavender/20 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-lavender/20 flex items-center justify-center">
                                <span class="material-icons-round text-lavender text-lg">payments</span>
                            </div>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Payroll</span>
                        </div>
                        <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full text-xs font-bold
                            {{ $pendingApprovals['payroll'] > 0 ? 'bg-lavender text-slate-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                            {{ $pendingApprovals['payroll'] }}
                        </span>
                    </a>
                </div>
            </div>

            {{-- Violation Summary --}}
            <div class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-pastel-rose">gavel</span>
                    Pelanggaran Bulan Ini
                </h3>
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $violationSummary['total_violations'] }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Total</p>
                    </div>
                    <div class="text-center p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $violationSummary['total_points'] }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Poin</p>
                    </div>
                    <div class="text-center p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $violationSummary['sp_count'] }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">SP</p>
                    </div>
                </div>
            </div>

            @can('approve_team_izin')
            <div class="bg-white dark:bg-card-dark p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 relative">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-12 h-12 rounded-xl bg-peach/20 flex items-center justify-center text-slate-800 dark:text-slate-200">
                        <span class="material-icons-round text-3xl">hourglass_empty</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white leading-tight">Pengajuan Ketidakhadiran</h3>
                        <p class="text-xs text-slate-400">Menunggu Persetujuan</p>
                    </div>
                </div>
                @if($pendingRequest > 0)
                    <div class="absolute top-4 right-4 w-6 h-6 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-full flex items-center justify-center text-[10px] font-bold">
                        {{ $pendingRequest }}
                    </div>
                @endif
                <button onclick="window.location='{{ route('admin.absence.index') }}'"
                    class="mt-6 w-full py-3 bg-sage hover:brightness-95 transition-all rounded-xl font-bold text-sm text-slate-900">
                    Lihat Pengajuan
                </button>
            </div>
            @endcan

            <div class="bg-sky p-6 rounded-2xl shadow-sm border border-sky">
                <p class="text-sm text-slate-700 italic">
                    "Sistem diperbarui secara real-time berdasarkan input perangkat desa di lapangan."
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? '#1e293b' : '#f1f5f9';

    const pastel = {
        sage: '#C8D5B9',
        sky: '#B8D4E3',
        peach: '#F5D5CB',
        lavender: '#D4C5E2',
        rose: '#F0D4D8',
    };

    // ── 30-Day Attendance Trend Line Chart ──
    const trendData = @json($attendanceTrend);

    new ApexCharts(document.querySelector('#attendance-trend-chart'), {
        chart: {
            type: 'area',
            height: 320,
            fontFamily: 'Plus Jakarta Sans, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false },
        },
        series: [
            { name: 'Hadir', data: trendData.hadir },
            { name: 'Terlambat', data: trendData.terlambat },
            { name: 'Alpha', data: trendData.alpha },
        ],
        colors: [pastel.sage, pastel.peach, pastel.rose],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 95, 100],
            },
        },
        stroke: { curve: 'smooth', width: 2.5 },
        xaxis: {
            categories: trendData.dates,
            labels: {
                style: { colors: textColor, fontSize: '11px' },
                rotate: -45,
                rotateAlways: false,
                hideOverlappingLabels: true,
                maxHeight: 60,
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: { style: { colors: textColor, fontSize: '11px' } },
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4,
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            labels: { colors: textColor },
            fontWeight: 600,
            fontSize: '12px',
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: (val) => val + ' orang' },
        },
        dataLabels: { enabled: false },
    }).render();

    // ── Donut Chart (replaces CSS pie chart) ──
    const donutValues = [
        {{ $persentaseHadir }},
        {{ $persentaseTerlambat }},
        {{ $persentaseIzin }},
        {{ $persentaseAlpha }}
    ];

    new ApexCharts(document.querySelector('#donut-chart'), {
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: 'Plus Jakarta Sans, sans-serif',
        },
        series: donutValues,
        labels: ['Hadir', 'Terlambat', 'Izin/Sakit/Dinas', 'Alpha'],
        colors: [pastel.sage, pastel.peach, pastel.lavender, pastel.rose],
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '13px',
                            fontWeight: 600,
                            color: textColor,
                        },
                        value: {
                            show: true,
                            fontSize: '24px',
                            fontWeight: 700,
                            color: isDark ? '#f1f5f9' : '#1e293b',
                            formatter: (val) => val + '%',
                        },
                        total: {
                            show: true,
                            label: 'Hadir',
                            fontSize: '13px',
                            fontWeight: 600,
                            color: textColor,
                            formatter: () => '{{ $persentaseHadir }}%',
                        },
                    },
                },
            },
        },
        stroke: { width: 2, colors: [isDark ? '#1e1e1e' : '#ffffff'] },
        legend: {
            position: 'bottom',
            labels: { colors: textColor },
            fontWeight: 600,
            fontSize: '12px',
        },
        dataLabels: { enabled: false },
        tooltip: {
            y: { formatter: (val) => val + '%' },
        },
    }).render();

    // ── Department Comparison Bar Chart ──
    const deptData = @json($departmentComparison);

    if (deptData.labels.length > 0) {
        new ApexCharts(document.querySelector('#department-chart'), {
            chart: {
                type: 'bar',
                height: 300,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false },
            },
            series: [{
                name: 'Kehadiran',
                data: deptData.rates,
            }],
            colors: [pastel.sky],
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '55%',
                    distributed: false,
                },
            },
            xaxis: {
                categories: deptData.labels,
                labels: {
                    style: { colors: textColor, fontSize: '11px' },
                    rotate: -45,
                    rotateAlways: false,
                    hideOverlappingLabels: true,
                    maxHeight: 80,
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                max: 100,
                labels: {
                    style: { colors: textColor, fontSize: '11px' },
                    formatter: (val) => val + '%',
                },
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4,
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => val + '%',
                style: {
                    fontSize: '11px',
                    fontWeight: 700,
                    colors: ['#475569'],
                },
                offsetY: -6,
            },
            tooltip: {
                y: { formatter: (val) => val + '%' },
                theme: isDark ? 'dark' : 'light',
            },
        }).render();
    } else {
        document.querySelector('#department-chart').innerHTML =
            '<div class="flex items-center justify-center h-full text-slate-400 text-sm">Belum ada data departemen</div>';
    }
});
</script>
@endpush
