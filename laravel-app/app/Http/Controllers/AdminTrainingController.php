<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminTrainingController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::withCount(['enrollments', 'materials'])
            ->withCount(['enrollments as completed_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->with('instructor');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $courses = $query->latest()->paginate(12);

        return view('admin.training.index', compact('courses'));
    }

    public function create()
    {
        $instructors = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.training.create', compact('instructors', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:users,id',
            'thumbnail' => 'nullable|image|max:2048',
            'duration_hours' => 'nullable|numeric|min:0|max:9999',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_mandatory' => 'boolean',
            'target_roles' => 'nullable|array',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['is_mandatory'] = $request->boolean('is_mandatory');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course = Course::create($validated);

        return redirect()->route('admin.training.show', $course)
            ->with('success', 'Kursus berhasil dibuat.');
    }

    public function show(Course $course)
    {
        $course->load(['materials', 'instructor']);

        $enrollments = CourseEnrollment::where('course_id', $course->id)
            ->with(['user', 'user.department'])
            ->latest('enrolled_at')
            ->get();

        return view('admin.training.show', compact('course', 'enrollments'));
    }

    public function edit(Course $course)
    {
        $instructors = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.training.edit', compact('course', 'instructors', 'roles'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|exists:users,id',
            'thumbnail' => 'nullable|image|max:2048',
            'duration_hours' => 'nullable|numeric|min:0|max:9999',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_mandatory' => 'boolean',
            'target_roles' => 'nullable|array',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $validated['is_mandatory'] = $request->boolean('is_mandatory');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course->update($validated);

        return redirect()->route('admin.training.show', $course)
            ->with('success', 'Kursus berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.training.index')
            ->with('success', 'Kursus berhasil dihapus.');
    }

    public function publish(Course $course)
    {
        $course->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Kursus berhasil dipublikasikan.');
    }

    public function unpublish(Course $course)
    {
        $course->update([
            'is_published' => false,
        ]);

        return back()->with('success', 'Kursus berhasil di-unpublish.');
    }

    public function storeMaterial(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:document,video,link,quiz',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        $maxOrder = $course->materials()->max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('courses/materials', 'public');
        }

        unset($validated['file']);

        $course->materials()->create($validated);

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    public function updateMaterial(Request $request, CourseMaterial $material)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:document,video,link,quiz',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'duration_minutes' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('courses/materials', 'public');
        }

        unset($validated['file']);

        $material->update($validated);

        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroyMaterial(CourseMaterial $material)
    {
        $material->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }

    public function enrollEmployee(Request $request, Course $course)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $enrolled = 0;
        foreach ($validated['user_ids'] as $userId) {
            $exists = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $userId)
                ->exists();

            if (!$exists) {
                CourseEnrollment::create([
                    'course_id' => $course->id,
                    'user_id' => $userId,
                    'enrolled_at' => now(),
                    'status' => 'enrolled',
                ]);
                $enrolled++;
            }
        }

        return back()->with('success', "{$enrolled} karyawan berhasil didaftarkan.");
    }

    public function report(Request $request)
    {
        $departments = Department::withCount('users')->orderBy('name')->get();

        $courses = Course::withCount([
            'enrollments',
            'enrollments as completed_count' => function ($q) {
                $q->where('status', 'completed');
            },
            'enrollments as in_progress_count' => function ($q) {
                $q->where('status', 'in_progress');
            },
        ])->where('is_published', true)->get();

        $mandatoryCourses = Course::where('is_mandatory', true)
            ->where('is_published', true)
            ->withCount([
                'enrollments',
                'enrollments as completed_count' => function ($q) {
                    $q->where('status', 'completed');
                },
            ])
            ->get();

        $departmentStats = [];
        foreach ($departments as $department) {
            $userIds = User::where('department_id', $department->id)->pluck('id');
            $totalEnrollments = CourseEnrollment::whereIn('user_id', $userIds)->count();
            $completedEnrollments = CourseEnrollment::whereIn('user_id', $userIds)
                ->where('status', 'completed')
                ->count();

            $departmentStats[] = [
                'name' => $department->name,
                'total_employees' => $department->users_count,
                'total_enrollments' => $totalEnrollments,
                'completed' => $completedEnrollments,
                'completion_rate' => $totalEnrollments > 0
                    ? round(($completedEnrollments / $totalEnrollments) * 100, 1)
                    : 0,
            ];
        }

        return view('admin.training.report', compact('courses', 'mandatoryCourses', 'departmentStats', 'departments'));
    }
}
