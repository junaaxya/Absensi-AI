@extends('layouts.app')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1e293b;">Kursus Saya</h1>
            <p style="font-size: 14px; color: #64748b; margin-top: 4px;">Pantau progres belajar Anda</p>
        </div>
        <a href="{{ route('training.index') }}" style="background: #1e293b; color: white; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px;">
            Katalog Kursus
        </a>
    </div>

    @forelse($enrollments as $enrollment)
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        @php
                            $statusColors = [
                                'enrolled' => 'background: #B8D4E3; color: #1e40af;',
                                'in_progress' => 'background: #F5D5CB; color: #9a3412;',
                                'completed' => 'background: #C8D5B9; color: #166534;',
                                'dropped' => 'background: #f1f5f9; color: #64748b;',
                            ];
                            $statusLabels = [
                                'enrolled' => 'Terdaftar',
                                'in_progress' => 'Sedang Belajar',
                                'completed' => 'Selesai',
                                'dropped' => 'Berhenti',
                            ];
                        @endphp
                        <span style="{{ $statusColors[$enrollment->status] ?? '' }} padding: 3px 10px; border-radius: 8px; font-size: 11px; font-weight: 700;">
                            {{ $statusLabels[$enrollment->status] ?? $enrollment->status }}
                        </span>
                    </div>

                    <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                        <a href="{{ route('training.show', $enrollment->course) }}" style="text-decoration: none; color: inherit;">{{ $enrollment->course->title }}</a>
                    </h3>

                    @if($enrollment->course->instructor)
                        <p style="font-size: 13px; color: #94a3b8; margin-bottom: 12px;">Instruktur: {{ $enrollment->course->instructor->name }}</p>
                    @endif

                    {{-- Progress Bar --}}
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span style="font-size: 12px; color: #64748b;">Progres</span>
                            <span style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $enrollment->progress_percentage }}%</span>
                        </div>
                        <div style="width: 100%; height: 6px; background: #f1f5f9; border-radius: 3px; overflow: hidden;">
                            <div style="width: {{ $enrollment->progress_percentage }}%; height: 100%; background: linear-gradient(90deg, #C8D5B9, #B8D4E3); border-radius: 3px;"></div>
                        </div>
                    </div>

                    <p style="font-size: 12px; color: #94a3b8;">
                        Terdaftar: {{ $enrollment->enrolled_at->format('d M Y') }}
                        @if($enrollment->completed_at)
                            &middot; Selesai: {{ $enrollment->completed_at->format('d M Y') }}
                        @endif
                    </p>
                </div>

                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                    @if($enrollment->status === 'completed' && $enrollment->certificate_number)
                        <a href="{{ route('training.certificate', $enrollment) }}" style="background: #D4C5E2; color: #6b21a8; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 13px;">
                            Sertifikat
                        </a>
                    @endif
                    <a href="{{ route('training.learn', $enrollment->course) }}" style="background: #1e293b; color: white; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 13px;">
                        {{ $enrollment->status === 'completed' ? 'Lihat' : 'Lanjutkan' }}
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 60px 20px; color: #94a3b8; background: white; border-radius: 16px; border: 1px solid #e2e8f0;">
            <p style="font-size: 48px; margin-bottom: 12px;">📚</p>
            <p style="font-size: 16px; font-weight: 600;">Belum ada kursus yang diikuti</p>
            <p style="font-size: 14px; margin-top: 4px;">Jelajahi katalog kursus untuk mulai belajar</p>
            <a href="{{ route('training.index') }}" style="display: inline-block; margin-top: 16px; background: #1e293b; color: white; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px;">
                Lihat Katalog
            </a>
        </div>
    @endforelse
</div>
@endsection
