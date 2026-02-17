<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /** @return array<int, array{name: string, date: string, description: string}> */
    public static function getDefaultHolidays(int $year = 2026): array
    {
        return [
            ['name' => 'Tahun Baru Masehi', 'date' => "{$year}-01-01", 'description' => 'Tahun Baru Masehi'],
            ['name' => 'Isra Mi\'raj Nabi Muhammad SAW', 'date' => "{$year}-01-27", 'description' => '27 Rajab 1447 Hijriah'],
            ['name' => 'Tahun Baru Imlek 2577', 'date' => "{$year}-02-17", 'description' => 'Tahun Baru Imlek 2577 Kongzili'],
            ['name' => 'Hari Raya Nyepi', 'date' => "{$year}-03-19", 'description' => 'Tahun Baru Saka 1948'],
            ['name' => 'Cuti Bersama Idul Fitri', 'date' => "{$year}-03-27", 'description' => 'Cuti Bersama Hari Raya Idul Fitri'],
            ['name' => 'Cuti Bersama Idul Fitri', 'date' => "{$year}-03-28", 'description' => 'Cuti Bersama Hari Raya Idul Fitri'],
            ['name' => 'Hari Raya Idul Fitri 1447H (Hari 1)', 'date' => "{$year}-03-30", 'description' => '1 Syawal 1447 Hijriah'],
            ['name' => 'Hari Raya Idul Fitri 1447H (Hari 2)', 'date' => "{$year}-03-31", 'description' => '2 Syawal 1447 Hijriah'],
            ['name' => 'Cuti Bersama Idul Fitri', 'date' => "{$year}-04-01", 'description' => 'Cuti Bersama Hari Raya Idul Fitri'],
            ['name' => 'Cuti Bersama Idul Fitri', 'date' => "{$year}-04-02", 'description' => 'Cuti Bersama Hari Raya Idul Fitri'],
            ['name' => 'Wafat Isa Al Masih', 'date' => "{$year}-04-03", 'description' => 'Jumat Agung'],
            ['name' => 'Hari Buruh Internasional', 'date' => "{$year}-05-01", 'description' => 'International Labour Day'],
            ['name' => 'Hari Raya Waisak 2570', 'date' => "{$year}-05-12", 'description' => 'Hari Raya Tri Suci Waisak 2570 BE'],
            ['name' => 'Kenaikan Isa Al Masih', 'date' => "{$year}-05-14", 'description' => 'Kenaikan Yesus Kristus'],
            ['name' => 'Hari Lahir Pancasila', 'date' => "{$year}-06-01", 'description' => 'Hari Lahir Pancasila'],
            ['name' => 'Hari Raya Idul Adha 1447H', 'date' => "{$year}-06-06", 'description' => '10 Dzulhijjah 1447 Hijriah'],
            ['name' => 'Tahun Baru Islam 1448H', 'date' => "{$year}-06-27", 'description' => '1 Muharram 1448 Hijriah'],
            ['name' => 'Hari Kemerdekaan RI', 'date' => "{$year}-08-17", 'description' => 'HUT Kemerdekaan Republik Indonesia ke-81'],
            ['name' => 'Maulid Nabi Muhammad SAW', 'date' => "{$year}-09-05", 'description' => '12 Rabiul Awal 1448 Hijriah'],
            ['name' => 'Hari Natal', 'date' => "{$year}-12-25", 'description' => 'Hari Raya Natal'],
            ['name' => 'Cuti Bersama Natal', 'date' => "{$year}-12-26", 'description' => 'Cuti Bersama Hari Raya Natal'],
        ];
    }

    public function run(): void
    {
        self::importHolidays();
    }

    /** @return array{imported: int, skipped: int} */
    public static function importHolidays(int $year = 2026): array
    {
        $holidays = self::getDefaultHolidays($year);
        $imported = 0;
        $skipped = 0;

        foreach ($holidays as $holiday) {
            $exists = Holiday::where('date', $holiday['date'])
                ->where('name', $holiday['name'])
                ->exists();

            if (!$exists) {
                Holiday::create([
                    'name' => $holiday['name'],
                    'date' => $holiday['date'],
                    'description' => $holiday['description'],
                    'is_national' => true,
                ]);
                $imported++;
            } else {
                $skipped++;
            }
        }

        return compact('imported', 'skipped');
    }
}
