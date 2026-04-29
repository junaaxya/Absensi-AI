<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->time('work_start_time')->default('08:00:00')->after('office_radius');
            $table->time('work_end_time')->default('17:00:00')->after('work_start_time');
            $table->time('overtime_start_time')->default('17:30:00')->after('work_end_time');
            $table->time('overtime_end_time')->default('21:00:00')->after('overtime_start_time');
            $table->integer('late_tolerance_minutes')->default(15)->after('overtime_end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'work_start_time',
                'work_end_time',
                'overtime_start_time',
                'overtime_end_time',
                'late_tolerance_minutes'
            ]);
        });
    }
};
