<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update user 'navarta' to admin
        $updated = User::where('username', 'navarta')
            ->orWhere('name', 'LIKE', '%navarta%')
            ->update(['role' => 'admin']);

        if ($updated === 0) {
            // Log or handle if user not found, but migration typically silent
            // For now we assume user exists as per request
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to karyawan (assuming that was the previous role)
        User::where('username', 'navarta')
            ->orWhere('name', 'LIKE', '%navarta%')
            ->update(['role' => 'karyawan']);
    }
};
