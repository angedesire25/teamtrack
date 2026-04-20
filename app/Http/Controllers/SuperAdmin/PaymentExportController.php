<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;

/** Exporte les paiements au format CSV */
class PaymentExportController extends Controller
{
    public function __invoke()
    {
        $payments = Payment::with('tenant')
            ->orderByDesc('created_at')
            ->get();

        $filename = 'paiements_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Référence', 'Club', 'Montant (FCFA)', 'Statut', 'Méthode', 'Date paiement', 'Note'], ';');

            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->reference,
                    $p->tenant->name ?? '—',
                    $p->amount,
                    match ($p->status) {
                        'paid'    => 'Payé',
                        'pending' => 'En attente',
                        'failed'  => 'Échoué',
                        default   => $p->status,
                    },
                    $p->method ?? '—',
                    $p->paid_at?->format('d/m/Y') ?? '—',
                    $p->note ?? '',
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
