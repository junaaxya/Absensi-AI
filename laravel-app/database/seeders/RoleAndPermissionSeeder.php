<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_all_attendance',
            'view_department_attendance',
            'view_team_attendance',
            'view_self_attendance',
            'approve_department_izin',
            'approve_team_izin',
            'request_izin',
            'request_izin_limited',
            'manage_employees',
            'manage_departments',
            'manage_shifts',
            'manage_holidays',
            'manage_leave_types',
            'manage_announcements',
            'manage_system_settings',
            'view_audit_logs',
            'manage_backups',
            'export_data',
            'manage_face_data',
            'manage_violations',
            'view_anomaly_attendance',
            'manage_payroll',
            'view_payroll',
            'view_visit_attendance',
            'manage_leave_balances',
            'manage_recruitment',
            'manage_projects',
            'manage_assets',
            'manage_tickets',
            'create_tickets',
            'manage_forms',
            'manage_training',
            'manage_companies',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define roles and assign permissions
        $roleDirektur = Role::firstOrCreate(['name' => 'Direktur', 'guard_name' => 'web']);
        $roleDirektur->syncPermissions(array_diff($permissions, ['request_izin_limited']));

        $roleVP = Role::firstOrCreate(['name' => 'Vice President', 'guard_name' => 'web']);
        $roleVP->syncPermissions(array_diff($permissions, ['request_izin_limited', 'manage_system_settings', 'manage_backups']));

        $roleManager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $roleManager->syncPermissions([
            'view_department_attendance',
            'view_team_attendance',
            'view_self_attendance',
            'approve_department_izin',
            'approve_team_izin',
            'request_izin',
            'manage_announcements',
            'export_data',
            'manage_violations',
            'view_anomaly_attendance',
            'view_payroll',
            'view_visit_attendance',
            'manage_leave_balances',
            'manage_recruitment',
            'manage_projects',
            'manage_assets',
            'manage_tickets',
            'create_tickets',
            'manage_forms',
            'manage_training',
        ]);

        $roleSupervisor = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $roleSupervisor->syncPermissions([
            'view_team_attendance',
            'view_self_attendance',
            'approve_team_izin',
            'request_izin',
            'view_visit_attendance',
            'manage_projects',
            'manage_tickets',
            'create_tickets',
        ]);

        $roleTeamLeader = Role::firstOrCreate(['name' => 'Team Leader', 'guard_name' => 'web']);
        $roleTeamLeader->syncPermissions([
            'view_team_attendance',
            'view_self_attendance',
            'approve_team_izin',
            'request_izin',
            'manage_projects',
            'create_tickets',
        ]);

        $roleStaf = Role::firstOrCreate(['name' => 'Staf', 'guard_name' => 'web']);
        $roleStaf->syncPermissions([
            'view_self_attendance',
            'request_izin',
            'create_tickets',
        ]);

        $roleMagang = Role::firstOrCreate(['name' => 'Magang', 'guard_name' => 'web']);
        $roleMagang->syncPermissions([
            'view_self_attendance',
            'request_izin_limited',
            'create_tickets',
        ]);
    }
}