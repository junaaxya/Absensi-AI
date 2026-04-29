@extends('layouts.admin')

@section('header-title', $candidate->name)
@section('header-subtitle', 'Detail kandidat')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.recruitment.positions.show', $candidate->jobPosition) }}"
        class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition-colors">
        <span class="material-icons-round text-[16px]">arrow_back</span>
        Kembali ke {{ $candidate->jobPosition->title }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profile Card --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3">
                    <span class="material-icons-round text-[36px] text-slate-400">person</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $candidate->name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $candidate->jobPosition->title }}</p>
                @php
                    $statusColors = [
                        'applied' => 'bg-neutral-stone/60 text-slate-700',
                        'screening' => 'bg-sky/40 text-sky-800',
                        'interview' => 'bg-lavender/40 text-purple-800',
                        'assessment' => 'bg-peach/40 text-orange-800',
                        'offered' => 'bg-green-100 text-green-800',
                        'hired' => 'bg-primary/40 text-green-800',
                        'rejected' => 'bg-pastel-rose/40 text-rose-800',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold mt-2 {{ $statusColors[$candidate->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($candidate->status) }}
                </span>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <span class="material-icons-round text-[18px] text-slate-400">email</span>
                    <div>
                        <p class="text-xs text-slate-500">Email</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $candidate->email }}</p>
                    </div>
                </div>
                @if($candidate->phone)
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <span class="material-icons-round text-[18px] text-slate-400">phone</span>
                    <div>
                        <p class="text-xs text-slate-500">Telepon</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $candidate->phone }}</p>
                    </div>
                </div>
                @endif
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <span class="material-icons-round text-[18px] text-slate-400">source</span>
                    <div>
                        <p class="text-xs text-slate-500">Sumber</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ ucfirst($candidate->source) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <span class="material-icons-round text-[18px] text-slate-400">calendar_today</span>
                    <div>
                        <p class="text-xs text-slate-500">Tanggal Melamar</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $candidate->applied_at->format('d F Y') }}</p>
                    </div>
                </div>
                @if($candidate->rating)
                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                    <span class="material-icons-round text-[18px] text-slate-400">star</span>
                    <div>
                        <p class="text-xs text-slate-500">Rating</p>
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                            <span class="material-icons-round text-[16px] {{ $i <= $candidate->rating ? 'text-amber-400' : 'text-slate-300' }}">star</span>
                            @endfor
                        </div>
                    </div>
                </div>
                @endif
            </div>

            @if($candidate->resume_path)
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.recruitment.candidates.show', $candidate) }}?download=resume"
                    class="inline-flex items-center gap-2 w-full justify-center px-4 py-2.5 bg-sky/20 text-sky-800 rounded-xl font-bold text-sm hover:bg-sky/30 transition-all">
                    <span class="material-icons-round text-[16px]">download</span>
                    Download Resume
                </a>
            </div>
            @endif

            {{-- Status Update --}}
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <form method="POST" action="{{ route('admin.recruitment.candidates.update-status', $candidate) }}">
                    @csrf
                    @method('PATCH')
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Ubah Status</label>
                    <div class="flex gap-2">
                        <select name="status"
                            class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
                            @foreach(['applied', 'screening', 'interview', 'assessment', 'offered', 'hired', 'rejected'] as $s)
                            <option value="{{ $s }}" {{ $candidate->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="px-3 py-2 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition-all">
                            <span class="material-icons-round text-[16px]">check</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Interview Timeline --}}
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 dark:text-white">Riwayat Interview</h3>
                <button onclick="document.getElementById('scheduleInterviewModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-slate-800 transition-all">
                    <span class="material-icons-round text-[14px]">add</span>
                    Jadwalkan
                </button>
            </div>
            <div class="p-6">
                @if($candidate->interviews->count() > 0)
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200 dark:bg-slate-700"></div>
                    <div class="space-y-6">
                        @foreach($candidate->interviews->sortByDesc('scheduled_at') as $interview)
                        <div class="relative pl-10">
                            @php
                                $interviewStatusColors = [
                                    'scheduled' => 'bg-sky text-sky-800 border-sky',
                                    'completed' => 'bg-primary text-green-800 border-primary',
                                    'cancelled' => 'bg-pastel-rose text-rose-800 border-pastel-rose',
                                    'no_show' => 'bg-peach text-orange-800 border-peach',
                                ];
                                $dotColor = [
                                    'scheduled' => 'bg-sky',
                                    'completed' => 'bg-primary',
                                    'cancelled' => 'bg-pastel-rose',
                                    'no_show' => 'bg-peach',
                                ];
                            @endphp
                            <div class="absolute left-2.5 top-1 w-3 h-3 rounded-full {{ $dotColor[$interview->status] ?? 'bg-slate-300' }} border-2 border-white dark:border-card-dark"></div>

                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-100 dark:border-slate-700">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $interviewStatusColors[$interview->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ ucfirst(str_replace('_', ' ', $interview->status)) }}
                                            </span>
                                            @php
                                                $typeIcons = ['phone' => 'call', 'video' => 'videocam', 'onsite' => 'location_on', 'technical' => 'code'];
                                            @endphp
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                                <span class="material-icons-round text-[12px]">{{ $typeIcons[$interview->type] ?? 'event' }}</span>
                                                {{ ucfirst($interview->type) }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ $interview->scheduled_at->format('d F Y, H:i') }}
                                            <span class="font-normal text-slate-500">({{ $interview->duration_minutes }} menit)</span>
                                        </p>
                                    </div>
                                    @if($interview->score)
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $interview->score }}</p>
                                        <p class="text-[10px] text-slate-500 font-bold">/10</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                                    <span class="material-icons-round text-[14px]">person</span>
                                    {{ $interview->interviewer->name }}
                                    @if($interview->location)
                                    <span class="mx-1">&bull;</span>
                                    <span class="material-icons-round text-[14px]">location_on</span>
                                    {{ $interview->location }}
                                    @endif
                                </div>
                                @if($interview->feedback)
                                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700">
                                    {{ $interview->feedback }}
                                </p>
                                @endif

                                {{-- Update Interview Form --}}
                                @if($interview->status === 'scheduled')
                                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700" x-data="{ showForm: false }">
                                    <button @click="showForm = !showForm"
                                        class="text-xs font-bold text-sky-600 hover:text-sky-800 transition-colors">
                                        Tambah Feedback
                                    </button>
                                    <form x-show="showForm" x-cloak method="POST" action="{{ route('admin.recruitment.interviews.update', $interview) }}" class="mt-3 space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="no_show">No Show</option>
                                        </select>
                                        <textarea name="feedback" rows="2" placeholder="Feedback..."
                                            class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs"></textarea>
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="score" min="1" max="10" placeholder="Skor (1-10)"
                                                class="w-24 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                                            <button type="submit"
                                                class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-slate-800 transition-all">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center py-8 text-slate-400">
                    <span class="material-icons-round text-[40px] mb-2 block">event_busy</span>
                    <p class="text-sm">Belum ada interview dijadwalkan</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Onboarding Checklist (only if hired) --}}
        @if($candidate->status === 'hired')
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 dark:text-white">Onboarding Checklist</h3>
                @if($candidate->onboardingTasks->isEmpty())
                <form method="POST" action="{{ route('admin.recruitment.onboarding.init', $candidate) }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary/50 text-green-800 rounded-lg text-xs font-bold hover:bg-primary transition-all">
                        <span class="material-icons-round text-[14px]">playlist_add</span>
                        Inisialisasi
                    </button>
                </form>
                @endif
            </div>
            <div class="p-6">
                @if($candidate->onboardingTasks->count() > 0)
                <div class="space-y-3">
                    @foreach($candidate->onboardingTasks->sortBy('pivot.id') as $task)
                    <div class="flex items-start gap-3 p-3 rounded-xl border {{ $task->pivot->status === 'completed' ? 'bg-primary/10 border-primary/30' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-100 dark:border-slate-700' }}">
                        <form method="POST" action="{{ route('admin.recruitment.onboarding.update', $task->pivot->id) }}" class="flex-shrink-0 mt-0.5">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $task->pivot->status === 'completed' ? 'pending' : 'completed' }}">
                            <button type="submit" class="w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all
                                {{ $task->pivot->status === 'completed' ? 'bg-primary border-green-600 text-green-800' : 'border-slate-300 hover:border-primary' }}">
                                @if($task->pivot->status === 'completed')
                                <span class="material-icons-round text-[14px]">check</span>
                                @endif
                            </button>
                        </form>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-900 dark:text-white {{ $task->pivot->status === 'completed' ? 'line-through opacity-60' : '' }}">
                                {{ $task->name }}
                            </p>
                            @if($task->description)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $task->description }}</p>
                            @endif
                            @if($task->pivot->completed_at)
                            <p class="text-[10px] text-green-600 mt-1 font-medium">
                                Selesai {{ \Carbon\Carbon::parse($task->pivot->completed_at)->format('d M Y, H:i') }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @php
                    $completedCount = $candidate->onboardingTasks->where('pivot.status', 'completed')->count();
                    $totalCount = $candidate->onboardingTasks->count();
                    $percentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
                @endphp
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-slate-500">Progress</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $completedCount }}/{{ $totalCount }} ({{ $percentage }}%)</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @else
                <div class="text-center py-8 text-slate-400">
                    <span class="material-icons-round text-[40px] mb-2 block">checklist</span>
                    <p class="text-sm">Klik "Inisialisasi" untuk memulai onboarding</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Cover Letter --}}
        @if($candidate->cover_letter)
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h3 class="font-bold text-slate-900 dark:text-white mb-3">Cover Letter</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line">{{ $candidate->cover_letter }}</p>
        </div>
        @endif

        {{-- Notes --}}
        @if($candidate->notes)
        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h3 class="font-bold text-slate-900 dark:text-white mb-3">Catatan</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line">{{ $candidate->notes }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Schedule Interview Modal --}}
