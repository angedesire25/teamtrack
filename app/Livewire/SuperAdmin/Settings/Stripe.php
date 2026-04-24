<?php
namespace App\Livewire\SuperAdmin\Settings;
use Livewire\Component;
class Stripe extends Component {
    public function render() {
        return view('livewire.superadmin.settings.stripe')
            ->layout('layouts.superadmin', ['pageTitle' => 'Configuration Stripe']);
    }
}
