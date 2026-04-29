<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->integer('max_devices_per_user')->default(2)->after('face_require_liveness');
            $table->integer('anomaly_score_warning_threshold')->default(30)->after('max_devices_per_user');
            $table->integer('anomaly_score_reject_threshold')->default(60)->after('anomaly_score_warning_threshold');
            $table->boolean('enable_anti_cheat')->default(true)->after('anomaly_score_reject_threshold');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'max_devices_per_user',
                'anomaly_score_warning_threshold',
                'anomaly_score_reject_threshold',
                'enable_anti_cheat',
            ]);
        });
    }
};
