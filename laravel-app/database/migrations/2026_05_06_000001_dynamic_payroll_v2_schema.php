<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ALTER salary_components — ADD 13 columns + parent_id
        Schema::table('salary_components', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_components', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            }
            if (!Schema::hasColumn('salary_components', 'category')) {
                $table->enum('category', ['basic', 'earning', 'deduction', 'benefit', 'bpjs_employee', 'bpjs_company', 'tax'])
                    ->default('earning')->after('type');
            }
            if (!Schema::hasColumn('salary_components', 'value_type')) {
                $table->enum('value_type', ['flat', 'formula'])->default('flat')->after('default_amount');
            }
            if (!Schema::hasColumn('salary_components', 'formula')) {
                $table->text('formula')->nullable()->after('value_type');
            }
            if (!Schema::hasColumn('salary_components', 'is_prorated')) {
                $table->boolean('is_prorated')->default(false)->after('formula');
            }
            if (!Schema::hasColumn('salary_components', 'prorate_basis')) {
                $table->enum('prorate_basis', ['working_days', 'calendar_days'])->default('working_days')->after('is_prorated');
            }
            if (!Schema::hasColumn('salary_components', 'execution_order')) {
                $table->smallInteger('execution_order')->unsigned()->default(100)->after('prorate_basis');
            }
            if (!Schema::hasColumn('salary_components', 'depends_on')) {
                $table->json('depends_on')->nullable()->after('execution_order');
            }
            if (!Schema::hasColumn('salary_components', 'min_value')) {
                $table->decimal('min_value', 15, 2)->nullable()->after('depends_on');
            }
            if (!Schema::hasColumn('salary_components', 'max_value')) {
                $table->decimal('max_value', 15, 2)->nullable()->after('min_value');
            }
            if (!Schema::hasColumn('salary_components', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('max_value');
            }
            if (!Schema::hasColumn('salary_components', 'effective_until')) {
                $table->date('effective_until')->nullable()->after('effective_from');
            }
            if (!Schema::hasColumn('salary_components', 'version')) {
                $table->unsignedInteger('version')->default(1)->after('effective_until');
            }
            if (!Schema::hasColumn('salary_components', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('version');
                $table->foreign('parent_id')->references('id')->on('salary_components')->nullOnDelete();
            }
        });

        // Add indexes for salary_components
        Schema::table('salary_components', function (Blueprint $table) {
            $table->index(['company_id', 'is_active'], 'idx_company_active');
            $table->index(['company_id', 'execution_order'], 'idx_execution_order');
            $table->index(['effective_from', 'effective_until'], 'idx_effective_range');
        });

        // ALTER employee_salary_components — ADD 4 columns
        Schema::table('employee_salary_components', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_salary_components', 'value_type')) {
                $table->enum('value_type', ['flat', 'formula'])->default('flat')->after('amount');
            }
            if (!Schema::hasColumn('employee_salary_components', 'formula')) {
                $table->text('formula')->nullable()->after('value_type');
            }
            if (!Schema::hasColumn('employee_salary_components', 'effective_until')) {
                $table->date('effective_until')->nullable()->after('effective_date');
            }
            if (!Schema::hasColumn('employee_salary_components', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('effective_until');
            }
        });

        // Add index for employee_salary_components
        Schema::table('employee_salary_components', function (Blueprint $table) {
            $table->index(['user_id', 'effective_date', 'effective_until'], 'idx_employee_effective');
        });

        // ALTER payroll_detail_items — ADD 10 snapshot columns
        Schema::table('payroll_detail_items', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_detail_items', 'salary_component_id')) {
                $table->unsignedBigInteger('salary_component_id')->nullable()->after('payroll_detail_id');
            }
            if (!Schema::hasColumn('payroll_detail_items', 'component_code')) {
                $table->string('component_code', 50)->nullable()->after('salary_component_id');
            }
            if (!Schema::hasColumn('payroll_detail_items', 'component_category')) {
                $table->string('component_category', 30)->nullable()->after('component_code');
            }
            if (!Schema::hasColumn('payroll_detail_items', 'value_type')) {
                $table->string('value_type', 10)->nullable()->after('component_category');
            }
            if (!Schema::hasColumn('payroll_detail_items', 'formula_used')) {
                $table->text('formula_used')->nullable()->after('value_type');
            }
            if (!Schema::hasColumn('payroll_detail_items', 'formula_variables')) {
                $table->json('formula_variables')->nullable()->after('formula_used');
            }
            if (!Schema::hasColumn('payroll_detail_items', 'execution_order')) {
                $table->smallInteger('execution_order')->unsigned()->nullable()->after('formula_variables');
            }
            if (!Schema::hasColumn('payroll_detail_items', 'is_prorated')) {
                $table->boolean('is_prorated')->nullable()->after('execution_order');
            }
            if (!Schema::hasColumn('payroll_detail_items', 'prorate_factor')) {
                $table->decimal('prorate_factor', 8, 6)->nullable()->after('is_prorated');
            }
            if (!Schema::hasColumn('payroll_detail_items', 'pre_prorate_amount')) {
                $table->decimal('pre_prorate_amount', 15, 2)->nullable()->after('prorate_factor');
            }
        });

        // Add index for payroll_detail_items
        Schema::table('payroll_detail_items', function (Blueprint $table) {
            $table->index(['salary_component_id'], 'idx_pdi_component');
        });

        // CREATE salary_component_rules
        if (!Schema::hasTable('salary_component_rules')) {
            Schema::create('salary_component_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->foreignId('salary_component_id')->constrained('salary_components')->cascadeOnDelete();
                $table->text('override_formula')->nullable();
                $table->decimal('override_amount', 15, 2)->nullable();
                $table->decimal('min_value', 15, 2)->nullable();
                $table->decimal('max_value', 15, 2)->nullable();
                $table->smallInteger('execution_order')->nullable();
                $table->boolean('is_active')->default(true);
                $table->date('effective_from')->nullable();
                $table->date('effective_until')->nullable();
                $table->timestamps();

                $table->unique(['company_id', 'salary_component_id']);
            });
        }

        // CREATE bpjs_rate_versions
        if (!Schema::hasTable('bpjs_rate_versions')) {
            Schema::create('bpjs_rate_versions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
                $table->enum('program', ['jht', 'jkk', 'jkm', 'jp', 'bpjs_kesehatan']);
                $table->decimal('employer_rate', 8, 5);
                $table->decimal('employee_rate', 8, 5);
                $table->decimal('max_salary_basis', 15, 2)->nullable();
                $table->decimal('min_salary_basis', 15, 2)->nullable();
                $table->date('effective_from');
                $table->date('effective_until')->nullable();
                $table->string('notes', 255)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['program', 'effective_from', 'effective_until'], 'idx_program_effective');
            });
        }

        // CREATE tax_ter_rate_versions
        if (!Schema::hasTable('tax_ter_rate_versions')) {
            Schema::create('tax_ter_rate_versions', function (Blueprint $table) {
                $table->id();
                $table->string('regulation_code', 50);
                $table->enum('category', ['A', 'B', 'C']);
                $table->decimal('min_income', 15, 2);
                $table->decimal('max_income', 15, 2);
                $table->decimal('rate', 8, 5);
                $table->date('effective_from');
                $table->date('effective_until')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['regulation_code', 'effective_from'], 'idx_regulation');
                $table->index(['category', 'effective_from', 'min_income', 'max_income'], 'idx_lookup');
            });
        }

        // CREATE tax_ptkp_rate_versions
        if (!Schema::hasTable('tax_ptkp_rate_versions')) {
            Schema::create('tax_ptkp_rate_versions', function (Blueprint $table) {
                $table->id();
                $table->string('status', 10);
                $table->decimal('amount', 15, 2);
                $table->date('effective_from');
                $table->date('effective_until')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['status', 'effective_from'], 'idx_status_effective');
            });
        }

        // CREATE payroll_snapshots
        if (!Schema::hasTable('payroll_snapshots')) {
            Schema::create('payroll_snapshots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
                $table->enum('snapshot_type', ['bpjs_rates', 'ter_rates', 'ptkp_rates', 'salary_components', 'company_config']);
                $table->json('snapshot_data');
                $table->char('snapshot_hash', 64);
                $table->timestamp('created_at')->nullable();

                $table->unique(['payroll_period_id', 'snapshot_type']);
            });
        }

        // CREATE formula_audit_log
        if (!Schema::hasTable('formula_audit_log')) {
            Schema::create('formula_audit_log', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->enum('action', ['create', 'update', 'delete', 'evaluate_error']);
                $table->string('entity_type', 50);
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->text('old_formula')->nullable();
                $table->text('new_formula')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['entity_type', 'entity_id'], 'idx_entity');
                $table->index(['user_id', 'created_at'], 'idx_user');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_audit_log');
        Schema::dropIfExists('payroll_snapshots');
        Schema::dropIfExists('tax_ptkp_rate_versions');
        Schema::dropIfExists('tax_ter_rate_versions');
        Schema::dropIfExists('bpjs_rate_versions');
        Schema::dropIfExists('salary_component_rules');

        // Remove indexes and columns from payroll_detail_items
        Schema::table('payroll_detail_items', function (Blueprint $table) {
            $table->dropIndex('idx_pdi_component');
        });
        Schema::table('payroll_detail_items', function (Blueprint $table) {
            $table->dropColumn([
                'salary_component_id', 'component_code', 'component_category',
                'value_type', 'formula_used', 'formula_variables',
                'execution_order', 'is_prorated', 'prorate_factor', 'pre_prorate_amount',
            ]);
        });

        // Remove indexes and columns from employee_salary_components
        Schema::table('employee_salary_components', function (Blueprint $table) {
            $table->dropIndex('idx_employee_effective');
        });
        Schema::table('employee_salary_components', function (Blueprint $table) {
            $table->dropColumn(['value_type', 'formula', 'effective_until', 'is_active']);
        });

        // Remove indexes and columns from salary_components
        Schema::table('salary_components', function (Blueprint $table) {
            $table->dropIndex('idx_company_active');
            $table->dropIndex('idx_execution_order');
            $table->dropIndex('idx_effective_range');
        });
        Schema::table('salary_components', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id', 'category', 'value_type', 'formula', 'is_prorated',
                'prorate_basis', 'execution_order', 'depends_on', 'min_value', 'max_value',
                'effective_from', 'effective_until', 'version', 'parent_id',
            ]);
        });
    }
};
