@extends('layouts.admin')

@section('header-title', $jobPosition->title)
@section('header-subtitle', 'Detail posisi & pipeline kandidat')

@section('content')
{{-- Position Header --}}
<div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $jobPosition->title }}</h2>
                @php
                    $statusColors = [
                        'draft' => 'bg-neutral-stone/60 text-slate-700',
                        'open' => 'bg-primary/40 text-green-800',
                        'closed' => 'bg-pastel-rose/40 text-rose-800',
                        'on_hold' => 'bg-peach/40 text-orange-800',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$jobPosition->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst(str_replace('_', ' ', $jobPosition->status)) }}
                </span>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
                @if($jobPosition->department)
                <span class="flex items-center gap-1">
                    <span class="material-icons-round text-[16px]">business</span>
                    {{ $jobPosition->department->name }}
                </span>
                @endif
                <span class="flex items-center gap-1">
                    <span class="material-icons-round text-[16px]">badge</span>
                    @php
                        $typeLabels = ['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Kontrak', 'internship' => 'Magang'];
                    @endphp
                    {{ $typeLabels[$jobPosition->employment_type] ?? $jobPosition->employment_type }}
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-icons-round text-[16px]">person_add</span>
                    {{ $jobPosition->openings }} lowongan
                </span>
                @if($jobPosition->salary_range_min || $jobPosition->salary_range_max)
                <span class="flex items-center gap-1">
                    <span class="material-icons-round text-[16px]">payments</span>
                    @if($jobPosition->salary_range_min && $jobPosition->salary_range_max)
                        Rp {{ number_format($jobPosition->salary_range_min, 0, ',', '.') }} - {{ number_format($jobPosition->salary_range_max, 0, ',', '.') }}
                    @elseif($jobPosition->salary_range_min)
                        Mulai Rp {{ number_format($jobPosition->salary_range_min, 0, ',', '.') }}
                    @else
                        Hingga Rp {{ number_format($jobPosition->salary_range_max, 0, ',', '.') }}
                    @endif
                </span>
                @endif
            </div>
            @if($jobPosition->description)
            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $jobPosition->description }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.recruitment.positions.edit', $jobPosition) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                <span class="material-icons-round text-[16px]">edit</span>
                Edit
            </a>
            <button onclick="document.getElementById('addCandidateModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                <span class="material-icons-round text-[16px]">person_add</span>
                Tambah Kandidat
            </button>
        </div>
    </div>
</div>

