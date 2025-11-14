<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tenant IDs for foreign key references
        $tenants = DB::table('tenants')->pluck('id', 'code')->toArray();

        // Super Admin Kabupaten (global user, no tenant)
        DB::table('users')->updateOrInsert(
            ['email' => 'superadmin@kabupaten.test'],
            [
                'name' => 'Super Admin Kabupaten',
                'email' => 'superadmin@kabupaten.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'super_admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Verifikator Kabupaten (global user, no tenant)
        DB::table('users')->updateOrInsert(
            ['email' => 'verifikator@kabupaten.test'],
            [
                'name' => 'Verifikator Kabupaten',
                'email' => 'verifikator@kabupaten.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'verifikator_kabupaten',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Admin Dinas for DISDIKBUD
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@disdikbud.test'],
            [
                'name' => 'Admin Dinas Pendidikan',
                'email' => 'admin@disdikbud.test',
                'password' => Hash::make('password'),
                'tenant_id' => $tenants['DISDIKBUD'] ?? null,
                'email_verified_at' => now(),
                'role' => 'admin_dinas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Verifikator Dinas for DISDIKBUD
        DB::table('users')->updateOrInsert(
            ['email' => 'verifikator@disdikbud.test'],
            [
                'name' => 'Verifikator Dinas Pendidikan',
                'email' => 'verifikator@disdikbud.test',
                'password' => Hash::make('password'),
                'tenant_id' => $tenants['DISDIKBUD'] ?? null,
                'email_verified_at' => now(),
                'role' => 'verifikator_dinas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Operator Dinas for DISDIKBUD
        DB::table('users')->updateOrInsert(
            ['email' => 'operator@disdikbud.test'],
            [
                'name' => 'Operator Dinas Pendidikan',
                'email' => 'operator@disdikbud.test',
                'password' => Hash::make('password'),
                'tenant_id' => $tenants['DISDIKBUD'] ?? null,
                'email_verified_at' => now(),
                'role' => 'operator_dinas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Pegawai users (for demo purposes)
        DB::table('users')->updateOrInsert(
            ['email' => 'ahmad.fauzi@disdikbud.test'],
            [
                'name' => 'Ahmad Fauzi, S.Pd',
                'email' => 'ahmad.fauzi@disdikbud.test',
                'password' => Hash::make('password'),
                'tenant_id' => $tenants['DISDIKBUD'] ?? null,
                'email_verified_at' => now(),
                'role' => 'pegawai',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'siti.aminah@disdikbud.test'],
            [
                'name' => 'Siti Aminah, S.Pd.I',
                'email' => 'siti.aminah@disdikbud.test',
                'password' => Hash::make('password'),
                'tenant_id' => $tenants['DISDIKBUD'] ?? null,
                'email_verified_at' => now(),
                'role' => 'pegawai',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Admin Dinas for other tenants
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@dinkes.test'],
            [
                'name' => 'Admin Dinas Kesehatan',
                'email' => 'admin@dinkes.test',
                'password' => Hash::make('password'),
                'tenant_id' => $tenants['DISKES'] ?? null,
                'email_verified_at' => now(),
                'role' => 'admin_dinas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@pupr.test'],
            [
                'name' => 'Admin Dinas PUPR',
                'email' => 'admin@pupr.test',
                'password' => Hash::make('password'),
                'tenant_id' => $tenants['PUPR'] ?? null,
                'email_verified_at' => now(),
                'role' => 'admin_dinas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}