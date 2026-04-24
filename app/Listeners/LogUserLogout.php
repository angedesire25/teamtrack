<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\UserAuthActivity;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class LogUserLogout
{
    public function handle(Logout $event): void
    {
        $user = $event->user;

        if (! ($user instanceof User)) {
            return;
        }

        $tenantId = $user->tenant_id;
        if (! $tenantId) {
            return;
        }

        $notification = new UserAuthActivity(
            event: 'logout',
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
            if ($admin->id === $user->id) {
                continue;
            }
            $admin->notify($notification);
        }
    }
}
