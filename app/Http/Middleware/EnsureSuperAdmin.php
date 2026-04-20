<?php

/**
 * Middleware de protection du panel super administrateur.
 * Bloque tout accès si l'utilisateur n'est pas authentifié ou ne possède pas le flag is_super_admin.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vérification : connecté ET super admin de la plateforme
        if (! auth()->check() || ! auth()->user()->is_super_admin) {
            abort(403, 'Accès réservé aux super administrateurs de la plateforme.');
        }

        return $next($request);
    }
}
