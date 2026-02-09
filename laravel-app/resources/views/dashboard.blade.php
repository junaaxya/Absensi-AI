@extends('layouts.absensi')

@section('content')

    {{-- ================= HEADER ================= --}}
    <div class="card header-card">
        <h1>Hallo, {{ $user->name }}</h1>
        <p>Ringkasan Absensi hari ini</p>
    </div>

    {{-- ================= GRID UTAMA ================= --}}
    <div class="grid">

        {{-- ===== KIRI : AKTIVITAS HARI INI ===== --}}
        <div class="card">
            <h3>Aktivitas Hari Ini</h3>

            <div class="timeline">

                {{-- ===== ABSENSI MASUK ===== --}}
                {{-- ===== ABSENSI MASUK ===== --}}
<div class="timeline-row">
    <div class="timeline-left">
        <span class="dot"></span>
    </div>

    <div class="timeline-right">
        <div class="title-row badge-connect start">
            <div class="badge">
                @if($attendanceToday && $attendanceToday->jam_masuk)
                    @php
                        $rawMasuk = trim($attendanceToday->jam_masuk);

                        // ambil HH:MM dari "HH:MM:SS" atau "YYYY-MM-DD HH:MM:SS"
                        if (strlen($rawMasuk) > 8) {
                            $jamMasukText = substr($rawMasuk, 11, 5);
                        } else {
                            $jamMasukText = substr($rawMasuk, 0, 5);
                        }
                    @endphp

                    {{ $jamMasukText }}
                @else
                    −
                @endif
            </div>

            <strong>Absensi Masuk</strong>
        </div>

        <div class="status-box status-connect
            @if($attendanceToday && $attendanceToday->jam_masuk)
                {{ $attendanceToday->status === 'terlambat' ? 'danger' : 'success' }}
            @else
                warning
            @endif
        ">
            @if(!$attendanceToday || !$attendanceToday->jam_masuk)
                ⚠️ Absen Masuk Belum dilakukan
            @else
                @if($attendanceToday->status === 'terlambat')
                    Terlambat ({{ $jamMasukText }})
                @else
                    Tepat waktu ({{ $jamMasukText }})
                @endif
            @endif
        </div>
    </div>
</div>

                {{-- ===== ABSEN KELUAR ===== --}}
                <div class="timeline-row">
                    <div class="timeline-left">
                        <span class="dot"></span>
                    </div>

                    <div class="timeline-right">
                        <div class="title-row badge-connect end">
                            <div class="badge">−</div>
                            <strong>Absen Keluar</strong>
                        </div>

                        <div class="status-box status-connect">
                            ⏳ Belum dilakukan
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ===== KANAN : JAM KERJA & AKSI ===== --}}
        <div>

            {{-- ===== INFORMASI JAM KERJA ===== --}}
            <div class="card jam-wrapper">
                <h3>Informasi Jam Kerja</h3>

                <div class="jam-container">
                    <div class="jam-card">
                        <span class="jam-label">Jam Masuk</span>
                        <span class="jam-time">08:00</span>
                    </div>

                    <div class="jam-card">
                        <span class="jam-label">Jam Keluar</span>
                        <span class="jam-time">17:00</span>
                    </div>
                </div>
            </div>


            {{-- ===== AKSI ABSENSI ===== --}}
<div class="card action-wrapper">

    <div class="action-row">

        {{-- ABSEN MASUK --}}
        <form method="POST" action="{{ route('absen.masuk') }}">
            @csrf
            <button type="submit" class="action-box">
                Absen Masuk
            </button>
        </form>

        {{-- ABSEN KELUAR --}}
        <form method="POST" action="{{ route('absen.keluar') }}">
            @csrf
            <button type="submit" class="action-box">
                Absen Keluar
            </button>
        </form>

    </div>

    <div class="action-row-full">
        <div class="action-box" onclick="openIzinModal()">
            Pengajuan Ketidakhadiran
        </div>
    </div>

