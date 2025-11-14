<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign roles to users based on their role field
        $users = User::all();

        foreach ($users as $user) {
            // Clear existing roles
            $user->roles()->detach();

            // Get appropriate role based on user's role field
            $roleName = $this->getRoleName($user->role);
            if ($roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $user->assignRole($role);
                }
            }
        }
    }


    private function getRoleName($roleKey)
    {
        $roleMap = [
            'super_admin' => 'Super Admin Kabupaten',
            'verifikator_kabupaten' => 'Verifikator Kabupaten',
            'admin_dinas' => 'Admin Dinas',
            'verifikator_dinas' => 'Verifikator Dinas',
            'operator_dinas' => 'Operator Dinas',
            'pegawai' => 'Pegawai',
        ];

        return $roleMap[$roleKey] ?? null;
    }
}
