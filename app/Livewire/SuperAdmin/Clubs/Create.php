<?php

namespace App\Livewire\SuperAdmin\Clubs;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Nouveau club')]
class Create extends Component
{
    // --- Informations du club ---
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|string|max:50|unique:tenants,subdomain')]
    public string $subdomain = '';

    #[Validate('required|email|max:150|unique:tenants,email')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|string|max:50')]
    public string $city = '';

    #[Validate('required|string|max:100')]
    public string $country = 'CI';

    #[Validate('required|exists:plans,id')]
    public string $plan_id = '';

    #[Validate('required|in:active,trial')]
    public string $status = 'trial';

    #[Validate('nullable|date')]
    public string $trial_ends_at = '';

    // --- Compte admin du club ---
    #[Validate('required|string|max:100')]
    public string $admin_name = '';

    #[Validate('required|email|max:150|unique:users,email')]
    public string $admin_email = '';

    #[Validate('required|string|min:8')]
    public string $admin_password = '';

    /** Génère automatiquement le sous-domaine depuis le nom du club */
    public function updatedName(): void
    {
        $this->subdomain = Str::slug($this->name);
    }

    /** Enregistre le club et son admin en transaction */
    public function save(): void
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            foreach ($e->validator->errors()->all() as $message) {
                $this->dispatch('toast', message: $message, type: 'error');
            }
            return;
        }

        DB::transaction(function () {
            $tenant = Tenant::create([
                'plan_id'       => $this->plan_id,
                'name'          => $this->name,
                'slug'          => Str::slug($this->name),
                'subdomain'     => $this->subdomain,
                'email'         => $this->email,
                'phone'         => $this->phone ?: null,
                'city'          => $this->city ?: null,
                'country'       => $this->country,
                'status'        => $this->status,
                'trial_ends_at' => $this->status === 'trial' && $this->trial_ends_at
                    ? $this->trial_ends_at
                    : ($this->status === 'trial' ? now()->addDays(30) : null),
            ]);

            User::create([
                'tenant_id'  => $tenant->id,
                'name'       => $this->admin_name,
                'email'      => $this->admin_email,
                'password'   => Hash::make($this->admin_password),
                'is_active'  => true,
            ]);
        });

        session()->flash('success', "Le club « {$this->name} » a été créé avec son compte administrateur.");

        $this->redirect(route('superadmin.clubs.index'), navigate: true);
    }

    /** Plans disponibles pour la sélection */
    #[Computed]
    public function plans(): Collection
    {
        return Plan::where('is_active', true)->orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.super-admin.clubs.create', [
            'plans' => $this->plans,
        ]);
    }
}
