<?php
namespace App\Livewire\SuperAdmin\Communication;
use Livewire\Component;
class Templates extends Component {
    public function render() {
        return view('livewire.superadmin.communication.templates')
            ->layout('layouts.superadmin', ['pageTitle' => 'Templates e-mails']);
    }
}
