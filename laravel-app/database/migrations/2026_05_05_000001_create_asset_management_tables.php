<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'none'])->default('straight_line');
            $table->integer('useful_life_years')->nullable();
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_category_id')->constrained('asset_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('asset_code')->unique();
            $table->text('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('current_value', 15, 2);
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang', 'dihapuskan'])->default('baik');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('assigned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users');
            $table->dateTime('assigned_at');
            $table->dateTime('returned_at')->nullable();
            $table->string('condition_on_assign');
            $table->string('condition_on_return')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->enum('maintenance_type', ['preventive', 'corrective', 'upgrade'])->default('corrective');
            $table->text('description');
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('performed_by')->nullable();
            $table->date('performed_at');
            $table->date('next_maintenance_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
};
