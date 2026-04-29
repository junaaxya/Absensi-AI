<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $users = User::all();
        $migratedCount = 0;

        foreach ($users as $user) {
            $oldRole = $user->role;
            $spatieRole = 'Staf'; // Default fallback

            if ($oldRole === 'admin') {
                $spatieRole = 'Direktur';
            } elseif ($oldRole === 'karyawan' || $oldRole === 'user') {
                $spatieRole = 'Staf';
            }

            // SyncRoles will remove any existing roles and assign the new one.
            // This is safer for re-running migrations.
            $user->syncRoles([$spatieRole]);
            $migratedCount++;
        }

        Log::info("Migrated $migratedCount users to Spatie roles.");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all Spatie role assignments for all Users
        DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->delete();
    }
};
