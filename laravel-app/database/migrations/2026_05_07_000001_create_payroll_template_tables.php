<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // payroll_templates
        Schema::create('payroll_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('industry_type', 50)->nullable();
            $table->string('company_size', 20)->nullable();
            $table->boolean('includes_bpjs')->default(true);
            $table->boolean('includes_pph21')->default(true);
            $table->boolean('includes_overtime')->default(true);
            $table->smallInteger('component_count')->unsigned()->default(0);
            $table->unsignedInteger('popularity_score')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(true);
            $table->string('icon', 50)->nullable();
            $table->smallInteger('sort_order')->unsigned()->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // payroll_template_items
        Schema::create('payroll_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_template_id')->constrained('payroll_templates')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('code', 50);
            $table->enum('type', ['earning', 'deduction', 'benefit']);
            $table->enum('category', ['basic', 'earning', 'deduction', 'benefit', 'bpjs_employee', 'bpjs_company', 'tax']);
            $table->enum('value_type', ['flat', 'formula'])->default('flat');
            $table->string('formula', 500)->nullable();
            $table->decimal('default_amount', 15, 2)->default(0);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_fixed')->default(true);
            $table->boolean('is_prorated')->default(false);
            $table->enum('prorate_basis', ['working_days', 'calendar_days'])->nullable();
            $table->smallInteger('execution_order')->unsigned()->default(100);
            $table->json('depends_on')->nullable();
            $table->decimal('min_value', 15, 2)->nullable();
            $table->decimal('max_value', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('help_text', 500)->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_optional')->default(false);
            $table->string('group_label', 100)->nullable();
            $table->smallInteger('sort_within_group')->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['payroll_template_id', 'code'], 'uq_template_item_code');
        });

        // company_template_applications
        Schema::create('company_template_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->foreignId('payroll_template_id')->constrained('payroll_templates');
            $table->foreignId('applied_by')->constrained('users');
            $table->timestamp('applied_at')->nullable();
            $table->enum('mode', ['fresh', 'merge', 'replace'])->default('fresh');
            $table->unsignedInteger('components_created')->default(0);
            $table->unsignedInteger('components_skipped')->default(0);
            $table->unsignedInteger('components_updated')->default(0);
            $table->text('notes')->nullable();
            $table->json('rollback_snapshot')->nullable();
            $table->timestamps();
        });

        // ALTER salary_components — add template tracking columns
        Schema::table('salary_components', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_components', 'source_template_id')) {
                $table->unsignedBigInteger('source_template_id')->nullable()->after('parent_id');
            }
            if (!Schema::hasColumn('salary_components', 'source_template_item_id')) {
                $table->unsignedBigInteger('source_template_item_id')->nullable()->after('source_template_id');
            }
            if (!Schema::hasColumn('salary_components', 'help_text')) {
                $table->string('help_text', 500)->nullable()->after('description');
            }
            if (!Schema::hasColumn('salary_components', 'group_label')) {
                $table->string('group_label', 100)->nullable()->after('help_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            $columns = [];
            foreach (['source_template_id', 'source_template_item_id', 'help_text', 'group_label'] as $col) {
                if (Schema::hasColumn('salary_components', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        Schema::dropIfExists('company_template_applications');
        Schema::dropIfExists('payroll_template_items');
        Schema::dropIfExists('payroll_templates');
    }
};
