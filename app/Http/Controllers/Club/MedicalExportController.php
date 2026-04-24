<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Injury;
use App\Models\MedicalCertificate;
use App\Models\MedicalClearance;
use App\Models\Player;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class MedicalExportController extends Controller
{
    public function weeklyReport(): Response
    {
        abort_unless(
            auth()->user()->hasAnyRole(['admin_club', 'staff_medical']),
            403
        );

        $tenantId = app('tenant')->id;

        $players = Player::where('players.tenant_id', $tenantId)
            ->where('status', 'active')
            ->with([
                'injuries'         => fn($q) => $q->whereIn('status', ['active', 'recovering'])->orderBy('start_date', 'desc'),
                'latestClearance',
                'medicalCertificates' => fn($q) => $q->whereNotNull('expires_at')
                    ->whereDate('expires_at', '>=', now())
                    ->whereDate('expires_at', '<=', now()->addDays(60))
                    ->orderBy('expires_at'),
            ])
            ->orderBy('last_name')
            ->get();

        $expiredCerts = MedicalCertificate::where('tenant_id', $tenantId)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', now())
            ->with('player')
            ->orderBy('expires_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.medical-weekly-report', [
            'tenant'       => app('tenant'),
            'players'      => $players,
            'expiredCerts' => $expiredCerts,
            'weekStart'    => now()->startOfWeek()->isoFormat('D MMMM YYYY'),
            'weekEnd'      => now()->endOfWeek()->isoFormat('D MMMM YYYY'),
            'generatedAt'  => now()->isoFormat('dddd D MMMM YYYY'),
        ])->setPaper('a4');

        $filename = 'rapport-medical-'
            .now()->startOfWeek()->format('Y-m-d')
            .'-'
            .now()->endOfWeek()->format('Y-m-d')
            .'.pdf';

        return $pdf->download($filename);
    }
}
