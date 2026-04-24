<?php
namespace App\Livewire\SuperAdmin\Settings;
use Livewire\Component;
class Smtp extends Component {
    public function render() {
        return view('livewire.superadmin.settings.smtp')
            ->layout('layouts.superadmin', ['pageTitle' => 'Configuration SMTP']);
    }
}
