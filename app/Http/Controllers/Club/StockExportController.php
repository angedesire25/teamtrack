<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\EquipmentItem;
use App\Models\Jersey;
use App\Models\JerseyAssignment;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StockExportController extends Controller
{
    public function inventoryPdf(Request $request): Response
    {
        $tenant   = app()->has('tenant') ? app('tenant') : auth()->user()?->tenant;
        $jerseys  = Jersey::with('supplier')->orderBy('name')->orderBy('size')->get();
        $equipment= EquipmentItem::with('supplier')->orderBy('category')->orderBy('name')->get();

        $pdf = Pdf::loadView('pdf.stock-inventory', compact('tenant','jerseys','equipment'))
            ->setPaper('a4');

        return $pdf->download('inventaire-stock-' . now()->format('Y-m-d') . '.pdf');
    }

    public function inventoryCsv(): Response
    {
        $jerseys   = Jersey::with('supplier')->orderBy('name')->orderBy('size')->get();
        $equipment = EquipmentItem::with('supplier')->orderBy('category')->orderBy('name')->get();

        $rows = [];
        $rows[] = ['Type','Nom','Taille/Réf','Catégorie','Qté totale','Qté disponible','Seuil alerte','Prix unit.','Fournisseur','État'];

        foreach ($jerseys as $j) {
            $rows[] = ['Maillot',$j->name,$j->size,'',$j->quantity_total,$j->quantity_available,$j->low_stock_threshold,$j->unit_price,$j->supplier?->name ?? '',''];
        }
        foreach ($equipment as $e) {
            $rows[] = ['Matériel',$e->name,$e->reference ?? '',$e->category,$e->quantity_total,$e->quantity_available,$e->low_stock_threshold,$e->unit_price,$e->supplier?->name ?? '',$e->conditionLabel()];
        }

        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        foreach ($rows as $row) {
            fputcsv($handle, $row, ';');
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="inventaire-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function purchaseOrderPdf(Request $request): Response
    {
        $request->validate(['supplier_id' => 'required|exists:suppliers,id']);

        $tenant   = app()->has('tenant') ? app('tenant') : auth()->user()?->tenant;
        $supplier = Supplier::with(['jerseys','equipmentItems'])->findOrFail($request->supplier_id);

        $lowJerseys   = $supplier->jerseys->filter(fn($j) => $j->isLowStock());
        $lowEquipment = $supplier->equipmentItems->filter(fn($e) => $e->isLowStock());

        $pdf = Pdf::loadView('pdf.purchase-order', compact('tenant','supplier','lowJerseys','lowEquipment'))
            ->setPaper('a4');

        return $pdf->download('bon-commande-' . \Str::slug($supplier->name) . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
