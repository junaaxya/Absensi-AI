<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('category')->nullable();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('thumbnail')->nullable();
            $table->decimal('duration_hours', 5, 1)->nullable();
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced']);
            $table->boolean('is_mandatory')->default(false);
            $table->json('target_roles')->nullable();
            $table->boolean('is_published')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->integer('max_participants')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['document', 'video', 'link', 'quiz']);
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('duration_minutes')->nullable();
            $table->timestamps();
        });

        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('enrolled_at');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'dropped']);
            $table->decimal('score', 5, 2)->nullable();
            $table->string('certificate_number')->nullable()->unique();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });

        Schema::create('course_material_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_enrollment_id')->constrained('course_enrollments')->cascadeOnDelete();
            $table->foreignId('course_material_id')->constrained('course_materials')->cascadeOnDelete();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique(['course_enrollment_id', 'course_material_id'], 'enrollment_material_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_material_progress');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('courses');
    }
};
