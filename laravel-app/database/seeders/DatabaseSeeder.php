<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::factory()->create([
            'name' => 'Admin Direktur',
            'email' => 'admin@absensi.test',
            'username' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            RoleAndPermissionSeeder::class,
            SystemSettingSeeder::class,
            HolidaySeeder::class,
            ViolationTypeSeeder::class,
            PayrollSeeder::class,
            DynamicPayrollSeeder::class,
        ]);

        // Assign Direktur role to admin user (after roles are seeded)
        $admin->assignRole('Direktur');
    }
}
