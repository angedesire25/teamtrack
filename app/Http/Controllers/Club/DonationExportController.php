<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationCampaign;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DonationExportController extends Controller
{
    public function csv(Request $request): Response
    {
        $donations = Donation::with(['donor','campaign'])
            ->where('status','completed')
            ->when($request->campaign_id, fn($q) => $q->where('campaign_id', $request->campaign_id))
            ->when($request->from, fn($q) => $q->where('created_at', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->where('created_at', '<=', $request->to.' 23:59:59'))
            ->orderByDesc('created_at')
            ->get();

        $rows = [];
        $rows[] = ['N° Reçu','Date','Donateur','Email','Montant','Devise','Type','Fréquence','Campagne','Stripe ID'];

        foreach ($donations as $d) {
            $rows[] = [
                $d->receipt_number,
                $d->created_at->format('d/m/Y H:i'),
                $d->is_anonymous ? 'Anonyme' : $d->donorName(),
                $d->donor?->email ?? '',
                number_format($d->amount, 2, '.', ''),
                strtoupper($d->currency),
                $d->frequencyLabel(),
                $d->frequency,
                $d->campaign?->title ?? '',
                $d->stripe_payment_intent_id ?? '',
            ];
        }

        $handle = fopen('php://temp','r+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        foreach ($rows as $row) { fputcsv($handle, $row, ';'); }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="dons-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function receiptPdf(int $donationId): Response
    {
        $donation = Donation::with(['donor','campaign'])->findOrFail($donationId);
        $tenant   = \App\Models\Tenant::find($donation->tenant_id);

        $pdf = Pdf::loadView('pdf.donation-receipt', compact('donation','tenant'))
            ->setPaper('a5');

        return $pdf->download('recu-' . $donation->receipt_number . '.pdf');
    }
}
