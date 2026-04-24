<?php
namespace App\Livewire\SuperAdmin\Admins;
use Livewire\Component;
class Accounts extends Component {
    public function render() {
        return view('livewire.superadmin.admins.accounts')
            ->layout('layouts.superadmin', ['pageTitle' => 'Comptes administrateurs']);
    }
}
