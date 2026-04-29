<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\CourseMaterialProgress;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('is_published', true)
            ->withCount(['materials', 'enrollments'])
            ->with('instructor');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->latest('published_at')->paginate(12);

        $enrolledCourseIds = CourseEnrollment::where('user_id', auth()->id())
            ->pluck('course_id')
            ->toArray();

        $categories = Course::where('is_published', true)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('training.index', compact('courses', 'enrolledCourseIds', 'categories'));
    }

    public function show(Course $course)
    {
        if (!$course->is_published) {
            abort(404);
        }

        $course->load(['materials', 'instructor', 'enrollments']);

        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->first();

        $enrolledCount = $course->enrollments->count();

        return view('training.show', compact('course', 'enrollment', 'enrolledCount'));
    }

    public function enroll(Course $course)
    {
        if (!$course->is_published) {
            abort(404);
        }

        $existing = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()->route('training.learn', $course)
                ->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        if ($course->max_participants) {
            $enrolledCount = CourseEnrollment::where('course_id', $course->id)->count();
            if ($enrolledCount >= $course->max_participants) {
                return back()->with('error', 'Kursus sudah penuh.');
            }
        }

        CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => auth()->id(),
            'enrolled_at' => now(),
            'status' => 'enrolled',
        ]);

        return redirect()->route('training.learn', $course)
            ->with('success', 'Berhasil mendaftar kursus!');
    }

    public function learn(Course $course)
    {
        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $course->load('materials');

        $completedMaterialIds = CourseMaterialProgress::where('course_enrollment_id', $enrollment->id)
            ->where('is_completed', true)
            ->pluck('course_material_id')
            ->toArray();

        $currentMaterial = $course->materials->first(function ($material) use ($completedMaterialIds) {
            return !in_array($material->id, $completedMaterialIds);
        }) ?? $course->materials->last();

        return view('training.learn', compact('course', 'enrollment', 'completedMaterialIds', 'currentMaterial'));
    }

    public function markMaterialComplete(CourseMaterial $material)
    {
        $enrollment = CourseEnrollment::where('course_id', $material->course_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($enrollment->status === 'enrolled') {
            $enrollment->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        CourseMaterialProgress::updateOrCreate(
            [
                'course_enrollment_id' => $enrollment->id,
                'course_material_id' => $material->id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
                'started_at' => now(),
            ]
        );

        $enrollment->updateProgress();

        return response()->json([
            'success' => true,
            'progress' => $enrollment->fresh()->progress_percentage,
            'status' => $enrollment->fresh()->status,
            'certificate_number' => $enrollment->fresh()->certificate_number,
        ]);
    }

    public function myCourses()
    {
        $enrollments = CourseEnrollment::where('user_id', auth()->id())
            ->with(['course' => function ($q) {
                $q->withCount('materials');
            }, 'course.instructor'])
            ->latest('enrolled_at')
            ->get();

        return view('training.my-courses', compact('enrollments'));
    }

    public function certificate(CourseEnrollment $enrollment)
    {
        if ($enrollment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($enrollment->status !== 'completed' || !$enrollment->certificate_number) {
            abort(404, 'Sertifikat belum tersedia.');
        }

        $enrollment->load(['course', 'user']);

        return view('training.certificate', compact('enrollment'));
    }
}
