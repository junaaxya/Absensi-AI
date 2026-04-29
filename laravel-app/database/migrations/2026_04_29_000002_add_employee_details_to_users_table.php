<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->unique()->after('name');
            $table->string('tempat_lahir')->nullable()->after('nik');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->text('alamat')->nullable()->after('jenis_kelamin');
            $table->string('no_telepon', 20)->nullable()->after('alamat');
            $table->string('no_rekening')->nullable()->after('no_telepon');
            $table->string('nama_bank')->nullable()->after('no_rekening');
            $table->string('npwp')->nullable()->after('nama_bank');
            $table->enum('status_pernikahan', ['TK', 'K'])->nullable()->after('npwp');
            $table->tinyInteger('jumlah_tanggungan')->default(0)->after('status_pernikahan');
            $table->date('tanggal_masuk')->nullable()->after('jumlah_tanggungan');
            $table->date('tanggal_keluar')->nullable()->after('tanggal_masuk');
            $table->enum('status_karyawan', ['tetap', 'kontrak', 'magang'])->default('tetap')->after('tanggal_keluar');
            $table->decimal('gaji_pokok', 15, 2)->default(0)->after('status_karyawan');
            $table->string('no_bpjs_kesehatan')->nullable()->after('gaji_pokok');
            $table->string('no_bpjs_ketenagakerjaan')->nullable()->after('no_bpjs_kesehatan');
            $table->string('emergency_contact_name')->nullable()->after('no_bpjs_ketenagakerjaan');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nik']);
            $table->dropColumn([
                'nik',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'alamat',
                'no_telepon',
                'no_rekening',
                'nama_bank',
                'npwp',
                'status_pernikahan',
                'jumlah_tanggungan',
                'tanggal_masuk',
                'tanggal_keluar',
                'status_karyawan',
                'gaji_pokok',
                'no_bpjs_kesehatan',
                'no_bpjs_ketenagakerjaan',
                'emergency_contact_name',
                'emergency_contact_phone',
            ]);
        });
    }
};
