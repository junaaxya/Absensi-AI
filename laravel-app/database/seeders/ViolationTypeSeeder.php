<?php

namespace Database\Seeders;

use App\Models\ViolationType;
use Illuminate\Database\Seeder;

class ViolationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'TELAT_1', 'name' => 'Terlambat 1-15 menit', 'points' => 1, 'is_auto' => true],
            ['code' => 'TELAT_2', 'name' => 'Terlambat 16-30 menit', 'points' => 2, 'is_auto' => true],
            ['code' => 'TELAT_3', 'name' => 'Terlambat 31-60 menit', 'points' => 3, 'is_auto' => true],
            ['code' => 'TELAT_4', 'name' => 'Terlambat >60 menit', 'points' => 5, 'is_auto' => true],
            ['code' => 'ALPHA', 'name' => 'Tidak hadir tanpa keterangan', 'points' => 10, 'is_auto' => true],
            ['code' => 'MANUAL', 'name' => 'Pelanggaran manual', 'points' => 0, 'is_auto' => false],
        ];

        foreach ($types as $type) {
            ViolationType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
