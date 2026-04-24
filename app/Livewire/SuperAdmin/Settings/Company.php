<?php
namespace App\Livewire\SuperAdmin\Settings;
use Livewire\Component;
class Company extends Component {
    public function render() {
        return view('livewire.superadmin.settings.company')
            ->layout('layouts.superadmin', ['pageTitle' => 'Informations société']);
    }
}
