<?php

namespace App\Livewire\Club\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.club')]
class Index extends Component
{
    public bool    $showModal   = false;
    public ?int    $editingId   = null;
    public string  $name        = '';
    public string  $email       = '';
    public string  $role        = 'secretaire';
    public bool    $is_active   = true;

    // Mot de passe généré affiché une seule fois après création
    public ?string $generatedPassword = null;

    public const ROLES = [
        'admin_club'         => 'Admin Club',
        'manager'            => 'Manager',
        'entraineur'         => 'Entraîneur',
        'staff_medical'      => 'Staff Médical',
        'secretaire'         => 'Secrétaire',
        'gestionnaire_stock' => 'Gestionnaire Stock',
        'comptable'          => 'Comptable',
    ];

    public function openInvite(): void
    {
        $this->reset(['editingId', 'name', 'email', 'role', 'is_active', 'generatedPassword']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $user            = User::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->role      = $user->roles->first()?->name ?? 'secretaire';
        $this->is_active = $user->is_active;
        $this->generatedPassword = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $tenantId = app()->has('tenant') ? app('tenant')->id : auth()->user()->tenant_id;

        if ($this->editingId) {
            $this->validate([
                'name'  => 'required|string|max:100',
                'email' => "required|email|unique:users,email,{$this->editingId}",
                'role'  => 'required|in:' . implode(',', array_keys(self::ROLES)),
            ]);

            $user = User::findOrFail($this->editingId);
            $user->update([
                'name'      => $this->name,
                'email'     => $this->email,
                'is_active' => $this->is_active,
            ]);
            $user->syncRoles([$this->role]);
            $this->dispatch('toast', message: 'Utilisateur mis à jour.', type: 'success');
        } else {
            $this->validate([
                'name'  => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'role'  => 'required|in:' . implode(',', array_keys(self::ROLES)),
            ]);

            $password = Str::password(12);

            $user = User::create([
                'tenant_id'  => $tenantId,
                'name'       => $this->name,
                'email'      => $this->email,
                'password'   => Hash::make($password),
                'is_active'  => true,
                'is_super_admin' => false,
            ]);
            $user->assignRole($this->role);

            $this->generatedPassword = $password;
            $this->dispatch('toast', message: 'Utilisateur créé. Notez le mot de passe.', type: 'success');
            return; // On garde le modal ouvert pour afficher le mot de passe
        }

        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);

        // Empêcher la désactivation du dernier admin actif
        if ($user->is_active && $user->hasRole('admin_club')) {
            $activeAdmins = User::role('admin_club')->where('is_active', true)->count();
            if ($activeAdmins <= 1) {
                $this->dispatch('toast', message: 'Impossible : dernier admin actif du club.', type: 'error');
                return;
            }
        }

        $user->update(['is_active' => ! $user->is_active]);
        $this->dispatch('toast', message: 'Statut du compte mis à jour.', type: 'success');
    }

    public function render()
    {
        $tenantId = app()->has('tenant') ? app('tenant')->id : auth()->user()->tenant_id;

        return view('livewire.club.users.index', [
            'users' => User::with('roles')
                ->where('tenant_id', $tenantId)
                ->orderBy('name')
                ->get(),
        ])->title('Utilisateurs');
    }
}
