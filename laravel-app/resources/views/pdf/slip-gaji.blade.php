<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $detail->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        .container { max-width: 700px; margin: 0 auto; padding: 30px; }
        .header { text-align: center; border-bottom: 3px solid #1e293b; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .header .address { font-size: 10px; color: #64748b; margin-top: 4px; }
        .header .title { font-size: 14px; font-weight: bold; margin-top: 12px; text-transform: uppercase; letter-spacing: 3px; color: #334155; }
        .header .period { font-size: 11px; color: #64748b; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .info-table .label { width: 120px; color: #64748b; font-weight: 600; }
        .info-table .value { color: #1e293b; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 8px; margin-top: 18px; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table td { padding: 4px 0; }
        .items-table .name { color: #475569; }
        .items-table .amount { text-align: right; font-weight: 600; font-family: 'DejaVu Sans Mono', monospace; }
        .items-table .amount.positive { color: #15803d; }
        .items-table .amount.negative { color: #dc2626; }
        .subtotal { border-top: 1px solid #e2e8f0; font-weight: bold; }
        .subtotal td { padding-top: 6px; }
        .net-box { background: #1e293b; color: white; text-align: center; padding: 18px; margin-top: 24px; border-radius: 8px; }
        .net-box .label { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: #94a3b8; }
        .net-box .amount { font-size: 24px; font-weight: bold; font-family: 'DejaVu Sans Mono', monospace; margin-top: 4px; }
        .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #94a3b8; }
        .two-col { display: table; width: 100%; }
        .two-col .col { display: table-cell; width: 48%; vertical-align: top; }
        .two-col .col-gap { display: table-cell; width: 4%; }
        .bpjs-section { margin-top: 18px; }
        .bpjs-subtitle { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $settings->company_name ?? 'Perusahaan' }}</h1>
            @if($settings->company_address)
                <div class="address">{{ $settings->company_address }}</div>
            @endif
            <div class="title">Slip Gaji</div>
            <div class="period">Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $detail->payrollPeriod->period_month)->translatedFormat('F Y') }}</div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Nama</td>
                <td class="value">: {{ $detail->user->name }}</td>
                <td class="label">Jabatan</td>
                <td class="value">: {{ $detail->user->jabatan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td class="value">: {{ $detail->user->nik ?? '-' }}</td>
                <td class="label">Status</td>
                <td class="value">: {{ ucfirst($detail->user->status_karyawan ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label">Departemen</td>
                <td class="value">: {{ $detail->user->department->name ?? '-' }}</td>
                <td class="label">Hari Kerja</td>
                <td class="value">: {{ $detail->present_days }}/{{ $detail->working_days }} hari</td>
            </tr>
        </table>

        <div class="two-col">
            <div class="col">
                <div class="section-title">Pendapatan</div>
                <table class="items-table">
                    @foreach($detail->items->where('component_type', 'earning') as $item)
                    <tr>
                        <td class="name">{{ $item->component_name }}</td>
                        <td class="amount positive">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="subtotal">
                        <td><strong>Total Pendapatan</strong></td>
                        <td class="amount positive">Rp {{ number_format($detail->total_earnings, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-gap"></div>
            <div class="col">
                <div class="section-title">Potongan</div>
                <table class="items-table">
                    @foreach($detail->items->where('component_type', 'deduction') as $item)
                    <tr>
                        <td class="name">{{ $item->component_name }}</td>
                        <td class="amount negative">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="subtotal">
                        <td><strong>Total Potongan</strong></td>
                        <td class="amount negative">Rp {{ number_format($detail->total_deductions, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="bpjs-section">
            <div class="section-title">BPJS</div>
            <div class="two-col">
                <div class="col">
                    <div class="bpjs-subtitle">Ditanggung Perusahaan</div>
                    <table class="items-table">
                        @foreach($detail->items->where('component_type', 'bpjs_company') as $item)
                        <tr>
                            <td class="name">{{ $item->component_name }}</td>
                            <td class="amount">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="subtotal">
                            <td><strong>Total</strong></td>
                            <td class="amount">Rp {{ number_format($detail->total_bpjs_company, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-gap"></div>
                <div class="col">
                    <div class="bpjs-subtitle">Ditanggung Karyawan</div>
                    <table class="items-table">
                        @foreach($detail->items->where('component_type', 'bpjs_employee') as $item)
                        <tr>
                            <td class="name">{{ $item->component_name }}</td>
                            <td class="amount negative">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="subtotal">
                            <td><strong>Total</strong></td>
                            <td class="amount negative">Rp {{ number_format($detail->total_bpjs_employee, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="section-title">PPh 21</div>
        <table class="items-table">
            <tr>
                <td class="name">Pajak Penghasilan Pasal 21</td>
                <td class="amount negative">Rp {{ number_format($detail->pph21_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="net-box">
            <div class="label">Gaji Bersih (Take Home Pay)</div>
            <div class="amount">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</div>
        </div>

        <div class="footer">
            Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} &mdash; Dokumen ini bersifat rahasia dan hanya untuk penerima yang bersangkutan
        </div>
    </div>
</body>
</html>
