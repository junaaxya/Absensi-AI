<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['earning', 'deduction', 'benefit']);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_fixed')->default(false);
            $table->decimal('default_amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_salary_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('salary_component_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('effective_date');
            $table->timestamps();

            $table->unique(['user_id', 'salary_component_id']);
        });

        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period_month')->unique(); // YYYY-MM
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'processing', 'calculated', 'approved', 'paid', 'locked'])->default('draft');
            $table->integer('total_employees')->default(0);
            $table->decimal('total_gross', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('total_net', 15, 2)->default(0);
            $table->timestamp('calculated_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('working_days');
            $table->integer('present_days');
            $table->integer('late_count');
            $table->integer('late_minutes_total');
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('total_earnings', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('total_bpjs_company', 15, 2);
            $table->decimal('total_bpjs_employee', 15, 2);
            $table->decimal('pph21_amount', 15, 2);
            $table->decimal('net_salary', 15, 2);
            $table->decimal('prorate_factor', 5, 4)->default(1.0000);
            $table->string('prorate_reason')->nullable();
            $table->enum('status', ['calculated', 'approved', 'paid'])->default('calculated');
            $table->timestamps();

            $table->unique(['payroll_period_id', 'user_id']);
        });

        Schema::create('payroll_detail_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_detail_id')->constrained()->cascadeOnDelete();
            $table->string('component_name');
            $table->enum('component_type', ['earning', 'deduction', 'bpjs_company', 'bpjs_employee', 'tax']);
            $table->decimal('amount', 15, 2);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('tax_ter_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['A', 'B', 'C']);
            $table->decimal('min_income', 15, 2);
            $table->decimal('max_income', 15, 2);
            $table->decimal('rate', 8, 6); // e.g. 0.002500 for 0.25%
            $table->timestamps();
        });

        Schema::create('tax_ptkp_rates', function (Blueprint $table) {
            $table->id();
            $table->string('status')->unique(); // TK/0, K/1, etc.
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });

        // Add late_minutes_total to attendances table (needed for payroll calculations)
        if (!Schema::hasColumn('attendances', 'late_minutes_total')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->integer('late_minutes_total')->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendances', 'late_minutes_total')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('late_minutes_total');
            });
        }

        Schema::dropIfExists('payroll_detail_items');
        Schema::dropIfExists('payroll_details');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('employee_salary_components');
        Schema::dropIfExists('salary_components');
        Schema::dropIfExists('tax_ter_rates');
        Schema::dropIfExists('tax_ptkp_rates');
    }
};
