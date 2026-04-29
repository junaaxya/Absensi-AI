<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class AdminTaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:backlog,todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $validated['created_by'] = auth()->id();

        $maxOrder = Task::where('project_id', $validated['project_id'])
            ->where('status', $validated['status'])
            ->max('order');

        $validated['order'] = ($maxOrder ?? 0) + 1;

        Task::create($validated);

        return redirect()->back()->with('success', 'Task berhasil dibuat.');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:backlog,todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'Task berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:backlog,todo,in_progress,review,done',
            'order' => 'required|integer|min:0',
        ]);

        $task->update($validated);

        return response()->json(['success' => true, 'task' => $task->fresh()]);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->back()->with('success', 'Task berhasil dihapus.');
    }

    public function addComment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $task->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function startTimer(Task $task)
    {
        $activeTimer = TimeEntry::where('user_id', auth()->id())
            ->whereNull('ended_at')
            ->first();

        if ($activeTimer) {
            return redirect()->back()->with('info', 'Anda masih memiliki timer aktif. Hentikan terlebih dahulu.');
        }

        TimeEntry::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'started_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Timer dimulai.');
    }

    public function stopTimer(TimeEntry $timeEntry)
    {
        if ($timeEntry->user_id !== auth()->id()) {
            abort(403);
        }

        $endedAt = now();
        $durationMinutes = (int) $timeEntry->started_at->diffInMinutes($endedAt);

        $timeEntry->update([
            'ended_at' => $endedAt,
            'duration_minutes' => $durationMinutes,
        ]);

        return redirect()->back()->with('success', 'Timer dihentikan. Durasi: ' . $durationMinutes . ' menit.');
    }
}
