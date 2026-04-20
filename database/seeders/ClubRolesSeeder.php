<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ClubRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin_club'         => 'Admin Club',
            'manager'            => 'Manager',
            'entraineur'         => 'Entraîneur',
            'staff_medical'      => 'Staff Médical',
            'secretaire'         => 'Secrétaire',
            'gestionnaire_stock' => 'Gestionnaire Stock',
            'comptable'          => 'Comptable',
        ];

        foreach ($roles as $name => $label) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
}
