<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@teamtrack.test'],
            [
                'name'           => 'Super Admin',
                'email'          => 'admin@teamtrack.test',
                'password'       => Hash::make('password'),
                'is_super_admin' => true,
                'tenant_id'      => null,
                'is_active'      => true,
            ]
        );
    }
}
