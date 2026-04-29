@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="{{ route('training.my-courses') }}" style="color: #64748b; text-decoration: none; font-size: 14px;">&larr; Kembali</a>
        <button onclick="window.print()" style="background: #1e293b; color: white; padding: 8px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; font-size: 13px;">
            Cetak Sertifikat
        </button>
    </div>

    {{-- Certificate --}}
    <div id="certificate" style="background: white; border: 3px solid #C8D5B9; border-radius: 16px; padding: 60px; text-align: center; position: relative; overflow: hidden;">
        {{-- Decorative corners --}}
        <div style="position: absolute; top: 0; left: 0; width: 80px; height: 80px; border-right: 3px solid #B8D4E3; border-bottom: 3px solid #B8D4E3; border-radius: 0 0 16px 0;"></div>
        <div style="position: absolute; top: 0; right: 0; width: 80px; height: 80px; border-left: 3px solid #B8D4E3; border-bottom: 3px solid #B8D4E3; border-radius: 0 0 0 16px;"></div>
        <div style="position: absolute; bottom: 0; left: 0; width: 80px; height: 80px; border-right: 3px solid #B8D4E3; border-top: 3px solid #B8D4E3; border-radius: 0 16px 0 0;"></div>
        <div style="position: absolute; bottom: 0; right: 0; width: 80px; height: 80px; border-left: 3px solid #B8D4E3; border-top: 3px solid #B8D4E3; border-radius: 16px 0 0 0;"></div>

        <p style="font-size: 14px; color: #94a3b8; text-transform: uppercase; letter-spacing: 4px; font-weight: 600; margin-bottom: 8px;">Sertifikat</p>
        <h1 style="font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Penyelesaian Kursus</h1>
        <div style="width: 80px; height: 3px; background: linear-gradient(90deg, #C8D5B9, #B8D4E3); margin: 0 auto 32px;"></div>

        <p style="font-size: 14px; color: #64748b; margin-bottom: 8px;">Diberikan kepada</p>
        <h2 style="font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 24px;">{{ $enrollment->user->name }}</h2>

        <p style="font-size: 14px; color: #64748b; margin-bottom: 8px;">Telah berhasil menyelesaikan kursus</p>
        <h3 style="font-size: 22px; font-weight: 700; color: #475569; margin-bottom: 32px;">{{ $enrollment->course->title }}</h3>

        <div style="display: flex; justify-content: center; gap: 48px; margin-bottom: 32px;">
            <div>
                <p style="font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Tanggal Selesai</p>
                <p style="font-size: 14px; font-weight: 700; color: #1e293b;">{{ $enrollment->completed_at->format('d F Y') }}</p>
            </div>
            @if($enrollment->score)
                <div>
                    <p style="font-size: 12px; color: #94a3b8; margin-bottom: 4px;">Nilai</p>
                    <p style="font-size: 14px; font-weight: 700; color: #1e293b;">{{ $enrollment->score }}</p>
                </div>
            @endif
        </div>

        <div style="border-top: 1px dashed #e2e8f0; padding-top: 20px;">
            <p style="font-size: 11px; color: #94a3b8;">No. Sertifikat: {{ $enrollment->certificate_number }}</p>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #certificate, #certificate * { visibility: visible; }
        #certificate { position: absolute; left: 0; top: 0; width: 100%; border: 3px solid #C8D5B9 !important; }
    }
</style>
@endsection
