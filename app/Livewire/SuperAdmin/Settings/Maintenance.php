<?php
namespace App\Livewire\SuperAdmin\Settings;
use Livewire\Component;
class Maintenance extends Component {
    public function render() {
        return view('livewire.superadmin.settings.maintenance')
            ->layout('layouts.superadmin', ['pageTitle' => 'Maintenance']);
    }
}
