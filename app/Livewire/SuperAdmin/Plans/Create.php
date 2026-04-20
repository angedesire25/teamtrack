<?php

namespace App\Livewire\SuperAdmin\Plans;

use App\Models\Plan;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Nouveau plan')]
class Create extends Component
{
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|integer|min:0')]
    public string $price = '';

    #[Validate('required|in:monthly,yearly')]
    public string $billing_cycle = 'monthly';

    #[Validate('required|integer|min:1')]
    public string $max_players = '100';

    #[Validate('required|integer|min:1')]
    public string $max_users = '5';

    /** Fonctionnalités saisies une par ligne */
    public string $features_raw = '';

    public bool $is_active = true;

    public function save(): void
    {
        $this->validate();

        $features = array_values(array_filter(
            array_map('trim', explode("\n", $this->features_raw))
        ));

        Plan::create([
            'name'          => $this->name,
            'slug'          => Str::slug($this->name),
            'price'         => (int) $this->price,
            'billing_cycle' => $this->billing_cycle,
            'max_players'   => (int) $this->max_players,
            'max_users'     => (int) $this->max_users,
            'features'      => $features ?: null,
            'is_active'     => $this->is_active,
        ]);

        session()->flash('success', "Le plan « {$this->name} » a été créé.");

        $this->redirect(route('superadmin.plans.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.super-admin.plans.create');
    }
}
