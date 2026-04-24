<?php
namespace App\Livewire\SuperAdmin\Admins;
use Livewire\Component;
class AuditLog extends Component {
    public function render() {
        return view('livewire.superadmin.admins.audit-log')
            ->layout('layouts.superadmin', ['pageTitle' => "Journal d'audit"]);
    }
}
