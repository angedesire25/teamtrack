<?php

/**
 * Seeder principal — orchestre l'ordre d'exécution de tous les seeders.
 * L'ordre est important : Plans → SuperAdmin → DemoTenant.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,       // 1. Plans d'abonnement (requis par les tenants)
            SuperAdminSeeder::class, // 2. Compte super administrateur de la plateforme
            DemoTenantSeeder::class, // 3. Club de démo ASEC Mimosas
        ]);
    }
}
