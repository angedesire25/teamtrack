<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    @font-face { font-family: 'DejaVu Sans'; src: url('{{ storage_path("fonts/DejaVuSans.ttf") }}') format('truetype'); font-weight: normal; }
    @font-face { font-family: 'DejaVu Sans'; src: url('{{ storage_path("fonts/DejaVuSans-Bold.ttf") }}') format('truetype'); font-weight: bold; }
    * { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 0; box-sizing: border-box; font-size: 10px; }
    body { padding: 20px; color: #1f2937; background: #fff; }
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 2px solid #1E3A5F; padding-bottom: 12px; }
    .title { font-size: 16px; font-weight: bold; color: #1E3A5F; }
    .meta { color: #6b7280; font-size: 9px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #1E3A5F; color: white; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
    td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    tr:nth-child(even) td { background: #f9fafb; }
    .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; }
    .badge-gray    { background: #f3f4f6; color: #374151; }
    .badge-blue    { background: #dbeafe; color: #1d4ed8; }
    .badge-amber   { background: #fef3c7; color: #92400e; }
    .badge-emerald { background: #d1fae5; color: #065f46; }
    .badge-green   { background: #bbf7d0; color: #14532d; }
    .badge-red     { background: #fee2e2; color: #991b1b; }
    .badge-orange  { background: #ffedd5; color: #c2410c; }
    .dir-out { color: #ea580c; font-weight: bold; }
    .dir-in  { color: #2563eb; font-weight: bold; }
    .footer { margin-top: 20px; text-align: center; color: #9ca3af; font-size: 8px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
</style>
</head>
<body>
<div class="header">
    <div>
        <div class="title">Registre des Transferts</div>
        <div class="meta">{{ $tenant->name }} — Édité le {{ now()->translatedFormat('d F Y') }}</div>
    </div>
    <div class="meta" style="text-align:right;">
        {{ $transfers->count() }} transfert(s) au total<br>
        {{ $transfers->where('status','finalized')->count() }} finalisé(s)
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Joueur</th>
            <th>Direction</th>
            <th>Type</th>
            <th>Club</th>
            <th>Statut</th>
            <th>Prix demandé</th>
            <th>Accord</th>
            <th>Négos</th>
            <th>Maj le</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transfers as $t)
        <tr>
            <td>
                <strong>{{ $t->playerDisplayName() }}</strong>
                @if($t->player?->position)
                <br><span style="color:#6b7280;">{{ $t->player->position }}</span>
                @endif
            </td>
            <td class="{{ $t->direction === 'outgoing' ? 'dir-out' : 'dir-in' }}">
                {{ $t->directionLabel() }}
            </td>
            <td>{{ $t->typeLabel() }}</td>
            <td>{{ $t->counterpart_club ?? '—' }}</td>
            <td>
                <span class="badge badge-{{ $t->statusColor() }}">{{ $t->statusLabel() }}</span>
            </td>
            <td>{{ $t->asking_price ? number_format($t->asking_price, 0, '.', ' ').' F' : '—' }}</td>
            <td>{{ $t->agreed_fee ? number_format($t->agreed_fee, 0, '.', ' ').' F' : '—' }}</td>
            <td style="text-align:center;">{{ $t->negotiations_count }}</td>
            <td>{{ $t->updated_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:20px;color:#6b7280;">Aucun transfert enregistré</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">TeamTrack — Registre généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</div>
</body>
</html>
