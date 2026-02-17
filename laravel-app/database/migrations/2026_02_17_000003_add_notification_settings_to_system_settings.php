<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->boolean('notify_late_checkin')->default(false);
            $table->boolean('notify_absence')->default(false);
            $table->text('notification_emails')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'notify_late_checkin',
                'notify_absence',
                'notification_emails',
            ]);
        });
    }
};
