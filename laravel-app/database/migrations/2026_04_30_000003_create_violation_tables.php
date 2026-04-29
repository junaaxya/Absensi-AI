<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('points');
            $table->text('description')->nullable();
            $table->boolean('is_auto')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('violation_type_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('points');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'tanggal']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('warning_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['SP1', 'SP2', 'SP3']);
            $table->string('period_month'); // YYYY-MM
            $table->integer('total_points');
            $table->date('issued_at');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'type', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warning_letters');
        Schema::dropIfExists('violations');
        Schema::dropIfExists('violation_types');
    }
};
