@extends('layouts.absensi')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    {{-- Progress Bar --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 16px 24px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <h2 style="font-size: 16px; font-weight: 700; color: #1e293b;">{{ $course->title }}</h2>
            <span id="progress-text" style="font-size: 14px; font-weight: 700; color: #64748b;">{{ $enrollment->progress_percentage }}%</span>
        </div>
        <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
            <div id="progress-bar" style="width: {{ $enrollment->progress_percentage }}%; height: 100%; background: linear-gradient(90deg, #C8D5B9, #B8D4E3); border-radius: 4px; transition: width 0.5s ease;"></div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 20px;">
        {{-- Sidebar - Material List --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; height: fit-content;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9;">
                <h3 style="font-size: 14px; font-weight: 700; color: #1e293b;">Daftar Materi</h3>
            </div>
            @foreach($course->materials as $material)
                <a href="javascript:void(0)" onclick="loadMaterial({{ $material->id }})"
                    id="material-nav-{{ $material->id }}"
                    style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-bottom: 1px solid #f8fafc; text-decoration: none; transition: background 0.2s; {{ $currentMaterial && $currentMaterial->id === $material->id ? 'background: #f1f5f9;' : '' }}"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='{{ $currentMaterial && $currentMaterial->id === $material->id ? '#f1f5f9' : 'transparent' }}'">
                    <div id="material-check-{{ $material->id }}" style="width: 24px; height: 24px; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px;
                        {{ in_array($material->id, $completedMaterialIds) ? 'background: #C8D5B9; color: #166534;' : 'background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0;' }}">
                        {{ in_array($material->id, $completedMaterialIds) ? '✓' : $loop->iteration }}
                    </div>
                    <span style="font-size: 13px; color: #475569; font-weight: 500; line-height: 1.3;">{{ $material->title }}</span>
                </a>
            @endforeach
            <div style="padding: 16px 20px;">
                <a href="{{ route('training.my-courses') }}" style="font-size: 13px; color: #64748b; text-decoration: none;">&larr; Kembali</a>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden;">
            @if($currentMaterial)
                <div style="padding: 24px 32px; border-bottom: 1px solid #f1f5f9;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            @php
                                $typeIcons = ['document' => '📄', 'video' => '🎬', 'link' => '🔗', 'quiz' => '📝'];
                            @endphp
                            <span style="font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">{{ $typeIcons[$currentMaterial->type] ?? '' }} {{ $currentMaterial->type }}</span>
                            <h2 id="material-title" style="font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 4px;">{{ $currentMaterial->title }}</h2>
                        </div>
                        @if($currentMaterial->duration_minutes)
                            <span style="font-size: 13px; color: #94a3b8;">{{ $currentMaterial->duration_minutes }} menit</span>
                        @endif
                    </div>
                </div>

                <div id="material-content" style="padding: 32px; min-height: 300px;">
                    @if($currentMaterial->type === 'video' && $currentMaterial->content)
                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">
                            <iframe src="{{ $currentMaterial->content }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; border-radius: 12px;" allowfullscreen></iframe>
                        </div>
                    @elseif($currentMaterial->type === 'link' && $currentMaterial->content)
                        <a href="{{ $currentMaterial->content }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: #B8D4E3; color: #1e40af; padding: 12px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px;">
                            🔗 Buka Link Materi
                        </a>
                        <p style="margin-top: 12px; font-size: 13px; color: #94a3b8;">{{ $currentMaterial->content }}</p>
                    @elseif($currentMaterial->file_path)
                        <a href="{{ asset('storage/' . $currentMaterial->file_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: #C8D5B9; color: #166534; padding: 12px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px;">
                            📥 Download Materi
                        </a>
                    @endif

                    @if($currentMaterial->content && $currentMaterial->type !== 'video' && $currentMaterial->type !== 'link')
                        <div style="font-size: 14px; color: #475569; line-height: 1.8; white-space: pre-wrap;">{{ $currentMaterial->content }}</div>
                    @endif
                </div>

                {{-- Mark Complete Button --}}
                <div style="padding: 20px 32px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                    <button id="complete-btn-{{ $currentMaterial->id }}"
                        onclick="markComplete({{ $currentMaterial->id }})"
                        style="padding: 10px 24px; border-radius: 12px; border: none; cursor: pointer; font-weight: 700; font-size: 14px;
                        {{ in_array($currentMaterial->id, $completedMaterialIds) ? 'background: #C8D5B9; color: #166534;' : 'background: #1e293b; color: white;' }}"
                        {{ in_array($currentMaterial->id, $completedMaterialIds) ? 'disabled' : '' }}>
                        {{ in_array($currentMaterial->id, $completedMaterialIds) ? '✓ Selesai' : 'Tandai Selesai' }}
                    </button>
                </div>
            @else
                <div style="padding: 60px; text-align: center; color: #94a3b8;">
                    <p style="font-size: 48px; margin-bottom: 12px;">📚</p>
                    <p style="font-size: 16px; font-weight: 600;">Belum ada materi</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function markComplete(materialId) {
        const btn = document.getElementById('complete-btn-' + materialId);
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        fetch('/training/material/' + materialId + '/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.textContent = '✓ Selesai';
                btn.style.background = '#C8D5B9';
                btn.style.color = '#166534';

                const check = document.getElementById('material-check-' + materialId);
                if (check) {
                    check.style.background = '#C8D5B9';
                    check.style.color = '#166534';
                    check.style.border = 'none';
                    check.textContent = '✓';
                }

                document.getElementById('progress-bar').style.width = data.progress + '%';
                document.getElementById('progress-text').textContent = data.progress + '%';

                if (data.status === 'completed') {
                    alert('Selamat! Anda telah menyelesaikan kursus ini. Sertifikat: ' + data.certificate_number);
                }
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.textContent = 'Tandai Selesai';
        });
    }

    function loadMaterial(materialId) {
        window.location.href = '{{ route("training.learn", $course) }}?material=' + materialId;
    }
</script>
@endsection
