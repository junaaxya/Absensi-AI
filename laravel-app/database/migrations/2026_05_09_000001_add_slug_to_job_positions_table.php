<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_positions', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        // Backfill existing positions with slugs
        $positions = \DB::table('job_positions')->get();
        foreach ($positions as $position) {
            \DB::table('job_positions')
                ->where('id', $position->id)
                ->update(['slug' => Str::slug($position->title) . '-' . $position->id]);
        }
    }

    public function down(): void
    {
        Schema::table('job_positions', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
