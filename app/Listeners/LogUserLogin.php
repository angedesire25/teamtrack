<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\UserAuthActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! ($user instanceof User)) {
            return;
        }

        // Notifier tous les admin_club du même tenant (sauf l'utilisateur lui-même s'il est admin)
        $tenantId = $user->tenant_id;
        if (! $tenantId) {
            return;
        }

        $notification = new UserAuthActivity(
            event: 'login',
            userName: $user->name,
            userEmail: $user->email,
            ip: Request::ip(),
            occurredAt: now(),
        );

        // Notifier les admin_club du même tenant
        $admins = User::where('tenant_id', $tenantId)
            ->whereHas('roles', fn($q) => $q->where('name', 'admin_club'))
            ->get();

        foreach ($admins as $admin) {
            // Éviter la double notification si l'utilisateur qui se connecte est lui-même admin
            if ($admin->id === $user->id) {
                continue;
            }
            $admin->notify($notification);
        }

        // Notifier également l'utilisateur lui-même (confirmation de sa propre connexion)
        $user->notify(new UserAuthActivity(
            event: 'login',
            userName: $user->name,
            userEmail: $user->email,
            ip: Request::ip(),
            occurredAt: now(),
        ));
    }
}
