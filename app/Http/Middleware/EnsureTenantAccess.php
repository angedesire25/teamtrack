<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pas de tenant résolu → domaine central, on laisse passer
        if (! app()->has('tenant')) {
            return $next($request);
        }

        $tenant = app('tenant');

        if (! auth()->check()) {
            return redirect()->route('login');
        }

        // Super admin en mode impersonation → accès autorisé
        if (session('impersonating_super_admin_id')) {
            return $next($request);
        }

        // Super admin direct (sans impersonation) → refusé sur les routes tenant
        if (auth()->user()->is_super_admin) {
            abort(403, 'Accès réservé aux membres du club.');
        }

        // Vérifier l'appartenance au tenant courant
        if (auth()->user()->tenant_id !== $tenant->id) {
            abort(403, 'Vous n\'appartenez pas à ce club.');
        }

        // Vérifier que le compte est actif
        if (! auth()->user()->is_active) {
            abort(403, 'Votre compte est désactivé. Contactez l\'administrateur de votre club.');
        }

        return $next($request);
    }
}
