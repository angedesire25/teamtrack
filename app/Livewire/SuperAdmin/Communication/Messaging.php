<?php
namespace App\Livewire\SuperAdmin\Communication;
use Livewire\Component;
class Messaging extends Component {
    public function render() {
        return view('livewire.superadmin.communication.messaging')
            ->layout('layouts.superadmin', ['pageTitle' => 'Messagerie clubs']);
    }
}
