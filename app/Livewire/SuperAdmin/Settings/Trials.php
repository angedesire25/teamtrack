<?php
namespace App\Livewire\SuperAdmin\Settings;
use Livewire\Component;
class Trials extends Component {
    public function render() {
        return view('livewire.superadmin.settings.trials')
            ->layout('layouts.superadmin', ['pageTitle' => "Périodes d'essai"]);
    }
}
