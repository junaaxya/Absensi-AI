@extends('layouts.absensi')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1e293b;">Katalog Training</h1>
            <p style="font-size: 14px; color: #64748b; margin-top: 4px;">Tingkatkan kompetensi Anda dengan mengikuti kursus yang tersedia</p>
        </div>
        <a href="{{ route('training.my-courses') }}" style="background: #1e293b; color: white; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px;">
            Kursus Saya
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('training.index') }}" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kursus..."
            style="flex: 1; min-width: 200px; padding: 10px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px;">
        <select name="category" onchange="this.form.submit()" style="padding: 10px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px;">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <select name="difficulty" onchange="this.form.submit()" style="padding: 10px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px;">
            <option value="">Semua Level</option>
            <option value="beginner" {{ request('difficulty') === 'beginner' ? 'selected' : '' }}>Beginner</option>
            <option value="intermediate" {{ request('difficulty') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
            <option value="advanced" {{ request('difficulty') === 'advanced' ? 'selected' : '' }}>Advanced</option>
        </select>
    </form>

    {{-- Course Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
        @forelse($courses as $course)
            <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; transition: box-shadow 0.2s;">
                {{-- Thumbnail --}}
                <div style="height: 160px; background: linear-gradient(135deg, #C8D5B9 0%, #B8D4E3 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <span style="font-size: 48px; opacity: 0.3;">📚</span>
                    @endif
                    @if($course->is_mandatory)
                        <span style="position: absolute; top: 12px; left: 12px; background: #F5D5CB; color: #9a3412; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700;">WAJIB</span>
                    @endif
                    @if(in_array($course->id, $enrolledCourseIds))
                        <span style="position: absolute; top: 12px; right: 12px; background: #C8D5B9; color: #166534; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700;">TERDAFTAR</span>
                    @endif
                </div>

                <div style="padding: 20px;">
                    {{-- Difficulty Badge --}}
                    @php
                        $diffColors = [
                            'beginner' => 'background: #C8D5B9; color: #166534;',
                            'intermediate' => 'background: #B8D4E3; color: #1e40af;',
                            'advanced' => 'background: #D4C5E2; color: #6b21a8;',
                        ];
                    @endphp
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="{{ $diffColors[$course->difficulty] ?? '' }} padding: 3px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                            {{ $course->difficulty }}
                        </span>
                        @if($course->duration_hours)
                            <span style="color: #64748b; font-size: 12px;">{{ $course->duration_hours }} jam</span>
                        @endif
                    </div>

                    <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px; line-height: 1.3;">
                        <a href="{{ route('training.show', $course) }}" style="text-decoration: none; color: inherit;">{{ $course->title }}</a>
                    </h3>

                    <p style="font-size: 13px; color: #64748b; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $course->description }}
                    </p>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                        @if($course->instructor)
                            <span style="font-size: 12px; color: #64748b;">{{ $course->instructor->name }}</span>
                        @else
                            <span></span>
                        @endif
                        <span style="font-size: 12px; color: #94a3b8;">{{ $course->materials_count }} materi</span>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #94a3b8;">
                <p style="font-size: 48px; margin-bottom: 12px;">📚</p>
                <p style="font-size: 16px; font-weight: 600;">Belum ada kursus tersedia</p>
                <p style="font-size: 14px; margin-top: 4px;">Kursus baru akan segera ditambahkan</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 24px;">
        {{ $courses->links() }}
    </div>
</div>
@endsection