</div>



        </div>
    </div>

    {{-- ================= RIWAYAT ABSENSI ================= --}}
    <div class="card">

        {{-- HEADER + FILTER TANGGAL --}}
        <div class="table-header">
            <h3>Riwayat Absensi</h3>

            <form method="GET" action="{{ route('dashboard') }}" class="filter-date">
                <input type="date" name="tanggal" value="{{ $tanggal }}" max="{{ date('Y-m-d') }}">
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Jam Kerja</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
@if(isset($attendanceHistory) && $attendanceHistory->count() > 0)
    @foreach($attendanceHistory as $i => $row)
        <tr>
            {{-- No --}}
            <td>{{ $i + 1 }}</td>

            {{-- Nama --}}
            <td>{{ $user->name }}</td>

            {{-- Tanggal --}}
            <td>
                {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}
            </td>

            {{-- Jam Masuk + label terlambat --}}
            <td>
                @if($row->jam_masuk)
                    <div>{{ \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') }}</div>

                    @if($row->status === 'terlambat')
                        <div class="badge-terlambat">
                            Terlambat
                        </div>
                    @endif
                @else
                    -
                @endif
            </td>

            {{-- Jam Keluar --}}
{{-- Jam Keluar + badge lembur --}}
<td style="text-align:center;">
    @if($row->jam_keluar)

        @php
            $raw = trim($row->jam_keluar);

            // Kalau formatnya datetime → ambil jamnya
            if (strlen($raw) > 8) {
                $jamKeluar = substr($raw, 11, 5); // HH:MM dari "YYYY-MM-DD HH:MM:SS"
            } else {
                $jamKeluar = substr($raw, 0, 5);  // HH:MM dari "HH:MM:SS"
            }

            $isLembur = $jamKeluar >= '17:00';
        @endphp

        <div>{{ $jamKeluar }}</div>

        {{-- Badge lembur di bawah jam keluar --}}
        @if($isLembur)
            <div class="badge-lembur">
                Lembur
            </div>
        @endif

    @else
        -
    @endif
</td>

            {{-- Jam Kerja --}}
{{-- Jam Kerja --}}
<td style="text-align:center;">
    @if($row->jam_masuk && $row->jam_keluar)

        @php
            try {
                // ambil HH:MM dari jam_masuk
                $rawMasuk = trim($row->jam_masuk);
                if (strlen($rawMasuk) > 8) {
                    $jamMasuk = substr($rawMasuk, 11, 5);
                } else {
                    $jamMasuk = substr($rawMasuk, 0, 5);
                }

                // ambil HH:MM dari jam_keluar
                $rawKeluar = trim($row->jam_keluar);
                if (strlen($rawKeluar) > 8) {
                    $jamKeluar = substr($rawKeluar, 11, 5);
                } else {
                    $jamKeluar = substr($rawKeluar, 0, 5);
                }

                // buat Carbon dari jam murni
                $start = \Carbon\Carbon::createFromFormat('H:i', $jamMasuk);
                $end   = \Carbon\Carbon::createFromFormat('H:i', $jamKeluar);

                // safety kalau lewat tengah malam
                if ($end->lessThan($start)) {
                    $end->addDay();
                }

                $diffMinutes = $start->diffInMinutes($end);
                $hours   = intdiv($diffMinutes, 60);
                $minutes = $diffMinutes % 60;

                $jamKerjaText = $hours . 'j ' . $minutes . 'm';
            } catch (\Exception $e) {
                $jamKerjaText = '-';
            }
        @endphp

        {{ $jamKerjaText }}

    @else
        -
    @endif
</td>

            {{-- STATUS HADIR --}}
            <td>
                @if(in_array($row->status, ['izin','sakit','cuti','dinas']))
                    <span class="badge-hadir danger">Tidak Hadir</span>
                @elseif($row->jam_keluar)
                    <span class="badge-hadir success">Hadir</span>
                @else
                    <span class="badge-hadir secondary">Belum Hadir</span>
                @endif
            </td>


            {{-- KETERANGAN --}}
            <td>
                @if(in_array($row->status, ['izin','sakit','cuti','dinas']))
                    {{ ucfirst($row->status) }}
                @else
                    -
                @endif
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" class="empty">
            Belum ada data absensi
        </td>
    </tr>
@endif
</tbody>


        </table>
    </div>

    <!-- ================= MODAL PENGAJUAN KETIDAKHADIRAN ================= -->
<div id="izinModal" class="modal-overlay">

  <div class="modal-box large">

    <div class="modal-header">
      <h3>Pengajuan Ketidakhadiran</h3>
      <span class="modal-close" onclick="closeIzinModal()">✖</span>
    </div>

    <div class="modal-body">
        <div class="modal-form-wrapper">

      <form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data">
        @csrf

         <div class="modal-field">
        <label>Jenis Pengajuan</label>
        <select name="jenis" required>
            <option value="izin">Izin</option>
            <option value="sakit">Sakit</option>
            <option value="cuti">Cuti</option>
            <option value="dinas">Dinas</option>
        </select>
    </div>

    <div class="modal-date-row">
        <div class="modal-field">
            <label>Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" required>
        </div>

        <div class="modal-field">
            <label>Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" required>
        </div>
    </div>

    <div class="modal-field">
        <label>Alasan</label>
        <textarea name="alasan" required></textarea>
    </div>

    <div class="modal-field">
        <label>Lampirkan Foto atau Dokumen</label>
        <input 
            type="file" 
            name="dokumen"
            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
        >

        <small class="upload-hint">
            Format: PDF, JPG, JPEG, PNG, DOC, DOCX
        </small>
        <small class="upload-hint">
            Maksimal ukuran file 5 MB
        </small>
    </div>

        <!-- ACTION -->
        <div class="modal-actions center">
          <button type="submit" class="btn-primary">Ajukan</button>
          <button type="button" class="btn-secondary" onclick="closeIzinModal()">✖ Batal</button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- ================= SCRIPT MODAL ================= -->
<script>
function openIzinModal() {
  document.getElementById("izinModal").style.display = "flex";
}

function closeIzinModal() {
  document.getElementById("izinModal").style.display = "none";
}
</script>


@endsection
