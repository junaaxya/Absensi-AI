<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateOnboarding;
use App\Models\Department;
use App\Models\Interview;
use App\Models\JobPosition;
use App\Models\OnboardingTask;
use App\Models\User;
use Illuminate\Http\Request;

class AdminRecruitmentController extends Controller
{
    public function index()
    {
        $openPositions = JobPosition::where('status', 'open')->count();
        $activeCandidates = Candidate::whereNotIn('status', ['hired', 'rejected'])->count();
        $upcomingInterviews = Interview::where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->where('scheduled_at', '<=', now()->endOfWeek())
            ->with(['candidate', 'interviewer'])
            ->orderBy('scheduled_at')
            ->get();
        $hiredThisMonth = Candidate::where('status', 'hired')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        $pipelineSummary = Candidate::selectRaw('status, count(*) as total')
            ->whereNull('deleted_at')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentCandidates = Candidate::with('jobPosition')
            ->latest('applied_at')
            ->take(10)
            ->get();

        return view('admin.recruitment.index', compact(
            'openPositions',
            'activeCandidates',
            'upcomingInterviews',
            'hiredThisMonth',
            'pipelineSummary',
            'recentCandidates'
        ));
    }

    public function positions(Request $request)
    {
        $query = JobPosition::with(['department', 'creator'])
            ->withCount('candidates');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $positions = $query->latest()->paginate(12);
        $departments = Department::active()->orderBy('name')->get();

        return view('admin.recruitment.positions', compact('positions', 'departments'));
    }

    public function createPosition()
    {
        $departments = Department::active()->orderBy('name')->get();

        return view('admin.recruitment.positions-create', compact('departments'));
    }

    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'employment_type' => 'required|in:full_time,part_time,contract,internship',
            'salary_range_min' => 'nullable|numeric|min:0',
            'salary_range_max' => 'nullable|numeric|min:0|gte:salary_range_min',
            'status' => 'required|in:draft,open,closed,on_hold',
            'openings' => 'required|integer|min:1',
        ]);

        $validated['created_by'] = auth()->id();

        JobPosition::create($validated);

        return redirect()
            ->route('admin.recruitment.positions')
            ->with('success', 'Posisi berhasil ditambahkan.');
    }

    public function showPosition(JobPosition $jobPosition)
    {
        $jobPosition->load(['department', 'creator', 'candidates']);

        $pipeline = [
            'applied' => $jobPosition->candidates->where('status', 'applied'),
            'screening' => $jobPosition->candidates->where('status', 'screening'),
            'interview' => $jobPosition->candidates->where('status', 'interview'),
            'assessment' => $jobPosition->candidates->where('status', 'assessment'),
            'offered' => $jobPosition->candidates->where('status', 'offered'),
            'hired' => $jobPosition->candidates->where('status', 'hired'),
        ];

        return view('admin.recruitment.position-show', compact('jobPosition', 'pipeline'));
    }

    public function editPosition(JobPosition $jobPosition)
    {
        $departments = Department::active()->orderBy('name')->get();

        return view('admin.recruitment.positions-create', compact('jobPosition', 'departments'));
    }

    public function updatePosition(Request $request, JobPosition $jobPosition)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'employment_type' => 'required|in:full_time,part_time,contract,internship',
            'salary_range_min' => 'nullable|numeric|min:0',
            'salary_range_max' => 'nullable|numeric|min:0|gte:salary_range_min',
            'status' => 'required|in:draft,open,closed,on_hold',
            'openings' => 'required|integer|min:1',
        ]);

        $jobPosition->update($validated);

        return redirect()
            ->route('admin.recruitment.positions.show', $jobPosition)
            ->with('success', 'Posisi berhasil diperbarui.');
    }

    public function addCandidate(Request $request, JobPosition $jobPosition)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|string',
            'source' => 'required|in:website,referral,linkedin,jobstreet,other',
        ]);

        if ($request->hasFile('resume')) {
            $validated['resume_path'] = $request->file('resume')->store('resumes', 'local');
        }

        unset($validated['resume']);
        $validated['job_position_id'] = $jobPosition->id;
        $validated['applied_at'] = now();

        Candidate::create($validated);

        return redirect()
            ->route('admin.recruitment.positions.show', $jobPosition)
            ->with('success', 'Kandidat berhasil ditambahkan.');
    }

    public function showCandidate(Candidate $candidate)
    {
        $candidate->load([
            'jobPosition.department',
            'interviews.interviewer',
            'onboardingTasks',
        ]);

        $interviewers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Direktur', 'Vice President', 'Manager', 'Supervisor', 'Team Leader']);
        })->orderBy('name')->get();

        return view('admin.recruitment.candidate-show', compact('candidate', 'interviewers'));
    }

    public function updateCandidateStatus(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'status' => 'required|in:applied,screening,interview,assessment,offered,hired,rejected',
        ]);

        $candidate->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $candidate->status]);
        }

        return back()->with('success', 'Status kandidat berhasil diperbarui.');
    }

    public function scheduleInterview(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'interviewer_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'type' => 'required|in:phone,video,onsite,technical',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['candidate_id'] = $candidate->id;

        Interview::create($validated);

        return redirect()
            ->route('admin.recruitment.candidates.show', $candidate)
            ->with('success', 'Interview berhasil dijadwalkan.');
    }

    public function updateInterview(Request $request, Interview $interview)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
            'feedback' => 'nullable|string',
            'score' => 'nullable|integer|min:1|max:10',
        ]);

        $interview->update($validated);

        return redirect()
            ->route('admin.recruitment.candidates.show', $interview->candidate)
            ->with('success', 'Interview berhasil diperbarui.');
    }

    public function initOnboarding(Candidate $candidate)
    {
        if ($candidate->status !== 'hired') {
            return back()->with('error', 'Onboarding hanya dapat dimulai untuk kandidat yang sudah diterima.');
        }

        $tasks = OnboardingTask::where('is_template', true)
            ->orderBy('order')
            ->get();

        if ($tasks->isEmpty()) {
            return back()->with('error', 'Belum ada template onboarding task. Silakan buat terlebih dahulu.');
        }

        foreach ($tasks as $task) {
            CandidateOnboarding::firstOrCreate([
                'candidate_id' => $candidate->id,
                'onboarding_task_id' => $task->id,
            ], [
                'status' => 'pending',
            ]);
        }

        return redirect()
            ->route('admin.recruitment.candidates.show', $candidate)
            ->with('success', 'Onboarding tasks berhasil diinisialisasi.');
    }

    public function updateOnboardingTask(Request $request, CandidateOnboarding $candidateOnboarding)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        } else {
            $validated['completed_at'] = null;
        }

        $candidateOnboarding->update($validated);

        return redirect()
            ->route('admin.recruitment.candidates.show', $candidateOnboarding->candidate)
            ->with('success', 'Onboarding task berhasil diperbarui.');
    }
}
