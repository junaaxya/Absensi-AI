<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('system_settings', 'bpjs_kes_ceiling')) {
                $table->decimal('bpjs_kes_ceiling', 15, 2)->default(12000000);
            }
            if (!Schema::hasColumn('system_settings', 'bpjs_jp_ceiling')) {
                $table->decimal('bpjs_jp_ceiling', 15, 2)->default(10042300);
            }
            if (!Schema::hasColumn('system_settings', 'jkk_risk_group')) {
                $table->tinyInteger('jkk_risk_group')->default(1);
            }
            if (!Schema::hasColumn('system_settings', 'no_npwp_surcharge_enabled')) {
                $table->boolean('no_npwp_surcharge_enabled')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $columns = ['bpjs_kes_ceiling', 'bpjs_jp_ceiling', 'jkk_risk_group', 'no_npwp_surcharge_enabled'];
            $existing = array_filter($columns, fn ($col) => Schema::hasColumn('system_settings', $col));
            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};