{{-- Candidate Pipeline (Kanban) --}}
<div class="overflow-x-auto pb-4" x-data="pipelineManager()">
    <div class="flex gap-4 min-w-max">
        @php
            $stageConfig = [
                'applied' => ['label' => 'Applied', 'color' => 'border-neutral-stone', 'bg' => 'bg-neutral-stone/20', 'badge' => 'bg-neutral-stone/60 text-slate-700'],
                'screening' => ['label' => 'Screening', 'color' => 'border-sky', 'bg' => 'bg-sky/10', 'badge' => 'bg-sky/40 text-sky-800'],
                'interview' => ['label' => 'Interview', 'color' => 'border-lavender', 'bg' => 'bg-lavender/10', 'badge' => 'bg-lavender/40 text-purple-800'],
                'assessment' => ['label' => 'Assessment', 'color' => 'border-peach', 'bg' => 'bg-peach/10', 'badge' => 'bg-peach/40 text-orange-800'],
                'offered' => ['label' => 'Offered', 'color' => 'border-green-200', 'bg' => 'bg-green-50', 'badge' => 'bg-green-100 text-green-800'],
                'hired' => ['label' => 'Hired', 'color' => 'border-primary', 'bg' => 'bg-primary/10', 'badge' => 'bg-primary/40 text-green-800'],
            ];
        @endphp

        @foreach($stageConfig as $stageKey => $config)
        <div class="w-72 flex-shrink-0">
            <div class="rounded-2xl border-2 {{ $config['color'] }} {{ $config['bg'] }} p-4 min-h-[400px]"
                @dragover.prevent
                @drop="dropCandidate($event, '{{ $stageKey }}')">

                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $config['label'] }}</h4>
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold {{ $config['badge'] }}">
                        {{ $pipeline[$stageKey]->count() }}
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach($pipeline[$stageKey] as $candidate)
                    <div class="bg-white dark:bg-card-dark rounded-xl border border-slate-200 dark:border-slate-700 p-4 shadow-sm cursor-grab active:cursor-grabbing hover:shadow-soft transition-all"
                        draggable="true"
                        @dragstart="dragCandidate($event, {{ $candidate->id }})"
                        @dragend="dragEnd($event)">
                        <a href="{{ route('admin.recruitment.candidates.show', $candidate) }}"
                            class="block">
                            <p class="font-bold text-slate-900 dark:text-white text-sm mb-1 hover:text-sky-600 transition-colors">
                                {{ $candidate->name }}
                            </p>
                            <div class="flex items-center gap-2 mb-2">
                                @if($candidate->rating)
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                    <span class="material-icons-round text-[12px] {{ $i <= $candidate->rating ? 'text-amber-400' : 'text-slate-300' }}">star</span>
                                    @endfor
                                </div>
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                @php
                                    $sourceColors = [
                                        'website' => 'bg-sky/30 text-sky-800',
                                        'referral' => 'bg-primary/30 text-green-800',
                                        'linkedin' => 'bg-blue-100 text-blue-800',
                                        'jobstreet' => 'bg-lavender/30 text-purple-800',
                                        'other' => 'bg-slate-100 text-slate-600',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sourceColors[$candidate->source] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($candidate->source) }}
                                </span>
                                <span>{{ $candidate->applied_at->format('d M') }}</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Add Candidate Modal --}}
<div id="addCandidateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('addCandidateModal').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Kandidat</h3>
            <button onclick="document.getElementById('addCandidateModal').classList.add('hidden')"
                class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-icons-round text-slate-400">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.recruitment.candidates.store', $jobPosition) }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                    placeholder="Nama kandidat">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Email *</label>
                <input type="email" name="email" required
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                    placeholder="email@contoh.com">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Telepon</label>
                <input type="text" name="phone"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                    placeholder="08xxxxxxxxxx">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Resume (PDF/DOC/DOCX, max 5MB)</label>
                <input type="file" name="resume" accept=".pdf,.doc,.docx"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Cover Letter</label>
                <textarea name="cover_letter" rows="3"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                    placeholder="Surat pengantar (opsional)"></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Sumber *</label>
                <select name="source" required
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
                    <option value="website">Website</option>
                    <option value="referral">Referral</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="jobstreet">JobStreet</option>
                    <option value="other" selected>Lainnya</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('addCandidateModal').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-bold text-slate-600 hover:text-slate-800 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                    <span class="material-icons-round text-[16px]">person_add</span>
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<div class="fixed bottom-4 right-4 z-50 bg-pastel-rose border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl shadow-lg max-w-sm">
    <div class="flex items-start gap-2">
        <span class="material-icons-round text-[18px] mt-0.5">error</span>
        <div>
            @foreach($errors->all() as $error)
            <p class="text-sm font-medium">{{ $error }}</p>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function pipelineManager() {
    return {
        draggedId: null,

        dragCandidate(event, candidateId) {
            this.draggedId = candidateId;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', candidateId);
            event.target.closest('[draggable]').classList.add('opacity-50');
        },

        dragEnd(event) {
            event.target.closest('[draggable]').classList.remove('opacity-50');
        },

        async dropCandidate(event, newStatus) {
            event.preventDefault();
            const candidateId = event.dataTransfer.getData('text/plain');
            if (!candidateId) return;

            try {
                const response = await fetch(`/admin/recruitment/candidates/${candidateId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            || document.querySelector('input[name="_token"]')?.value,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus }),
                });

                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Failed to update status:', error);
            }
        }
    };
}
</script>
@endpush
