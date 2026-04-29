<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_fingerprint')->unique();
            $table->string('device_name')->nullable();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->boolean('is_trusted')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('device_fingerprint')->nullable()->after('device_info');
            $table->json('gps_readings')->nullable()->after('device_fingerprint');
            $table->string('ip_address', 45)->nullable()->after('gps_readings');
            $table->string('timezone_client')->nullable()->after('ip_address');
            $table->integer('anomaly_score')->default(0)->after('timezone_client');
            $table->json('anomaly_flags')->nullable()->after('anomaly_score');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'device_fingerprint',
                'gps_readings',
                'ip_address',
                'timezone_client',
                'anomaly_score',
                'anomaly_flags',
            ]);
        });

        Schema::dropIfExists('employee_devices');
    }
};
