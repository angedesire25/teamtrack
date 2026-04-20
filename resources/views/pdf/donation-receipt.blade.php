<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; padding: 30px; }
    .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 3px solid #1E3A5F; }
    .header h1 { font-size: 20px; font-weight: bold; color: #1E3A5F; }
    .header p  { font-size: 11px; color: #6b7280; margin-top: 4px; }
    .receipt-number { background: #f0f4ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 10px 16px; margin: 16px 0; text-align: center; }
    .receipt-number strong { font-size: 14px; color: #1E3A5F; }
    .section { margin: 16px 0; }
    .section h2 { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; margin-bottom: 8px; }
    .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6; }
    .row .label { color: #6b7280; }
    .row .value { font-weight: bold; color: #111827; }
    .amount-box { background: #1E3A5F; color: white; border-radius: 12px; padding: 16px; text-align: center; margin: 20px 0; }
    .amount-box .amount { font-size: 28px; font-weight: bold; }
    .amount-box .label  { font-size: 11px; opacity: .8; margin-top: 4px; }
    .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #9ca3af; text-align: center; }
    .thank-you { text-align: center; margin: 16px 0; font-size: 13px; font-weight: bold; color: #1E3A5F; }
    .stamp { border: 2px dashed #d1d5db; border-radius: 8px; padding: 16px; text-align: center; margin-top: 20px; color: #9ca3af; font-size: 10px; }
</style>
</head>
<body>

<div class="header">
    <h1>{{ $tenant?->name ?? 'Club' }}</h1>
    <p>Reçu de don officiel</p>
</div>

<div class="receipt-number">
    <strong>Reçu N° {{ $donation->receipt_number }}</strong>
</div>

<div class="amount-box">
    <div class="amount">{{ number_format($donation->amount, 0, '.', ' ') }} {{ strtoupper($donation->currency) }}</div>
    <div class="label">Montant du don · {{ $donation->frequencyLabel() }}</div>
</div>

<div class="section">
    <h2>Informations du donateur</h2>
    @if($donation->is_anonymous)
    <div class="row"><span class="label">Donateur</span><span class="value">Anonyme</span></div>
    @else
    <div class="row"><span class="label">Nom</span><span class="value">{{ $donation->donor?->fullName() ?? '—' }}</span></div>
    @if($donation->donor?->email)
    <div class="row"><span class="label">Email</span><span class="value">{{ $donation->donor->email }}</span></div>
    @endif
    @if($donation->donor?->address)
    <div class="row"><span class="label">Adresse</span><span class="value">{{ $donation->donor->address }}</span></div>
    @endif
    @endif
</div>

<div class="section">
    <h2>Détails du don</h2>
    <div class="row"><span class="label">Date</span><span class="value">{{ $donation->created_at->translatedFormat('d F Y') }}</span></div>
    <div class="row"><span class="label">Référence</span><span class="value">{{ $donation->receipt_number }}</span></div>
    <div class="row"><span class="label">Type</span><span class="value">{{ $donation->frequencyLabel() }}</span></div>
    @if($donation->campaign)
    <div class="row"><span class="label">Campagne</span><span class="value">{{ $donation->campaign->title }}</span></div>
    @endif
    <div class="row"><span class="label">Mode de paiement</span><span class="value">Carte bancaire (Stripe)</span></div>
    <div class="row"><span class="label">Statut</span><span class="value">✓ Complété</span></div>
</div>

<p class="thank-you">Merci pour votre générosité !</p>

<div class="stamp">
    Ce document tient lieu de reçu officiel.
    Conservez-le pour vos archives.
</div>

<div class="footer">
    <p>{{ $tenant?->name ?? 'Club' }} · Généré le {{ now()->translatedFormat('d F Y à H:i') }} · Propulsé par TeamTrack</p>
</div>
</body>
</html>
