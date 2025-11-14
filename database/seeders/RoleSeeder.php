<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for admin panel (Kabupaten/BKPSDM)
        $adminPermissions = [
            // Tenant management permissions
            'view tenants',
            'create tenants',
            'edit tenants',
            'delete tenants',
            
            // Pengajuan KGB management permissions
            'view all pengajuan',
            'view pengajuan kabupaten',
            'approve pengajuan',
            'reject pengajuan',
            'view pending verifikasi kabupaten',
            
            // User management permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // System settings permissions
            'view system settings',
            'edit system settings',
            
            // Report permissions
            'view reports',
            'export reports',
        ];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create permissions for app panel (Dinas/OPD)
        $dinasPermissions = [
            // Pegawai management permissions
            'view pegawai',
            'create pegawai',
            'edit pegawai',
            'delete pegawai',
            
            // Pengajuan KGB management permissions
            'view pengajuan',
            'create pengajuan',
            'edit pengajuan',
            'delete pengajuan',
            'submit pengajuan',
            'view own pengajuan',
            
            // Verification permissions
            'verify pengajuan',
            'view verifikasi',
            
            // Report permissions
            'view dinas reports',
            'export dinas reports',
        ];

        foreach ($dinasPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create permissions for pegawai panel (Self-Service)
        $pegawaiPermissions = [
            // Self-service permissions
            'view own profile',
            'edit own profile',
            'view own pengajuan',
            'create own pengajuan',
            'upload own documents',
            'download own documents',
            'download own sk',
            'view pengajuan status',
        ];

        foreach ($pegawaiPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles based on PRD
        // Super Admin Kabupaten
        $superAdminKabupaten = Role::firstOrCreate(['name' => 'Super Admin Kabupaten', 'guard_name' => 'web']);
        $superAdminKabupaten->givePermissionTo($adminPermissions);

        // Verifikator Kabupaten
        $verifikatorKabupaten = Role::firstOrCreate(['name' => 'Verifikator Kabupaten', 'guard_name' => 'web']);
        $verifikatorKabupaten->givePermissionTo([
            'view all pengajuan',
            'view pengajuan kabupaten',
            'approve pengajuan',
            'reject pengajuan',
            'view pending verifikasi kabupaten',
            'view reports',
        ]);

        // Admin Dinas
        $adminDinas = Role::firstOrCreate(['name' => 'Admin Dinas', 'guard_name' => 'web']);
        $adminDinas->givePermissionTo($dinasPermissions);
        $adminDinas->givePermissionTo(['create pegawai', 'edit pegawai']); // Additional permissions

        // Verifikator Dinas
        $verifikatorDinas = Role::firstOrCreate(['name' => 'Verifikator Dinas', 'guard_name' => 'web']);
        $verifikatorDinas->givePermissionTo([
            'view pegawai',
            'view pengajuan',
            'verify pengajuan',
            'view verifikasi',
            'view dinas reports',
        ]);

        // Operator Dinas
        $operatorDinas = Role::firstOrCreate(['name' => 'Operator Dinas', 'guard_name' => 'web']);
        $operatorDinas->givePermissionTo([
            'view pegawai',
            'create pengajuan',
            'edit pengajuan',
            'submit pengajuan',
            'view own pengajuan',
        ]);

        // Pegawai
        $pegawai = Role::firstOrCreate(['name' => 'Pegawai', 'guard_name' => 'web']);
        $pegawai->givePermissionTo($pegawaiPermissions);
    }
}