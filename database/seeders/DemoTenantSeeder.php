<?php

/**
 * Seeder de démonstration — crée le club ASEC Mimosas avec son administrateur.
 * Utilisé pour tester et présenter l'application en environnement local.
 */

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        // Récupération du plan Pro (doit exister après PlanSeeder)
        $plan = Plan::where('slug', 'pro')->firstOrFail();

        // Création ou mise à jour du tenant de démonstration
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'asec'],
            [
                'plan_id'         => $plan->id,
                'name'            => 'ASEC Mimosas',
                'slug'            => 'asec',
                'subdomain'       => 'asec',
                'custom_domain'   => null,
                'email'           => 'admin@asec.test',
                'phone'           => null,
                'city'            => 'Abidjan',
                'country'         => 'CI',
                'logo'            => null,
                'primary_color'   => '#1E3A5F',
                'secondary_color' => '#2E75B6',
                'status'          => 'active',
                'trial_ends_at'   => null,
                'suspended_at'    => null,
            ]
        );

        // Création de l'administrateur du club
        User::updateOrCreate(
            ['email' => 'admin@asec.test'],
            [
                'tenant_id'      => $tenant->id,
                'name'           => 'Admin ASEC',
                'email'          => 'admin@asec.test',
                'password'       => Hash::make('password'),
                'is_super_admin' => false,
                'is_active'      => true,
            ]
        );
    }
}
