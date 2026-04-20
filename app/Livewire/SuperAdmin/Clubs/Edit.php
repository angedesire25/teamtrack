<?php

namespace App\Livewire\SuperAdmin\Clubs;

use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Éditer un club')]
class Edit extends Component
{
    public Tenant $tenant;

    // --- Champs du formulaire ---
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|string|max:50')]
    public string $subdomain = '';

    #[Validate('required|email|max:150')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|string|max:50')]
    public string $city = '';

    #[Validate('required|string|max:10')]
    public string $country = 'CI';

    #[Validate('required|exists:plans,id')]
    public string $plan_id = '';

    #[Validate('required|in:active,trial,suspended,cancelled')]
    public string $status = 'trial';

    #[Validate('nullable|date')]
    public string $trial_ends_at = '';

    /** Charge les données du club dans le formulaire au démarrage */
    public function mount(Tenant $tenant): void
    {
        $this->tenant       = $tenant;
        $this->name         = $tenant->name;
        $this->subdomain    = $tenant->subdomain;
        $this->email        = $tenant->email;
        $this->phone        = $tenant->phone ?? '';
        $this->city         = $tenant->city ?? '';
        $this->country      = $tenant->country;
        $this->plan_id      = (string) $tenant->plan_id;
        $this->status       = $tenant->status;
        $this->trial_ends_at = $tenant->trial_ends_at?->format('Y-m-d') ?? '';
    }

    /** Enregistre les modifications */
    public function save(): void
    {
        $this->validate([
            'name'          => 'required|string|max:100',
            'subdomain'     => "required|string|max:50|unique:tenants,subdomain,{$this->tenant->id}",
            'email'         => "required|email|max:150|unique:tenants,email,{$this->tenant->id}",
            'phone'         => 'nullable|string|max:20',
            'city'          => 'nullable|string|max:50',
            'country'       => 'required|string|max:10',
            'plan_id'       => 'required|exists:plans,id',
            'status'        => 'required|in:active,trial,suspended,cancelled',
            'trial_ends_at' => 'nullable|date',
        ]);

        $this->tenant->update([
            'name'          => $this->name,
            'slug'          => Str::slug($this->name),
            'subdomain'     => $this->subdomain,
            'email'         => $this->email,
            'phone'         => $this->phone ?: null,
            'city'          => $this->city ?: null,
            'country'       => $this->country,
            'plan_id'       => $this->plan_id,
            'status'        => $this->status,
            'trial_ends_at' => $this->trial_ends_at ?: null,
            'suspended_at'  => $this->status === 'suspended' ? ($this->tenant->suspended_at ?? now()) : null,
        ]);

        session()->flash('success', "Le club « {$this->name} » a été mis à jour.");

        $this->redirect(route('superadmin.clubs.show', $this->tenant), navigate: true);
    }

    /** Plans disponibles */
    #[Computed]
    public function plans(): Collection
    {
        return Plan::orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.super-admin.clubs.edit', [
            'plans' => $this->plans,
        ]);
    }
}
