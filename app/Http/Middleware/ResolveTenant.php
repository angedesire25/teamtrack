<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Domaines réservés à l'interface centrale (super admin, marketing...)
     */
    private const CENTRAL_SUBDOMAINS = ['www', 'app', ''];

    /** Hôtes locaux traités comme le domaine central (dev sans Laragon) */
    private const LOCAL_HOSTS = ['localhost', '127.0.0.1', '::1'];

    public function handle(Request $request, Closure $next): Response
    {
        $host           = $request->getHost();
        $centralDomain  = config('app.domain', 'teamtrack.test');

        // Accès local via IP ou localhost → domaine central
        if (in_array($host, self::LOCAL_HOSTS, true)) {
            return $next($request);
        }

        // Extraction du sous-domaine en retirant le domaine principal
        $subdomain = str($host)->before('.' . $centralDomain)->toString();

        // Domaine central ou sous-domaine réservé → pas de résolution de tenant
        if ($host === $centralDomain || in_array($subdomain, self::CENTRAL_SUBDOMAINS, true)) {
            return $next($request);
        }

        // Recherche par sous-domaine OU domaine personnalisé
        $tenant = Tenant::where('subdomain', $subdomain)
            ->orWhere('custom_domain', $host)
            ->first();

        if (! $tenant) {
            abort(404, 'Club introuvable');
        }

        // Club suspendu → vue dédiée avec code 402
        if ($tenant->isSuspended()) {
            return response()->view('errors.suspended', ['tenant' => $tenant], 402);
        }

        // Injection du tenant dans le conteneur pour la durée de la requête
        App::instance('tenant', $tenant);

        return $next($request);
    }
}
