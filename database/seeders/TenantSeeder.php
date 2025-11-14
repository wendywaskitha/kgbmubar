<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'Dinas Pendidikan dan Kebudayaan',
                'code' => 'DISDIKBUD',
                'email' => 'disdikbud@kabupaten.test',
                'phone' => '021-12345678',
                'address' => 'Jl. Pendidikan No. 1, Kabupaten',
            ],
            [
                'name' => 'Dinas Kesehatan',
                'code' => 'DISKES',
                'email' => 'dinkes@kabupaten.test',
                'phone' => '021-12345679',
                'address' => 'Jl. Kesehatan No. 2, Kabupaten',
            ],
            [
                'name' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'code' => 'PUPR',
                'email' => 'pupr@kabupaten.test',
                'phone' => '021-12345680',
                'address' => 'Jl. Pembangunan No. 3, Kabupaten',
            ],
            [
                'name' => 'Dinas Sosial',
                'code' => 'DINSOS',
                'email' => 'dinsos@kabupaten.test',
                'phone' => '021-12345681',
                'address' => 'Jl. Sosial No. 4, Kabupaten',
            ],
            [
                'name' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'code' => 'DISDUKCAPIL',
                'email' => 'dukcapil@kabupaten.test',
                'phone' => '021-12345682',
                'address' => 'Jl. Demografi No. 5, Kabupaten',
            ],
        ];

        foreach ($tenants as $tenant) {
            DB::table('tenants')->updateOrInsert(
                ['code' => $tenant['code']],
                [
                    'name' => $tenant['name'],
                    'email' => $tenant['email'],
                    'phone' => $tenant['phone'],
                    'address' => $tenant['address'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}