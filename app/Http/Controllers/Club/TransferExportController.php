<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class TransferExportController extends Controller
{
    public function registerPdf(): Response
    {
        $tenantId = app('tenant')->id;
        $tenant   = app('tenant');

        $transfers = Transfer::where('tenant_id', $tenantId)
            ->with(['player', 'negotiations'])
            ->withCount('negotiations')
            ->orderBy('direction')
            ->orderByDesc('updated_at')
            ->get();

        $pdf = Pdf::loadView('pdf.transfer-register', compact('transfers', 'tenant'))
            ->setPaper('a4', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="registre-transferts.pdf"',
        ]);
    }

    public function filePdf(int $transfer): Response
    {
        $tenantId = app('tenant')->id;
        $t = Transfer::with(['player.team', 'negotiations'])->findOrFail($transfer);
        abort_unless($t->tenant_id === $tenantId, 403);

        $tenant = app('tenant');

        $pdf = Pdf::loadView('pdf.transfer-file', compact('t', 'tenant'))
            ->setPaper('a4', 'portrait');

        $filename = 'dossier-transfert-' . str($t->playerDisplayName())->slug() . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