<div id="scheduleInterviewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('scheduleInterviewModal').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 shadow-soft-lg w-full max-w-lg">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Jadwalkan Interview</h3>
            <button onclick="document.getElementById('scheduleInterviewModal').classList.add('hidden')"
                class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-icons-round text-slate-400">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.recruitment.interviews.store', $candidate) }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Interviewer *</label>
                <select name="interviewer_id" required
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
                    <option value="">Pilih interviewer</option>
                    @foreach($interviewers as $interviewer)
                    <option value="{{ $interviewer->id }}">{{ $interviewer->name }} ({{ $interviewer->getRoleNames()->first() }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal & Waktu *</label>
                <input type="datetime-local" name="scheduled_at" required
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Durasi (menit) *</label>
                    <input type="number" name="duration_minutes" value="60" min="15" max="480" required
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe *</label>
                    <select name="type" required
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary">
                        <option value="onsite">Onsite</option>
                        <option value="video">Video</option>
                        <option value="phone">Phone</option>
                        <option value="technical">Technical</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Lokasi</label>
                <input type="text" name="location"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary"
                    placeholder="Ruang meeting / Link video call">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('scheduleInterviewModal').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-bold text-slate-600 hover:text-slate-800 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                    <span class="material-icons-round text-[16px]">event</span>
                    Jadwalkan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
