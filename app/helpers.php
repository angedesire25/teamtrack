<?php

use App\Models\Tenant;

if (! function_exists('tenant')) {
    /**
     * Retourne le tenant courant injecté dans le conteneur Laravel.
     * Retourne null si on se trouve sur le domaine central (super admin).
     */
    function tenant(): ?Tenant
    {
        if (! app()->bound('tenant')) {
            return null;
        }

        return app('tenant');
    }
}
