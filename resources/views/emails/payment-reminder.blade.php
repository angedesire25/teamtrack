<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><style>
body { font-family: sans-serif; color: #374151; background: #f9fafb; margin: 0; padding: 32px 0; }
.wrap { max-width: 580px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
.header { background: #1E3A5F; padding: 24px 32px; }
.header h1 { color: white; margin: 0; font-size: 18px; }
.header p { color: #93c5fd; margin: 4px 0 0; font-size: 13px; }
.body { padding: 32px; line-height: 1.7; font-size: 15px; }
.alert { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
.alert .amount { font-size: 24px; font-weight: bold; color: #dc2626; }
.table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
.table th { text-align: left; padding: 8px 12px; background: #f9fafb; color: #6b7280; font-weight: 600; border-bottom: 1px solid #e5e7eb; }
.table td { padding: 8px 12px; border-bottom: 1px solid #f3f4f6; color: #374151; }
.note { background: #fffbeb; border-left: 3px solid #f59e0b; padding: 12px 16px; margin: 20px 0; font-size: 14px; color: #92400e; border-radius: 0 8px 8px 0; }
.cta { text-align: center; margin: 28px 0; }
.cta a { background: #1E3A5F; color: white; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px; }
.footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; text-align: center; }
</style></head>
<body>
<div class="wrap">
    <div class="header">
        <h1>TeamTrack</h1>
        <p>Relance de paiement à l'attention de {{ $clubName }}</p>
    </div>
    <div class="body">
        <p>Bonjour,</p>
        <p>
            Nous vous contactons au sujet d'un ou plusieurs paiements en attente sur votre compte TeamTrack.
            Votre situation nécessite une régularisation dans les meilleurs délais.
        </p>

        <div class="alert">
            <div class="amount">{{ number_format($amountDue / 100, 0, ',', ' ') }} XOF</div>
            <div style="color:#ef4444;font-size:13px;margin-top:4px;">
                Total dû · En retard depuis {{ $daysOverdue }} jour{{ $daysOverdue > 1 ? 's' : '' }}
            </div>
        </div>

        @if ($pendingPayments->isNotEmpty())
        <p style="font-weight:600;color:#1f2937;margin-bottom:8px;">Détail des paiements en attente :</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingPayments as $payment)
                <tr>
                    <td>{{ $payment->reference }}</td>
                    <td>{{ number_format($payment->amount / 100, 0, ',', ' ') }} XOF</td>
                    <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                    <td style="color:#d97706;font-weight:600;">
                        {{ $payment->status === 'failed' ? 'Échoué' : 'En attente' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if ($note)
        <div class="note">
            <strong>Message de notre équipe :</strong><br>
            {{ $note }}
        </div>
        @endif

        <p>
            Pour régulariser votre situation, veuillez procéder au règlement via votre espace client
            ou contacter notre équipe support.
        </p>

        <div class="cta">
            <a href="#">Accéder à mon espace</a>
        </div>

        <p style="font-size:13px;color:#6b7280;">
            Si vous pensez recevoir ce message par erreur ou si vous avez déjà effectué le paiement,
            veuillez ignorer cet email ou contacter le support TeamTrack.
        </p>
    </div>
    <div class="footer">TeamTrack · Super Administration · Ce message a été envoyé depuis la plateforme TeamTrack.</div>
</div>
</body>
</html>
