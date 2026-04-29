<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            // Company Profile
            $table->string('company_name', 255)->nullable()->after('office_name');
            $table->text('company_address')->nullable();
            $table->string('company_phone', 30)->nullable();
            $table->string('company_email', 255)->nullable();
            $table->string('company_logo', 255)->nullable();
            $table->string('company_website', 255)->nullable();
            $table->string('company_npwp', 30)->nullable();

            // Attendance Policy
            $table->time('auto_checkout_time')->nullable();
            $table->integer('minimum_work_hours')->default(8);
            $table->integer('half_day_threshold_hours')->default(4);
            $table->boolean('require_checkout')->default(true);
            $table->boolean('allow_multiple_checkin')->default(false);
            $table->json('weekend_days')->nullable();

            // Face Recognition
            $table->float('face_similarity_threshold')->default(0.5);
            $table->integer('face_max_registration_photos')->default(6);
            $table->boolean('face_anti_spoofing_enabled')->default(false);
            $table->integer('face_min_photo_quality')->default(80);
            $table->boolean('face_require_liveness')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_address',
                'company_phone',
                'company_email',
                'company_logo',
                'company_website',
                'company_npwp',
                'auto_checkout_time',
                'minimum_work_hours',
                'half_day_threshold_hours',
                'require_checkout',
                'allow_multiple_checkin',
                'weekend_days',
                'face_similarity_threshold',
                'face_max_registration_photos',
                'face_anti_spoofing_enabled',
                'face_min_photo_quality',
                'face_require_liveness',
            ]);
        });
    }
};
