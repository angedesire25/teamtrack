<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    @font-face { font-family: 'DejaVu Sans'; src: url('{{ storage_path("fonts/DejaVuSans.ttf") }}') format('truetype'); font-weight: normal; }
    @font-face { font-family: 'DejaVu Sans'; src: url('{{ storage_path("fonts/DejaVuSans-Bold.ttf") }}') format('truetype'); font-weight: bold; }
    * { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 0; box-sizing: border-box; font-size: 10px; color: #1f2937; }
    body { padding: 24px; background: #fff; }
    .header { border-bottom: 3px solid #1E3A5F; padding-bottom: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; }
    .title { font-size: 18px; font-weight: bold; color: #1E3A5F; }
    .subtitle { color: #6b7280; font-size: 10px; margin-top: 2px; }
    .kpi-row { display: flex; gap: 12px; margin-bottom: 20px; }
    .kpi { flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; }
    .kpi-label { font-size: 8px; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
    .kpi-val { font-size: 16px; font-weight: bold; }
    .kpi-income { color: #059669; }
    .kpi-expense { color: #dc2626; }
    .kpi-balance { color: #1d4ed8; }
    .section-title { font-size: 11px; font-weight: bold; color: #1E3A5F; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 10px; text-transform: uppercase; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    th { background: #1E3A5F; color: white; padding: 5px 8px; text-align: left; font-size: 9px; }
    td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 9px; }
    tr:nth-child(even) td { background: #f9fafb; }
    .income { color: #059669; font-weight: bold; }
    .expense { color: #dc2626; font-weight: bold; }
    .total-row td { font-weight: bold; background: #f1f5f9; border-top: 2px solid #e2e8f0; }
    .cat-bar { height: 6px; border-radius: 3px; background: #dc2626; display: inline-block; }
    .footer { margin-top: 24px; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
</style>
</head>
<body>

<div class="header">
    <div>
        <div class="title">Rapport Financier</div>
        <div class="subtitle">{{ $tenant->name }} — Période : {{ $periodLabel }}</div>
    </div>
    <div class="subtitle" style="text-align:right;">Édité le {{ now()->format('d/m/Y à H:i') }}</div>
</div>

{{-- KPIs --}}
<div class="kpi-row">
    <div class="kpi">
        <div class="kpi-label">Total recettes</div>
        <div class="kpi-val kpi-income">{{ number_format($totalIncome, 0, '.', ' ') }} F</div>
    </div>
    <div class="kpi">
        <div class="kpi-label">Total dépenses</div>
        <div class="kpi-val kpi-expense">{{ number_format($totalExpenses, 0, '.', ' ') }} F</div>
    </div>
    <div class="kpi">
        <div class="kpi-label">Solde net</div>
        <div class="kpi-val kpi-balance" style="{{ $solde < 0 ? 'color:#dc2626;' : '' }}">
            {{ $solde >= 0 ? '+' : '' }}{{ number_format($solde, 0, '.', ' ') }} F
        </div>
    </div>
</div>

{{-- Évolution mensuelle --}}
<div class="section-title">Évolution mensuelle</div>
<table>
    <thead>
        <tr>
            <th>Mois</th>
            <th>Recettes</th>
            <th>Dépenses</th>
            <th>Solde</th>
        </tr>
    </thead>
    <tbody>
        @foreach($months as $m)
        @php
            $inc = $monthlyIncome[$m] ?? 0;
            $exp = $monthlyExpenses[$m] ?? 0;
            $bal = $inc - $exp;
        @endphp
        <tr>
            <td>{{ \Carbon\Carbon::create($year, $m)->translatedFormat('F Y') }}</td>
            <td class="income">{{ number_format($inc,0,'.',' ') }} F</td>
            <td class="expense">{{ number_format($exp,0,'.',' ') }} F</td>
            <td style="{{ $bal >= 0 ? 'color:#059669;' : 'color:#dc2626;' }} font-weight:bold;">
                {{ $bal >= 0 ? '+' : '' }}{{ number_format($bal,0,'.',' ') }} F
            </td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="income">{{ number_format($totalIncome,0,'.',' ') }} F</td>
            <td class="expense">{{ number_format($totalExpenses,0,'.',' ') }} F</td>
            <td style="{{ $solde >= 0 ? 'color:#059669;' : 'color:#dc2626;' }} font-weight:bold;">
                {{ $solde >= 0 ? '+' : '' }}{{ number_format($solde,0,'.',' ') }} F
            </td>
        </tr>
    </tbody>
</table>

{{-- Par catégorie --}}
@if($byCategory->isNotEmpty())
<div class="section-title">Dépenses par catégorie</div>
<table>
    <thead>
        <tr>
            <th>Catégorie</th>
            <th>Montant</th>
            <th>% du total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($byCategory as $cat => $amount)
        <tr>
            <td>{{ $cat }}</td>
            <td class="expense">{{ number_format($amount,0,'.',' ') }} F</td>
            <td>{{ $totalExpenses > 0 ? round($amount/$totalExpenses*100,1) : 0 }}%</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="expense">{{ number_format($totalExpenses,0,'.',' ') }} F</td>
            <td>100%</td>
        </tr>
    </tbody>
</table>
@endif

<div class="footer">TeamTrack — Rapport généré automatiquement · Confidentiel</div>
</body>
</html>
