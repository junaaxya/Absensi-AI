<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Karyawan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #334155;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .header .company-address {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .header .company-contact {
            font-size: 8px;
            color: #94a3b8;
        }

        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin: 12px 0 4px;
        }

        .report-meta {
            text-align: center;
            font-size: 9px;
            color: #64748b;
            margin-bottom: 16px;
        }

        .report-meta span {
            margin: 0 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead th {
            background-color: #334155;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            padding: 6px 4px;
            text-align: left;
            border: 1px solid #475569;
        }

        table tbody td {
            padding: 4px;
            font-size: 9px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        table tbody tr:hover {
            background-color: #f1f5f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-section {
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .summary-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 8px;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 4px;
        }

        .summary-table thead th {
            background-color: #475569;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-hadir {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-terlambat {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .badge-alpha {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-izin {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-auto {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }

        .page-break {
            page-break-after: always;
        }

        .stats-grid {
            width: 100%;
            margin-bottom: 16px;
        }

        .stats-grid td {
            width: 25%;
            padding: 8px;
            text-align: center;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .stats-grid .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
        }

        .stats-grid .stat-label {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <div class="company-name">{{ $settings->company_name ?? 'Perusahaan' }}</div>
        @if($settings->company_address ?? false)
            <div class="company-address">{{ $settings->company_address }}</div>
        @endif
        <div class="company-contact">
            @if($settings->company_phone ?? false)
                Telp: {{ $settings->company_phone }}
            @endif
            @if($settings->company_email ?? false)
                | Email: {{ $settings->company_email }}
            @endif
            @if($settings->company_website ?? false)
                | {{ $settings->company_website }}
            @endif
        </div>
    </div>

    {{-- REPORT TITLE --}}
    <div class="report-title">Laporan Absensi Karyawan</div>
    <div class="report-meta">
        <span>Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</span>
        @if($department)
            <span>| Departemen: {{ $department->name }}</span>
        @endif
        <span>| Dicetak: {{ now()->format('d M Y H:i') }}</span>
    </div>

    {{-- OVERVIEW STATS --}}
    @php
        $totalHadir = $summary->sum('hadir');
        $totalTerlambat = $summary->sum('terlambat');
        $totalAlpha = $summary->sum('alpha');
        $totalIzin = $summary->sum('izin');
    @endphp
    <table class="stats-grid">
        <tr>
            <td>
                <div class="stat-value">{{ $summary->count() }}</div>
                <div class="stat-label">Total Karyawan</div>
            </td>
            <td>
                <div class="stat-value" style="color: #166534;">{{ $totalHadir }}</div>
                <div class="stat-label">Total Kehadiran</div>
            </td>
            <td>
                <div class="stat-value" style="color: #854d0e;">{{ $totalTerlambat }}</div>
                <div class="stat-label">Total Terlambat</div>
            </td>
            <td>
                <div class="stat-value" style="color: #991b1b;">{{ $totalAlpha }}</div>
                <div class="stat-label">Total Alpha</div>
            </td>
        </tr>
    </table>

    {{-- DETAIL TABLE --}}
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Departemen</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th class="text-center">Status</th>
                <th>Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $att)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $att->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $att->user->name ?? '-' }}</td>
                    <td>{{ $att->user->department->name ?? '-' }}</td>
                    <td>{{ $att->jam_masuk ? \Carbon\Carbon::parse($att->jam_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $att->jam_keluar ? \Carbon\Carbon::parse($att->jam_keluar)->format('H:i') : '-' }}</td>
                    <td class="text-center">
                        @php $status = $att->status ?? 'hadir'; @endphp
                        @if($status === 'terlambat')
                            <span class="badge badge-terlambat">Terlambat</span>
                        @elseif($status === 'alpha')
                            <span class="badge badge-alpha">Alpha</span>
                        @elseif(in_array($status, ['izin', 'sakit', 'cuti']))
                            <span class="badge badge-izin">{{ ucfirst($status) }}</span>
                        @elseif($status === 'auto_checkout')
                            <span class="badge badge-auto">Auto</span>
                        @else
                            <span class="badge badge-hadir">Hadir</span>
                        @endif
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($att->kegiatan ?? '-', 40) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data absensi untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SUMMARY PER EMPLOYEE --}}
    @if($summary->isNotEmpty())
        <div class="summary-section">
            <div class="summary-title">Ringkasan Per Karyawan</div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 30px;">No</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Departemen</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Terlambat</th>
                        <th class="text-center">Alpha</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Total Jam Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['nik'] ?? '-' }}</td>
                            <td>{{ $row['department'] }}</td>
                            <td class="text-center">{{ $row['hadir'] }}</td>
                            <td class="text-center">{{ $row['terlambat'] }}</td>
                            <td class="text-center">{{ $row['alpha'] }}</td>
                            <td class="text-center">{{ $row['izin'] }}</td>
                            <td class="text-center">{{ $row['total_jam'] }} jam</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                        <td colspan="4" class="text-right" style="padding: 6px 4px;">Total</td>
                        <td class="text-center" style="padding: 6px 4px;">{{ $totalHadir }}</td>
                        <td class="text-center" style="padding: 6px 4px;">{{ $totalTerlambat }}</td>
                        <td class="text-center" style="padding: 6px 4px;">{{ $totalAlpha }}</td>
                        <td class="text-center" style="padding: 6px 4px;">{{ $totalIzin }}</td>
                        <td class="text-center" style="padding: 6px 4px;">{{ $summary->sum('total_jam') }} jam</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        {{ $settings->company_name ?? 'Perusahaan' }} &mdash; Laporan digenerate otomatis pada {{ now()->format('d M Y H:i:s') }}
    </div>
</body>
</html>
