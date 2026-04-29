<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->double('lat_in')->nullable()->after('status');
            $table->double('long_in')->nullable()->after('lat_in');
            $table->double('lat_out')->nullable()->after('long_in');
            $table->double('long_out')->nullable()->after('lat_out');
            $table->double('similarity_score_in')->nullable()->after('long_out');
            $table->double('similarity_score_out')->nullable()->after('similarity_score_in');
            $table->string('device_info')->nullable()->after('similarity_score_out');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'lat_in',
                'long_in',
                'lat_out',
                'long_out',
                'similarity_score_in',
                'similarity_score_out',
                'device_info',
            ]);
        });
    }
};
