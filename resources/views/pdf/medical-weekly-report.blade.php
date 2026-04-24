<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1f2937; line-height: 1.4; }
    .page { padding: 20mm 18mm; }

    /* En-tête */
    .header { border-bottom: 2px solid #1E3A5F; padding-bottom: 10px; margin-bottom: 16px; }
    .header-title { font-size: 16pt; font-weight: bold; color: #1E3A5F; }
    .header-sub { font-size: 9pt; color: #6b7280; margin-top: 2px; }
    .header-club { font-size: 10pt; font-weight: bold; color: #374151; margin-top: 4px; }

    /* Section */
    .section-title { font-size: 11pt; font-weight: bold; color: #1E3A5F; background: #EFF6FF;
                     padding: 5px 8px; margin-bottom: 8px; margin-top: 14px; border-left: 4px solid #1E3A5F; }

    /* KPIs */
    .kpi-row { display: table; width: 100%; margin-bottom: 14px; }
    .kpi-cell { display: table-cell; width: 25%; padding: 8px 6px; }
    .kpi-box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; text-align: center; }
    .kpi-val { font-size: 18pt; font-weight: bold; }
    .kpi-lbl { font-size: 7.5pt; color: #6b7280; margin-top: 2px; }
    .kpi-red  { color: #dc2626; }
    .kpi-green { color: #059669; }
    .kpi-amber { color: #d97706; }

    /* Table */
    table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    th { background: #1E3A5F; color: white; padding: 5px 7px; text-align: left; font-size: 8pt; }
    td { padding: 5px 7px; border-bottom: 1px solid #f3f4f6; font-size: 8pt; vertical-align: top; }
    tr:nth-child(even) td { background: #f9fafb; }

    /* Badges */
    .badge { display: inline-block; padding: 1px 6px; border-radius: 99px; font-size: 7pt; font-weight: bold; }
    .badge-red    { background: #fee2e2; color: #dc2626; }
    .badge-green  { background: #d1fae5; color: #059669; }
    .badge-amber  { background: #fef3c7; color: #d97706; }
    .badge-gray   { background: #f3f4f6; color: #6b7280; }

    .footer { margin-top: 20px; padding-top: 8px; border-top: 1px solid #e5e7eb;
              text-align: center; font-size: 7.5pt; color: #9ca3af; }
    .page-break { page-break-before: always; }
    .no-data { color: #9ca3af; font-style: italic; padding: 10px 0; text-align: center; }
</style>
</head>
<body>
<div class="page">

    {{-- En-tête --}}
    <div class="header">
        <div class="header-title">Rapport Médical Hebdomadaire</div>
        <div class="header-club">{{ $tenant->name }}</div>
        <div class="header-sub">Semaine du {{ $weekStart }} au {{ $weekEnd }} · Généré le {{ $generatedAt }}</div>
    </div>

    {{-- KPIs --}}
    @php
        $activeInjCount   = $players->sum(fn($p) => $p->injuries->count());
        $fitCount         = $players->filter(fn($p) => $p->latestClearance?->status === 'fit')->count();
        $unfitCount       = $players->filter(fn($p) => $p->latestClearance?->status === 'unfit')->count();
        $conditionalCount = $players->filter(fn($p) => $p->latestClearance?->status === 'conditional')->count();
        $noClearanceCount = $players->filter(fn($p) => $p->latestClearance === null)->count();
        $expiringCount    = $players->sum(fn($p) => $p->medicalCertificates->count());
    @endphp
    <div class="kpi-row">
        <div class="kpi-cell">
            <div class="kpi-box">
                <div class="kpi-val kpi-red">{{ $activeInjCount }}</div>
                <div class="kpi-lbl">Blessures actives</div>
            </div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-box">
                <div class="kpi-val kpi-green">{{ $fitCount }}</div>
                <div class="kpi-lbl">Joueurs aptes</div>
            </div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-box">
                <div class="kpi-val kpi-amber">{{ $unfitCount + $conditionalCount }}</div>
                <div class="kpi-lbl">Inaptes / sous réserve</div>
            </div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-box">
                <div class="kpi-val kpi-amber">{{ $expiringCount }}</div>
                <div class="kpi-lbl">Certificats expirant ≤ 60j</div>
            </div>
        </div>
    </div>

    {{-- Joueurs blessés --}}
    <div class="section-title">Blessures actives et en rééducation</div>
    @php
        $injuredPlayers = $players->filter(fn($p) => $p->injuries->isNotEmpty());
    @endphp
    @if($injuredPlayers->isEmpty())
        <p class="no-data">Aucune blessure active cette semaine.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Joueur</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Début</th>
                    <th>Retour estimé</th>
                    <th>Traitement</th>
                </tr>
            </thead>
            <tbody>
                @foreach($injuredPlayers as $player)
                    @foreach($player->injuries as $inj)
                        <tr>
                            <td>{{ $player->last_name }} {{ $player->first_name }}</td>
                            <td>{{ $inj->typeLabel() }}</td>
                            <td>
                                <span class="badge {{ $inj->status === 'active' ? 'badge-red' : 'badge-amber' }}">
                                    {{ $inj->statusLabel() }}
                                </span>
                            </td>
                            <td>{{ $inj->start_date->isoFormat('D/MM/YYYY') }}</td>
                            <td>{{ $inj->estimated_return_date?->isoFormat('D/MM/YYYY') ?? '—' }}</td>
                            <td>{{ $inj->treatment ? \Illuminate\Support\Str::limit($inj->treatment, 60) : '—' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Aptitude effectif --}}
    <div class="section-title">Statut d'aptitude de l'effectif</div>
    <table>
        <thead>
            <tr>
                <th>Joueur</th>
                <th>Poste</th>
                <th>Aptitude</th>
                <th>Depuis le</th>
                <th>Révision</th>
                <th>Motif</th>
            </tr>
        </thead>
        <tbody>
            @foreach($players as $player)
                @php $cl = $player->latestClearance; @endphp
                <tr>
                    <td>{{ $player->last_name }} {{ $player->first_name }}</td>
                    <td>{{ $player->position ?? '—' }}</td>
                    <td>
                        @if($cl)
                            <span class="badge {{ $cl->status === 'fit' ? 'badge-green' : ($cl->status === 'unfit' ? 'badge-red' : 'badge-amber') }}">
                                {{ $cl->statusLabel() }}
                            </span>
                        @else
                            <span class="badge badge-gray">Non évalué</span>
                        @endif
                    </td>
                    <td>{{ $cl?->effective_date->isoFormat('D/MM/YYYY') ?? '—' }}</td>
                    <td>{{ $cl?->review_date?->isoFormat('D/MM/YYYY') ?? '—' }}</td>
                    <td>{{ $cl?->reason ? \Illuminate\Support\Str::limit($cl->reason, 50) : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Certificats expirant bientôt --}}
    @if($expiringCount > 0)
    <div class="section-title">Certificats médicaux à renouveler (≤ 60 jours)</div>
    <table>
        <thead>
            <tr>
                <th>Joueur</th>
                <th>Type</th>
                <th>Date d'émission</th>
                <th>Date d'expiration</th>
                <th>Jours restants</th>
            </tr>
        </thead>
        <tbody>
            @foreach($players as $player)
                @foreach($player->medicalCertificates as $cert)
                    <tr>
                        <td>{{ $player->last_name }} {{ $player->first_name }}</td>
                        <td>{{ $cert->typeLabel() }}</td>
                        <td>{{ $cert->issued_at->isoFormat('D/MM/YYYY') }}</td>
                        <td>{{ $cert->expires_at->isoFormat('D/MM/YYYY') }}</td>
                        <td>
                            <span class="badge {{ $cert->expires_at->diffInDays(now()) <= 7 ? 'badge-red' : 'badge-amber' }}">
                                {{ $cert->expires_at->diffInDays(now()) }}j
                            </span>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Certificats expirés --}}
    @if($expiredCerts->isNotEmpty())
    <div class="section-title">Certificats expirés (à régulariser)</div>
    <table>
        <thead>
            <tr>
                <th>Joueur</th>
                <th>Type</th>
                <th>Expiré le</th>
                <th>Depuis (jours)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expiredCerts as $cert)
                <tr>
                    <td>{{ $cert->player->last_name }} {{ $cert->player->first_name }}</td>
                    <td>{{ $cert->typeLabel() }}</td>
                    <td>{{ $cert->expires_at->isoFormat('D/MM/YYYY') }}</td>
                    <td>
                        <span class="badge badge-red">{{ now()->diffInDays($cert->expires_at) }}j</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Document confidentiel — Usage interne · {{ $tenant->name }} · TeamTrack
    </div>
</div>
</body>
</html>
