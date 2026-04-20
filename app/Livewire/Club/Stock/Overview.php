<?php

namespace App\Livewire\Club\Stock;

use App\Models\EquipmentItem;
use App\Models\EquipmentMovement;
use App\Models\Jersey;
use App\Models\JerseyAssignment;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Overview extends Component
{
    public function render()
    {
        $lowJerseys = Jersey::where('quantity_available', '<=', \DB::raw('low_stock_threshold'))->count();
        $lowEquipment = EquipmentItem::where('quantity_available', '<=', \DB::raw('low_stock_threshold'))->count();
        $outOfService = EquipmentItem::where('condition', 'out_of_service')->count();
        $toRepair     = EquipmentItem::where('condition', 'repair')->count();
        $activeAssignments = JerseyAssignment::whereNull('returned_at')->count();
        $overdueMovements  = EquipmentMovement::where('type', 'out')
            ->whereNull('returned_at')
            ->whereNotNull('expected_return_at')
            ->where('expected_return_at', '<', now())
            ->count();

        $alertJerseys   = Jersey::where('quantity_available', '<=', \DB::raw('low_stock_threshold'))->with('supplier')->get();
        $alertEquipment = EquipmentItem::where('quantity_available', '<=', \DB::raw('low_stock_threshold'))->get();

        return view('livewire.club.stock.overview', compact(
            'lowJerseys', 'lowEquipment', 'outOfService', 'toRepair',
            'activeAssignments', 'overdueMovements', 'alertJerseys', 'alertEquipment'
        ));
    }
}
