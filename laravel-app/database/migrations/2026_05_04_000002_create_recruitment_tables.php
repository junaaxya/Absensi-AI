<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship'])->default('full_time');
            $table->decimal('salary_range_min', 15, 2)->nullable();
            $table->decimal('salary_range_max', 15, 2)->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'on_hold'])->default('draft');
            $table->integer('openings')->default(1);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_position_id')->constrained();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('resume_path')->nullable();
            $table->text('cover_letter')->nullable();
            $table->enum('source', ['website', 'referral', 'linkedin', 'jobstreet', 'other'])->default('other');
            $table->enum('status', ['applied', 'screening', 'interview', 'assessment', 'offered', 'hired', 'rejected'])->default('applied');
            $table->tinyInteger('rating')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interviewer_id')->constrained('users');
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('type', ['phone', 'video', 'onsite', 'technical'])->default('onsite');
            $table->string('location')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->text('feedback')->nullable();
            $table->tinyInteger('score')->nullable();
            $table->timestamps();
        });

        Schema::create('onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_template')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('candidate_onboarding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained();
            $table->foreignId('onboarding_task_id')->constrained();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_onboarding');
        Schema::dropIfExists('onboarding_tasks');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('job_positions');
    }
};
