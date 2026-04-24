<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><style>
body { font-family: sans-serif; color: #374151; background: #f9fafb; margin: 0; padding: 32px 0; }
.wrap { max-width: 580px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
.header { background: #1E3A5F; padding: 24px 32px; }
.header h1 { color: white; margin: 0; font-size: 18px; }
.header p { color: #93c5fd; margin: 4px 0 0; font-size: 13px; }
.body { padding: 32px; line-height: 1.7; font-size: 15px; }
.invoice-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px 24px; margin: 20px 0; background: #f9fafb; }
.invoice-box .num { font-size: 20px; font-weight: bold; color: #1E3A5F; }
.invoice-box .detail { font-size: 13px; color: #6b7280; margin-top: 4px; }
.amount { font-size: 28px; font-weight: bold; color: #059669; margin: 8px 0; }
.footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; text-align: center; }
</style></head>
<body>
<div class="wrap">
    <div class="header">
        <h1>TeamTrack</h1>
        <p>Facture à l'attention de {{ $invoice->tenant->name }}</p>
    </div>
    <div class="body">
        <p>Bonjour,</p>
        <p>
            Veuillez trouver ci-joint votre facture TeamTrack pour la période
            du <strong>{{ $invoice->period_start->format('d/m/Y') }}</strong>
            au <strong>{{ $invoice->period_end->format('d/m/Y') }}</strong>.
        </p>

        <div class="invoice-box">
            <div class="num">Facture n° {{ $invoice->number }}</div>
            <div class="detail">
                Abonnement : {{ $invoice->plan_name ?? 'TeamTrack' }}<br>
                Période : {{ $invoice->period_start->format('d/m/Y') }} → {{ $invoice->period_end->format('d/m/Y') }}
            </div>
            <div class="amount">{{ number_format($invoice->amount / 100, 0, ',', ' ') }} XOF</div>
        </div>

        @if ($invoice->notes)
            <p style="font-size:14px;color:#4b5563;font-style:italic;">{{ $invoice->notes }}</p>
        @endif

        <p>
            La facture en format PDF est jointe à cet email.
            Conservez-la pour vos archives comptables.
        </p>

        <p style="font-size:13px;color:#6b7280;margin-top:24px;">
            Pour toute question relative à cette facture, contactez-nous à
            <a href="mailto:facturation@teamtrack.app" style="color:#1E3A5F;">facturation@teamtrack.app</a>.
        </p>
    </div>
    <div class="footer">TeamTrack · Super Administration · Ce message a été envoyé depuis la plateforme TeamTrack.</div>
</div>
</body>
</html>
