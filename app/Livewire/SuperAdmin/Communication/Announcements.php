<?php
namespace App\Livewire\SuperAdmin\Communication;
use Livewire\Component;
class Announcements extends Component {
    public function render() {
        return view('livewire.superadmin.communication.announcements')
            ->layout('layouts.superadmin', ['pageTitle' => 'Annonces plateforme']);
    }
}
