@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <a href="{{ route('training.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: #64748b; text-decoration: none; font-size: 14px; margin-bottom: 20px;">
        &larr; Kembali ke Katalog
    </a>

    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden;">
        {{-- Header --}}
        <div style="height: 200px; background: linear-gradient(135deg, #C8D5B9 0%, #B8D4E3 100%); display: flex; align-items: center; justify-content: center; position: relative;">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <span style="font-size: 64px; opacity: 0.3;">📚</span>
            @endif
            @if($course->is_mandatory)
                <span style="position: absolute; top: 16px; left: 16px; background: #F5D5CB; color: #9a3412; padding: 6px 14px; border-radius: 10px; font-size: 12px; font-weight: 700;">WAJIB</span>
            @endif
        </div>

        <div style="padding: 32px;">
            {{-- Badges --}}
            @php
                $diffColors = [
                    'beginner' => 'background: #C8D5B9; color: #166534;',
                    'intermediate' => 'background: #B8D4E3; color: #1e40af;',
                    'advanced' => 'background: #D4C5E2; color: #6b21a8;',
                ];
            @endphp
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; flex-wrap: wrap;">
                <span style="{{ $diffColors[$course->difficulty] ?? '' }} padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                    {{ $course->difficulty }}
                </span>
                @if($course->category)
                    <span style="background: #f1f5f9; color: #475569; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                        {{ $course->category }}
                    </span>
                @endif
                @if($course->duration_hours)
                    <span style="color: #64748b; font-size: 13px;">{{ $course->duration_hours }} jam</span>
                @endif
            </div>

            <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">{{ $course->title }}</h1>

            @if($course->instructor)
                <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Instruktur: <strong>{{ $course->instructor->name }}</strong></p>
            @endif

            <p style="font-size: 14px; color: #475569; line-height: 1.7; margin-bottom: 24px;">{{ $course->description }}</p>

            {{-- Stats --}}
            <div style="display: flex; gap: 24px; margin-bottom: 24px; flex-wrap: wrap;">
                <div style="text-align: center;">
                    <p style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ $course->materials->count() }}</p>
                    <p style="font-size: 12px; color: #94a3b8;">Materi</p>
                </div>
                <div style="text-align: center;">
                    <p style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ $enrolledCount }}</p>
                    <p style="font-size: 12px; color: #94a3b8;">Peserta</p>
                </div>
                @if($course->max_participants)
                    <div style="text-align: center;">
                        <p style="font-size: 24px; font-weight: 700; color: #1e293b;">{{ $course->max_participants }}</p>
                        <p style="font-size: 12px; color: #94a3b8;">Maks. Peserta</p>
                    </div>
                @endif
            </div>

            {{-- Enroll Button --}}
            @if($enrollment)
                @if($enrollment->status === 'completed')
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a href="{{ route('training.learn', $course) }}" style="background: #C8D5B9; color: #166534; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 14px;">
                            Lihat Kembali
                        </a>
                        @if($enrollment->certificate_number)
                            <a href="{{ route('training.certificate', $enrollment) }}" style="background: #D4C5E2; color: #6b21a8; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 14px;">
                                Lihat Sertifikat
                            </a>
                        @endif
                    </div>
                @else
                    <a href="{{ route('training.learn', $course) }}" style="background: #1e293b; color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 14px; display: inline-block;">
                        Lanjutkan Belajar ({{ $enrollment->progress_percentage }}%)
                    </a>
                @endif
            @else
                <form method="POST" action="{{ route('training.enroll', $course) }}">
                    @csrf
                    <button type="submit" style="background: #1e293b; color: white; padding: 12px 24px; border-radius: 12px; border: none; cursor: pointer; font-weight: 700; font-size: 14px;">
                        Daftar Kursus
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Materials List --}}
    @if($course->materials->count() > 0)
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; margin-top: 24px; overflow: hidden;">
            <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9;">
                <h2 style="font-size: 18px; font-weight: 700; color: #1e293b;">Daftar Materi</h2>
            </div>
            @foreach($course->materials as $index => $material)
                <div style="padding: 16px 24px; border-bottom: 1px solid #f8fafc; display: flex; align-items: center; gap: 16px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #64748b; flex-shrink: 0;">
                        {{ $index + 1 }}
                    </div>
                    <div style="flex: 1;">
                        <p style="font-size: 14px; font-weight: 600; color: #1e293b;">{{ $material->title }}</p>
                        <div style="display: flex; gap: 12px; margin-top: 4px;">
                            @php
                                $typeIcons = ['document' => '📄', 'video' => '🎬', 'link' => '🔗', 'quiz' => '📝'];
                            @endphp
                            <span style="font-size: 12px; color: #94a3b8;">{{ $typeIcons[$material->type] ?? '' }} {{ ucfirst($material->type) }}</span>
                            @if($material->duration_minutes)
                                <span style="font-size: 12px; color: #94a3b8;">{{ $material->duration_minutes }} menit</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
