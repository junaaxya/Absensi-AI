<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class MyTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where('assigned_to', auth()->id())
            ->with('project');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        $tasksByProject = $tasks->getCollection()->groupBy(function ($task) {
            return $task->project->name ?? 'Tanpa Proyek';
        });

        return view('my-tasks.index', compact('tasks', 'tasksByProject'));
    }

    public function show(Task $task)
    {
        if ($task->assigned_to !== auth()->id()) {
            abort(403);
        }

        $task->load(['project', 'comments.user', 'timeEntries']);

        $totalMinutes = $task->timeEntries
            ->where('user_id', auth()->id())
            ->sum('duration_minutes');

        $activeTimer = $task->timeEntries
            ->where('user_id', auth()->id())
            ->whereNull('ended_at')
            ->first();

        return view('my-tasks.show', compact('task', 'totalMinutes', 'activeTimer'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        if ($task->assigned_to !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
        ]);

        $task->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Status task berhasil diperbarui.');
    }

    public function addComment(Request $request, Task $task)
    {
        if ($task->assigned_to !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        $task->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return redirect()->route('my-tasks.show', $task)
            ->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function startTimer(Task $task)
    {
        if ($task->assigned_to !== auth()->id()) {
            abort(403);
        }

        $activeTimer = TimeEntry::where('task_id', $task->id)
            ->where('user_id', auth()->id())
            ->whereNull('ended_at')
            ->first();

        if ($activeTimer) {
            return redirect()->back()
                ->with('error', 'Timer sudah berjalan untuk task ini.');
        }

        TimeEntry::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'started_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Timer dimulai.');
    }

    public function stopTimer(Task $task)
    {
        if ($task->assigned_to !== auth()->id()) {
            abort(403);
        }

        $activeTimer = TimeEntry::where('task_id', $task->id)
            ->where('user_id', auth()->id())
            ->whereNull('ended_at')
            ->first();

        if (!$activeTimer) {
            return redirect()->back()
                ->with('error', 'Tidak ada timer yang aktif.');
        }

        $activeTimer->update([
            'ended_at' => now(),
            'duration_minutes' => (int) now()->diffInMinutes($activeTimer->started_at),
        ]);

        return redirect()->back()
            ->with('success', 'Timer dihentikan.');
    }
}
