<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
    .header { background: #1E3A5F; color: white; padding: 20px 24px; margin-bottom: 0; }
    .header h1 { font-size: 18px; font-weight: bold; }
    .header p  { font-size: 11px; opacity: .8; margin-top: 4px; }
    .meta { display: flex; gap: 0; margin: 0 0 20px; }
    .meta-box { flex: 1; padding: 16px 24px; border-bottom: 3px solid #e5e7eb; }
    .meta-box h3 { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; margin-bottom: 6px; }
    .meta-box p  { font-size: 11px; color: #111827; line-height: 1.5; }
    .section-title { font-size: 12px; font-weight: bold; color: #1E3A5F; background: #f0f4ff; padding: 8px 24px; margin-bottom: 0; }
    table { width: calc(100% - 48px); margin: 0 24px 20px; border-collapse: collapse; }
    th { background: #f3f4f6; padding: 7px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
    td { padding: 7px 8px; border-bottom: 1px solid #f3f4f6; }
    .total-row td { font-weight: bold; background: #f0f4ff; border-top: 2px solid #1E3A5F; }
    .footer { margin: 24px; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    .signature { margin: 40px 24px 0; display: flex; gap: 60px; }
    .sig-box { flex: 1; border-top: 1px solid #6b7280; padding-top: 8px; font-size: 9px; color: #6b7280; }
</style>
</head>
<body>
<div class="header">
    <h1>Bon de commande</h1>
    <p>{{ $tenant?->name ?? 'Club' }} · Réf. BC-{{ now()->format('Ymd-His') }}</p>
</div>

<div class="meta">
    <div class="meta-box">
        <h3>Émetteur</h3>
        <p><strong>{{ $tenant?->name ?? 'Club' }}</strong></p>
        @if($tenant?->city) <p>{{ $tenant->city }}</p> @endif
    </div>
    <div class="meta-box">
        <h3>Fournisseur</h3>
        <p><strong>{{ $supplier->name }}</strong></p>
        @if($supplier->contact_name) <p>{{ $supplier->contact_name }}</p> @endif
        @if($supplier->address) <p>{{ $supplier->address }}</p> @endif
        @if($supplier->email) <p>{{ $supplier->email }}</p> @endif
        @if($supplier->phone) <p>{{ $supplier->phone }}</p> @endif
    </div>
    <div class="meta-box">
        <h3>Date</h3>
        <p>{{ now()->translatedFormat('d F Y') }}</p>
    </div>
</div>

@if($lowJerseys->isNotEmpty())
<div class="section-title">Maillots à commander</div>
<table>
    <thead>
        <tr>
            <th>Désignation</th><th>Type</th><th>Taille</th><th>Saison</th>
            <th>Stock actuel</th><th>Seuil</th><th>Qté suggérée</th><th>Prix unit.</th><th>Total estimé</th>
        </tr>
    </thead>
    <tbody>
        @php $jerseyTotal = 0; @endphp
        @foreach($lowJerseys as $j)
        @php
            $suggested = max(1, $j->low_stock_threshold * 2 - $j->quantity_available);
            $lineTotal  = $j->unit_price ? $suggested * $j->unit_price : null;
            $jerseyTotal += $lineTotal ?? 0;
        @endphp
        <tr>
            <td>{{ $j->name }}</td>
            <td>{{ $j->typeLabel() }}</td>
            <td><strong>{{ $j->size }}</strong></td>
            <td>{{ $j->season ?? '—' }}</td>
            <td>{{ $j->quantity_available }}</td>
            <td>{{ $j->low_stock_threshold }}</td>
            <td><strong>{{ $suggested }}</strong></td>
            <td>{{ $j->unit_price ? number_format($j->unit_price,0,'.',' ').' F' : '—' }}</td>
            <td>{{ $lineTotal ? number_format($lineTotal,0,'.',' ').' F' : '—' }}</td>
        </tr>
        @endforeach
        @if($jerseyTotal > 0)
        <tr class="total-row">
            <td colspan="8" style="text-align:right;">Sous-total maillots</td>
            <td>{{ number_format($jerseyTotal,0,'.',' ') }} F</td>
        </tr>
        @endif
    </tbody>
</table>
@endif

@if($lowEquipment->isNotEmpty())
<div class="section-title">Matériel à commander</div>
<table>
    <thead>
        <tr>
            <th>Désignation</th><th>Catégorie</th><th>Réf. fournisseur</th>
            <th>Stock actuel</th><th>Seuil</th><th>Qté suggérée</th><th>Prix unit.</th><th>Total estimé</th>
        </tr>
    </thead>
    <tbody>
        @php $equipTotal = 0; @endphp
        @foreach($lowEquipment as $e)
        @php
            $suggested = max(1, $e->low_stock_threshold * 2 - $e->quantity_available);
            $lineTotal  = $e->unit_price ? $suggested * $e->unit_price : null;
            $equipTotal += $lineTotal ?? 0;
        @endphp
        <tr>
            <td>{{ $e->name }}</td>
            <td>{{ $e->category }}</td>
            <td>{{ $e->reference ?? '—' }}</td>
            <td>{{ $e->quantity_available }}</td>
            <td>{{ $e->low_stock_threshold }}</td>
            <td><strong>{{ $suggested }}</strong></td>
            <td>{{ $e->unit_price ? number_format($e->unit_price,0,'.',' ').' F' : '—' }}</td>
            <td>{{ $lineTotal ? number_format($lineTotal,0,'.',' ').' F' : '—' }}</td>
        </tr>
        @endforeach
        @if($equipTotal > 0)
        <tr class="total-row">
            <td colspan="7" style="text-align:right;">Sous-total matériel</td>
            <td>{{ number_format($equipTotal,0,'.',' ') }} F</td>
        </tr>
        @endif
    </tbody>
</table>
@endif

@php $grandTotal = ($jerseyTotal ?? 0) + ($equipTotal ?? 0); @endphp
@if($grandTotal > 0)
<table>
    <tbody>
        <tr class="total-row">
            <td style="text-align:right;font-size:12px;">TOTAL ESTIMÉ</td>
            <td style="font-size:12px;">{{ number_format($grandTotal,0,'.',' ') }} F CFA</td>
        </tr>
    </tbody>
</table>
@endif

<div class="signature">
    <div class="sig-box">Signature du responsable</div>
    <div class="sig-box">Cachet du fournisseur</div>
    <div class="sig-box">Date de livraison prévue</div>
</div>

<div class="footer">
    Document généré par TeamTrack · {{ now()->format('d/m/Y H:i') }} — Ce bon de commande est indicatif et doit être validé par le responsable du club.
</div>
</body>
</html>
