<?php
namespace App\Livewire\SuperAdmin\Settings;
use Livewire\Component;
class Legal extends Component {
    public function render() {
        return view('livewire.superadmin.settings.legal')
            ->layout('layouts.superadmin', ['pageTitle' => 'CGU / Mentions légales']);
    }
}
