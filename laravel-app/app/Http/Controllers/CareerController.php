<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\JobPosition;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function index()
    {
        $positions = JobPosition::where('status', 'open')
            ->with('department')
            ->withCount('candidates')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('career.index', compact('positions'));
    }

    public function show(JobPosition $jobPosition)
    {
        if ($jobPosition->status !== 'open') {
            abort(404);
        }

        $jobPosition->load('department');

        return view('career.show', compact('jobPosition'));
    }

    public function apply(JobPosition $jobPosition)
    {
        if ($jobPosition->status !== 'open') {
            abort(404);
        }

        return view('career.apply', compact('jobPosition'));
    }

    public function store(Request $request, JobPosition $jobPosition)
    {
        if ($jobPosition->status !== 'open') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|string|max:2000',
            'source' => 'required|in:website,referral,linkedin,jobstreet,other',
            'honeypot' => 'size:0',
        ]);

        $existingApplication = Candidate::where('job_position_id', $jobPosition->id)
            ->where('email', $validated['email'])
            ->exists();

        if ($existingApplication) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Anda sudah pernah melamar untuk posisi ini.']);
        }

        $resumePath = $request->file('resume')->store('resumes', 'local');

        Candidate::create([
            'job_position_id' => $jobPosition->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'resume_path' => $resumePath,
            'cover_letter' => $validated['cover_letter'],
            'source' => $validated['source'],
            'status' => 'applied',
            'applied_at' => now(),
        ]);

        return redirect()->route('career.thank-you', $jobPosition);
    }

    public function thankYou(JobPosition $jobPosition)
    {
        return view('career.thank-you', compact('jobPosition'));
    }
}
