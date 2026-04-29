<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('violation_deduction_type')->default('per_point');
            $table->decimal('violation_deduction_per_point', 15, 2)->default(25000);
            $table->decimal('violation_deduction_percentage', 5, 2)->default(0);
            $table->integer('sp1_threshold')->default(10);
            $table->integer('sp2_threshold')->default(20);
            $table->integer('sp3_threshold')->default(30);
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'violation_deduction_type',
                'violation_deduction_per_point',
                'violation_deduction_percentage',
                'sp1_threshold',
                'sp2_threshold',
                'sp3_threshold',
            ]);
        });
    }
};
