<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class AdminProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['manager', 'department', 'members', 'tasks']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $projects = $query->latest()->paginate(15)->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();

        return view('admin.projects.create', compact('users', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'manager_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'color' => 'nullable|string|max:7',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project = Project::create($validated);

        $project->members()->attach(auth()->id(), ['role' => 'manager']);

        if ($request->filled('members')) {
            foreach ($request->members as $memberId) {
                if ((int) $memberId !== auth()->id()) {
                    $project->members()->attach($memberId, ['role' => 'member']);
                }
            }
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project berhasil dibuat.');
    }

    public function show(Project $project)
    {
        $project->load([
            'manager',
            'department',
            'members',
            'tasks' => function ($query) {
                $query->with(['assignee', 'creator', 'subtasks', 'comments.user', 'timeEntries.user'])
                    ->orderBy('order');
            },
        ]);

        $tasksByStatus = [
            'backlog' => $project->tasks->where('status', 'backlog')->where('parent_id', null)->values(),
            'todo' => $project->tasks->where('status', 'todo')->where('parent_id', null)->values(),
            'in_progress' => $project->tasks->where('status', 'in_progress')->where('parent_id', null)->values(),
            'review' => $project->tasks->where('status', 'review')->where('parent_id', null)->values(),
            'done' => $project->tasks->where('status', 'done')->where('parent_id', null)->values(),
        ];

        $totalTasks = $project->tasks->where('parent_id', null)->count();
        $completedTasks = $project->tasks->where('parent_id', null)->where('status', 'done')->count();
        $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $projectMembers = $project->members;

        return view('admin.projects.show', compact(
            'project',
            'tasksByStatus',
            'totalTasks',
            'completedTasks',
            'progressPercent',
            'projectMembers',
        ));
    }

    public function edit(Project $project)
    {
        $project->load('members');
        $users = User::orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();

        return view('admin.projects.edit', compact('project', 'users', 'departments'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'manager_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'color' => 'nullable|string|max:7',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project->update($validated);

        if ($request->has('members')) {
            $syncData = [];
            foreach ($request->members as $memberId) {
                $syncData[$memberId] = ['role' => 'member'];
            }
            if (auth()->id()) {
                $syncData[auth()->id()] = ['role' => 'manager'];
            }
            $project->members()->sync($syncData);
        }

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }
}
