<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    @font-face { font-family: 'DejaVu Sans'; src: url('{{ storage_path("fonts/DejaVuSans.ttf") }}') format('truetype'); font-weight: normal; }
    @font-face { font-family: 'DejaVu Sans'; src: url('{{ storage_path("fonts/DejaVuSans-Bold.ttf") }}') format('truetype'); font-weight: bold; }
    * { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 0; box-sizing: border-box; font-size: 10px; color: #1f2937; }
    body { padding: 28px; background: #fff; }
    .header { border-bottom: 3px solid #1E3A5F; padding-bottom: 14px; margin-bottom: 20px; }
    .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
    .doc-title { font-size: 20px; font-weight: bold; color: #1E3A5F; }
    .doc-sub { font-size: 10px; color: #6b7280; margin-top: 2px; }
    .club-name { font-size: 12px; font-weight: bold; color: #1E3A5F; text-align: right; }
    .section { margin-bottom: 18px; }
    .section-title { font-size: 11px; font-weight: bold; color: #1E3A5F; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    .grid2 { display: flex; gap: 16px; }
    .grid2 > div { flex: 1; }
    .field-label { font-size: 8px; color: #6b7280; text-transform: uppercase; margin-bottom: 2px; }
    .field-value { font-size: 11px; font-weight: bold; }
    .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; }
    .badge-gray    { background: #f3f4f6; color: #374151; }
    .badge-blue    { background: #dbeafe; color: #1d4ed8; }
    .badge-amber   { background: #fef3c7; color: #92400e; }
    .badge-emerald { background: #d1fae5; color: #065f46; }
    .badge-green   { background: #bbf7d0; color: #14532d; }
    .badge-red     { background: #fee2e2; color: #991b1b; }
    .badge-orange  { background: #ffedd5; color: #c2410c; }
    .fee-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; padding: 12px 16px; margin-top: 8px; }
    .fee-label { font-size: 9px; color: #0284c7; }
    .fee-value { font-size: 22px; font-weight: bold; color: #0c4a6e; }
    .timeline-item { display: flex; gap: 10px; margin-bottom: 10px; }
    .timeline-dot { width: 8px; height: 8px; border-radius: 50%; background: #1E3A5F; margin-top: 2px; flex-shrink: 0; }
    .timeline-date { font-size: 8px; color: #6b7280; margin-bottom: 2px; }
    .timeline-note { font-size: 9px; }
    .clauses-grid { display: flex; flex-wrap: wrap; gap: 6px; }
    .clause-tag { background: #ede9fe; color: #5b21b6; padding: 3px 8px; border-radius: 4px; font-size: 9px; }
    .sig-row { display: flex; gap: 20px; margin-top: 30px; }
    .sig-box { flex: 1; border-top: 1px solid #374151; padding-top: 6px; }
    .sig-label { font-size: 9px; color: #6b7280; }
    .notes-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px 10px; font-size: 9px; color: #374151; white-space: pre-wrap; }
    .footer { margin-top: 28px; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
</style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div>
            <div class="doc-title">Dossier de Transfert</div>
            <div class="doc-sub">Référence #{{ $t->id }} — {{ $t->created_at->format('d/m/Y') }}</div>
        </div>
        <div>
            <div class="club-name">{{ $tenant->name }}</div>
            <div style="text-align:right;margin-top:4px;">
                <span class="badge badge-{{ $t->direction === 'outgoing' ? 'orange' : 'blue' }}">{{ $t->directionLabel() }}</span>
                &nbsp;
                <span class="badge badge-{{ $t->statusColor() }}">{{ $t->statusLabel() }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Joueur --}}
<div class="section">
    <div class="section-title">Joueur concerné</div>
    <div class="grid2">
        <div>
            <div class="field-label">Nom complet</div>
            <div class="field-value" style="font-size:14px;">{{ $t->playerDisplayName() }}</div>
        </div>
        @if($t->player)
        <div>
            <div class="field-label">Poste</div>
            <div class="field-value">{{ $t->player->position ?? '—' }}</div>
        </div>
        <div>
            <div class="field-label">Âge</div>
            <div class="field-value">{{ $t->player->age() ? $t->player->age().' ans' : '—' }}</div>
        </div>
        <div>
            <div class="field-label">Équipe</div>
            <div class="field-value">{{ $t->player->team?->name ?? '—' }}</div>
        </div>
        @endif
        @if($t->direction === 'incoming' && $t->search_position)
        <div>
            <div class="field-label">Poste recherché</div>
            <div class="field-value">{{ $t->search_position }}</div>
        </div>
        @if($t->search_age_min || $t->search_age_max)
        <div>
            <div class="field-label">Âge cible</div>
            <div class="field-value">{{ $t->search_age_min ?? '?' }} – {{ $t->search_age_max ?? '?' }} ans</div>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- Conditions --}}
<div class="section">
    <div class="section-title">Conditions du transfert</div>
    <div class="grid2">
        <div>
            <div class="field-label">Type</div>
            <div class="field-value">{{ $t->typeLabel() }}</div>
        </div>
        <div>
            <div class="field-label">Club {{ $t->direction === 'outgoing' ? 'acheteur' : 'vendeur' }}</div>
            <div class="field-value">{{ $t->counterpart_club ?? 'Non défini' }}</div>
        </div>
        @if($t->counterpart_contact)
        <div>
            <div class="field-label">Contact</div>
            <div class="field-value">{{ $t->counterpart_contact }}</div>
        </div>
        @endif
        @if($t->type === 'loan')
        <div>
            <div class="field-label">Durée du prêt</div>
            <div class="field-value">{{ $t->loan_duration_months ? $t->loan_duration_months.' mois' : '—' }}</div>
        </div>
        @if($t->loan_start_date)
        <div>
            <div class="field-label">Période</div>
            <div class="field-value">{{ $t->loan_start_date->format('d/m/Y') }} → {{ $t->loan_end_date?->format('d/m/Y') ?? '—' }}</div>
        </div>
        @endif
        @endif
    </div>

    @if($t->asking_price || $t->agreed_fee)
    <div class="grid2" style="margin-top:10px;">
        @if($t->asking_price)
        <div class="fee-box">
            <div class="fee-label">Prix demandé</div>
            <div class="fee-value">{{ number_format($t->asking_price, 0, '.', ' ') }} F</div>
        </div>
        @endif
        @if($t->agreed_fee)
        <div class="fee-box" style="background:#f0fdf4;border-color:#86efac;">
            <div class="fee-label" style="color:#15803d;">Montant accordé</div>
            <div class="fee-value" style="color:#14532d;">{{ number_format($t->agreed_fee, 0, '.', ' ') }} F</div>
        </div>
        @endif
    </div>
    @elseif($t->search_budget_max)
    <div class="fee-box" style="margin-top:10px;">
        <div class="fee-label">Budget maximum</div>
        <div class="fee-value">{{ number_format($t->search_budget_max, 0, '.', ' ') }} F</div>
    </div>
    @endif
</div>

{{-- Clauses --}}
@if(!empty($t->clauses))
<div class="section">
    <div class="section-title">Clauses contractuelles</div>
    <div class="clauses-grid">
        @foreach($t->clauses as $key => $active)
        @if($active)
        <span class="clause-tag">{{ str_replace('_', ' ', ucfirst($key)) }}</span>
        @endif
        @endforeach
    </div>
</div>
@endif

{{-- Notes --}}
@if($t->notes)
<div class="section">
    <div class="section-title">Notes</div>
    <div class="notes-box">{{ $t->notes }}</div>
</div>
@endif

{{-- Historique négociations --}}
@if($t->negotiations->isNotEmpty())
<div class="section">
    <div class="section-title">Historique des négociations ({{ $t->negotiations->count() }})</div>
    @foreach($t->negotiations as $neg)
    <div class="timeline-item">
        <div class="timeline-dot"></div>
        <div>
            <div class="timeline-date">{{ $neg->date->format('d/m/Y') }}
                @if($neg->amount_proposed) · <strong>{{ number_format($neg->amount_proposed, 0, '.', ' ') }} F proposés</strong> @endif
                @if($neg->status_after) · Statut → <strong>{{ (new \App\Models\Transfer(['status' => $neg->status_after]))->statusLabel() }}</strong> @endif
            </div>
            <div class="timeline-note">{{ $neg->note }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Signatures --}}
<div class="sig-row">
    <div class="sig-box">
        <div class="sig-label">Responsable club — {{ $tenant->name }}</div>
        <div style="height:30px;"></div>
        <div class="sig-label">Signature et date</div>
    </div>
    <div class="sig-box">
        <div class="sig-label">Club {{ $t->direction === 'outgoing' ? 'acheteur' : 'vendeur' }} — {{ $t->counterpart_club ?? '___________' }}</div>
        <div style="height:30px;"></div>
        <div class="sig-label">Signature et date</div>
    </div>
</div>

<div class="footer">TeamTrack — Document généré le {{ now()->format('d/m/Y à H:i') }} — Confidentiel</div>
</body>
</html>
