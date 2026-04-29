<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            $table->foreignId('leave_type_id')->nullable()->constrained('leave_types')->nullOnDelete();
            $table->enum('approval_status', [
                'pending',
                'approved_l1',
                'approved_l2',
                'approved_final',
                'rejected',
            ])->default('pending');
            $table->foreignId('current_approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('approved_by')->nullable();
            $table->string('wfa_location')->nullable();
            $table->json('wfa_daily_checkins')->nullable();
        });

        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->integer('year');
            $table->integer('quota');
            $table->integer('used')->default(0);
            $table->integer('remaining');
            $table->integer('carry_over')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'leave_type_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');

        Schema::table('izins', function (Blueprint $table) {
            $table->dropForeign(['leave_type_id']);
            $table->dropForeign(['current_approver_id']);
            $table->dropColumn([
                'leave_type_id',
                'approval_status',
                'current_approver_id',
                'approved_by',
                'wfa_location',
                'wfa_daily_checkins',
            ]);
        });
    }
};
