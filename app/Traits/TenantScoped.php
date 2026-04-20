<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait TenantScoped
{
    protected static function bootTenantScoped(): void
    {
        // Scope global : filtre automatiquement par tenant_id sur toutes les requêtes
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = self::resolveTenantId();
            if ($tenantId) {
                $builder->where((new static)->qualifyColumn('tenant_id'), $tenantId);
            }
        });

        // Injection automatique du tenant_id à la création
        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $tenantId = self::resolveTenantId();
                if ($tenantId) {
                    $model->tenant_id = $tenantId;
                }
            }
        });
    }

    // Résout le tenant_id depuis le conteneur ou l'utilisateur connecté
    protected static function resolveTenantId(): ?int
    {
        if (app()->has('tenant')) {
            return app('tenant')->id;
        }

        if (auth()->hasUser() && auth()->user()->tenant_id) {
            return auth()->user()->tenant_id;
        }

        return null;
    }
}
