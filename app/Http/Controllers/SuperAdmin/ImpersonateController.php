<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;

/**
 * Gère l'impersonation : permet au super admin de se connecter
 * en tant qu'administrateur d'un club, puis de revenir à son compte.
 */
class ImpersonateController extends Controller
{
    /** Démarre l'impersonation sur le premier admin du club */
    public function start(Tenant $tenant)
    {
        $admin = $tenant->users()->first();

        if (! $admin) {
            return back()->with('error', "Ce club n'a aucun utilisateur.");
        }

        // Mémorise l'ID du super admin pour pouvoir y revenir
        session(['impersonating_super_admin_id' => auth()->id()]);

        auth()->login($admin);

        return redirect('/dashboard')->with('status', "Mode impersonation activé pour {$tenant->name}.");
    }

    /** Arrête l'impersonation et restitue la session super admin */
    public function stop()
    {
        $superAdminId = session('impersonating_super_admin_id');

        if (! $superAdminId) {
            return redirect()->route('superadmin.dashboard');
        }

        $superAdmin = User::find($superAdminId);

        session()->forget('impersonating_super_admin_id');

        if ($superAdmin) {
            auth()->login($superAdmin);
        }

        return redirect()->route('superadmin.dashboard');
    }
}
