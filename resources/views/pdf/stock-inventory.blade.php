<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
    .header { background: #1E3A5F; color: white; padding: 20px 24px; margin-bottom: 24px; }
    .header h1 { font-size: 18px; font-weight: bold; }
    .header p  { font-size: 11px; opacity: .8; margin-top: 4px; }
    .section-title { font-size: 13px; font-weight: bold; color: #1E3A5F; border-bottom: 2px solid #1E3A5F; padding-bottom: 6px; margin: 20px 24px 10px; }
    table { width: calc(100% - 48px); margin: 0 24px; border-collapse: collapse; }
    th { background: #f3f4f6; padding: 7px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
    td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    tr:nth-child(even) td { background: #fafafa; }
    .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
    .alert { background: #fef2f2; color: #dc2626; }
    .ok    { background: #f0fdf4; color: #16a34a; }
    .footer { margin: 24px; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
</style>
</head>
<body>
<div class="header">
    <h1>Inventaire du stock — {{ $tenant?->name ?? 'Club' }}</h1>
    <p>Généré le {{ now()->translatedFormat('d F Y à H:i') }}</p>
</div>

<div class="section-title">Maillots ({{ $jerseys->count() }} références)</div>
<table>
    <thead>
        <tr>
            <th>Nom</th><th>Type</th><th>Saison</th><th>Taille</th>
            <th>Qté totale</th><th>Disponible</th><th>Seuil</th><th>Prix unit.</th><th>Fournisseur</th><th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jerseys as $j)
        <tr>
            <td>{{ $j->name }}</td>
            <td>{{ $j->typeLabel() }}</td>
            <td>{{ $j->season ?? '—' }}</td>
            <td><strong>{{ $j->size }}</strong></td>
            <td>{{ $j->quantity_total }}</td>
            <td>{{ $j->quantity_available }}</td>
            <td>{{ $j->low_stock_threshold }}</td>
            <td>{{ $j->unit_price ? number_format($j->unit_price,0,'.',' ').' F' : '—' }}</td>
            <td>{{ $j->supplier?->name ?? '—' }}</td>
            <td><span class="badge {{ $j->isLowStock() ? 'alert' : 'ok' }}">{{ $j->isLowStock() ? 'Alerte' : 'OK' }}</span></td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;color:#9ca3af;padding:16px;">Aucun maillot</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">Matériel ({{ $equipment->count() }} articles)</div>
<table>
    <thead>
        <tr>
            <th>Nom</th><th>Catégorie</th><th>État</th>
            <th>Qté totale</th><th>Disponible</th><th>Seuil</th><th>Prix unit.</th><th>Fournisseur</th><th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @forelse($equipment as $e)
        <tr>
            <td>{{ $e->name }}</td>
            <td>{{ $e->category }}</td>
            <td>{{ $e->conditionLabel() }}</td>
            <td>{{ $e->quantity_total }}</td>
            <td>{{ $e->quantity_available }}</td>
            <td>{{ $e->low_stock_threshold }}</td>
            <td>{{ $e->unit_price ? number_format($e->unit_price,0,'.',' ').' F' : '—' }}</td>
            <td>{{ $e->supplier?->name ?? '—' }}</td>
            <td><span class="badge {{ $e->isLowStock() ? 'alert' : 'ok' }}">{{ $e->isLowStock() ? 'Alerte' : 'OK' }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:16px;">Aucun article</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Document généré par TeamTrack — {{ config('app.name') }} · {{ now()->format('d/m/Y') }}
</div>
</body>
</html>
