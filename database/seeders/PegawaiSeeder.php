<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tenant IDs for foreign key references
        $tenants = DB::table('tenants')->pluck('id', 'code')->toArray();

        $pegawaiData = [
            [
                'tenant_code' => 'DISDIKBUD',
                'nip' => '198501012010011001',
                'name' => 'Ahmad Fauzi, S.Pd',
                'nrk' => '1234567890',
                'pangkat_golongan' => 'Penata Tk. I / III.d',
                'jabatan' => 'Guru Senior',
                'unit_kerja' => 'SMP Negeri 1',
                'tmt_pangkat_terakhir' => '2020-01-01',
                'tmt_kgb_terakhir' => '2022-01-01',
                'tmt_kgb_berikutnya' => '2024-01-01',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1985-01-01',
                'tempat_lahir' => 'Kabupaten',
                'status_kepegawaian' => 'PNS',
                'email' => 'ahmad.fauzi@disdikbud.test',
                'phone' => '081234567890',
            ],
            [
                'tenant_code' => 'DISDIKBUD',
                'nip' => '198002022008021002',
                'name' => 'Siti Aminah, S.Pd.I',
                'nrk' => '1234567891',
                'pangkat_golongan' => 'Pembina Tk. I / IV.b',
                'jabatan' => 'Kepala Sekolah',
                'unit_kerja' => 'SD Negeri 1',
                'tmt_pangkat_terakhir' => '2019-06-01',
                'tmt_kgb_terakhir' => '2021-06-01',
                'tmt_kgb_berikutnya' => '2023-06-01',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1980-02-02',
                'tempat_lahir' => 'Kabupaten',
                'status_kepegawaian' => 'PNS',
                'email' => 'siti.aminah@disdikbud.test',
                'phone' => '081234567891',
            ],
            [
                'tenant_code' => 'DISKES',
                'nip' => '197803032007031001',
                'name' => 'Dr. Budi Santoso, M.Kes',
                'nrk' => '1234567892',
                'pangkat_golongan' => 'Pembina Utama Muda / IV.c',
                'jabatan' => 'Kepala Instalasi',
                'unit_kerja' => 'Puskesmas Pusat',
                'tmt_pangkat_terakhir' => '2020-03-01',
                'tmt_kgb_terakhir' => '2022-03-01',
                'tmt_kgb_berikutnya' => '2024-03-01',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1978-03-03',
                'tempat_lahir' => 'Kabupaten',
                'status_kepegawaian' => 'PNS',
                'email' => 'budi.santoso@dinkes.test',
                'phone' => '081234567892',
            ],
            [
                'tenant_code' => 'PUPR',
                'nip' => '198204042010041001',
                'name' => 'Ir. Cahyo Wibowo',
                'nrk' => '1234567893',
                'pangkat_golongan' => 'Penata / III.c',
                'jabatan' => 'Pelaksana Proyek',
                'unit_kerja' => 'Bidang Bina Marga',
                'tmt_pangkat_terakhir' => '2021-04-01',
                'tmt_kgb_terakhir' => '2023-04-01',
                'tmt_kgb_berikutnya' => '2025-04-01',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1982-04-04',
                'tempat_lahir' => 'Kabupaten',
                'status_kepegawaian' => 'PNS',
                'email' => 'cahyo.wibowo@pupr.test',
                'phone' => '081234567893',
            ],
            [
                'tenant_code' => 'DINSOS',
                'nip' => '197505052005051001',
                'name' => 'Dra. Endang Suryani',
                'nrk' => '1234567894',
                'pangkat_golongan' => 'Pembina / IV.a',
                'jabatan' => 'Kasubag Perencanaan',
                'unit_kerja' => 'Subbagian Perencanaan',
                'tmt_pangkat_terakhir' => '2020-05-01',
                'tmt_kgb_terakhir' => '2022-05-01',
                'tmt_kgb_berikutnya' => '2024-05-01',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1975-05-05',
                'tempat_lahir' => 'Kabupaten',
                'status_kepegawaian' => 'PNS',
                'email' => 'endang.suryani@dinsos.test',
                'phone' => '081234567894',
            ],
        ];

        foreach ($pegawaiData as $pegawai) {
            DB::table('pegawai')->updateOrInsert(
                ['nip' => $pegawai['nip']],
                [
                    'tenant_id' => $tenants[$pegawai['tenant_code']] ?? null,
                    'nip' => $pegawai['nip'],
                    'name' => $pegawai['name'],
                    'nrk' => $pegawai['nrk'],
                    'pangkat_golongan' => $pegawai['pangkat_golongan'],
                    'jabatan' => $pegawai['jabatan'],
                    'unit_kerja' => $pegawai['unit_kerja'],
                    'tmt_pangkat_terakhir' => $pegawai['tmt_pangkat_terakhir'],
                    'tmt_kgb_terakhir' => $pegawai['tmt_kgb_terakhir'],
                    'tmt_kgb_berikutnya' => $pegawai['tmt_kgb_berikutnya'],
                    'jenis_kelamin' => $pegawai['jenis_kelamin'],
                    'tanggal_lahir' => $pegawai['tanggal_lahir'],
                    'tempat_lahir' => $pegawai['tempat_lahir'],
                    'status_kepegawaian' => $pegawai['status_kepegawaian'],
                    'email' => $pegawai['email'],
                    'phone' => $pegawai['phone'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}