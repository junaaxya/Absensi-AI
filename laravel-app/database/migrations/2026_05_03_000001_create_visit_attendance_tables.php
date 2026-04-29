<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->date('tanggal');
            $table->string('client_name');
            $table->string('location_name');
            $table->text('purpose');
            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable();
            $table->decimal('check_in_lat', 10, 7);
            $table->decimal('check_in_long', 10, 7);
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_long', 10, 7)->nullable();
            $table->string('check_in_photo')->nullable();
            $table->string('check_out_photo')->nullable();
            $table->float('similarity_score_in')->nullable();
            $table->float('similarity_score_out')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->string('device_fingerprint')->nullable();
            $table->integer('anomaly_score')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'tanggal']);
        });

        Schema::create('visit_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_attendance_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->float('accuracy');
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index('visit_attendance_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_locations');
        Schema::dropIfExists('visit_attendances');
    }
};
