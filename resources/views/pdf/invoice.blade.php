<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
@font-face { font-family: 'DejaVu Sans'; src: url('{{ storage_path("fonts/DejaVuSans.ttf") }}') format('truetype'); font-weight: normal; }
@font-face { font-family: 'DejaVu Sans'; src: url('{{ storage_path("fonts/DejaVuSans-Bold.ttf") }}') format('truetype'); font-weight: bold; }

* { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 0; box-sizing: border-box; color: #1f2937; }
body { background: #fff; font-size: 10px; padding: 0; }

/* ── En-tête ── */
.header { background: #1E3A5F; padding: 28px 36px; display: flex; justify-content: space-between; align-items: flex-start; }
.brand { color: white; }
.brand-name { font-size: 22px; font-weight: bold; letter-spacing: 1px; }
.brand-sub { font-size: 9px; color: #93c5fd; margin-top: 2px; }
.brand-contact { font-size: 8px; color: #bfdbfe; margin-top: 8px; line-height: 1.6; }
.invoice-meta { text-align: right; color: white; }
.invoice-num { font-size: 14px; font-weight: bold; }
.invoice-label { font-size: 8px; color: #93c5fd; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
.invoice-date { font-size: 9px; color: #bfdbfe; margin-top: 4px; }

/* ── Corps ── */
.body { padding: 28px 36px; }

/* Bandeau statut */
.status-band { padding: 6px 12px; border-radius: 4px; font-size: 9px; font-weight: bold; display: inline-block; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
.status-draft     { background: #f3f4f6; color: #6b7280; }
.status-sent      { background: #dbeafe; color: #1d4ed8; }
.status-paid      { background: #d1fae5; color: #065f46; }
.status-cancelled { background: #fee2e2; color: #991b1b; }

/* Parties (émetteur / destinataire) */
.parties { display: flex; gap: 24px; margin-bottom: 24px; }
.party { flex: 1; }
.party-label { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #9ca3af; margin-bottom: 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; }
.party-name { font-size: 12px; font-weight: bold; color: #111827; }
.party-detail { font-size: 9px; color: #4b5563; line-height: 1.7; margin-top: 3px; }

/* Tableau prestation */
.section-title { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #9ca3af; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.table-head th { background: #1E3A5F; color: white; padding: 7px 10px; text-align: left; font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
.table-head th:last-child { text-align: right; }
tbody tr td { padding: 9px 10px; border-bottom: 1px solid #f3f4f6; font-size: 9.5px; vertical-align: top; }
tbody tr:nth-child(even) td { background: #f9fafb; }
tbody td:last-child { text-align: right; font-weight: bold; }
.desc-main { font-weight: bold; color: #111827; }
.desc-sub { font-size: 8.5px; color: #6b7280; margin-top: 2px; }

/* Totaux */
.totals { display: flex; justify-content: flex-end; margin-bottom: 24px; }
.totals-box { width: 240px; }
.total-row { display: flex; justify-content: space-between; padding: 5px 10px; font-size: 9.5px; }
.total-row.ht { color: #4b5563; }
.total-row.divider { border-top: 1px solid #e5e7eb; }
.total-row.grand { background: #1E3A5F; color: white; border-radius: 4px; font-weight: bold; font-size: 11px; padding: 8px 10px; margin-top: 4px; }
.total-row.grand span:last-child { font-size: 13px; }

/* Notes */
.notes-box { background: #fffbeb; border-left: 3px solid #f59e0b; padding: 10px 14px; border-radius: 0 6px 6px 0; margin-bottom: 20px; font-size: 9px; color: #78350f; line-height: 1.6; }

/* Conditions */
.conditions { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px; font-size: 8.5px; color: #6b7280; line-height: 1.7; }
.conditions-title { font-weight: bold; color: #374151; margin-bottom: 4px; font-size: 9px; }

/* Pied de page */
.footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 10px 36px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 7.5px; color: #9ca3af; display: flex; justify-content: space-between; }

/* Tampon PAYÉE */
.stamp { position: absolute; top: 60px; right: 36px; border: 3px solid #059669; color: #059669; font-size: 22px; font-weight: bold; padding: 4px 12px; border-radius: 6px; opacity: 0.25; transform: rotate(-15deg); text-transform: uppercase; letter-spacing: 2px; }
.stamp-cancelled { border-color: #dc2626; color: #dc2626; }
</style>
</head>
<body>

{{-- ── En-tête ── --}}
<div class="header">
    <div class="brand">
        <div class="brand-name">TEAMTRACK</div>
        <div class="brand-sub">Plateforme de gestion de clubs sportifs</div>
        <div class="brand-contact">
            facturation@teamtrack.app<br>
            teamtrack.app<br>
            RC : TT-2026-00001
        </div>
    </div>
    <div class="invoice-meta">
        <div class="invoice-label">Facture</div>
        <div class="invoice-num">{{ $invoice->number }}</div>
        <div class="invoice-date">
            Émise le {{ $invoice->created_at->format('d/m/Y') }}<br>
            @if ($invoice->sent_at)
                Envoyée le {{ $invoice->sent_at->format('d/m/Y') }}<br>
            @endif
            @if ($invoice->paid_at)
                Payée le {{ $invoice->paid_at->format('d/m/Y') }}
            @endif
        </div>
    </div>
</div>

<div class="body" style="position:relative;">

    {{-- Tampon payée / annulée --}}
    @if ($invoice->status === 'paid')
        <div class="stamp">Payée</div>
    @elseif ($invoice->status === 'cancelled')
        <div class="stamp stamp-cancelled">Annulée</div>
    @endif

    {{-- Statut badge --}}
    <div class="status-band status-{{ $invoice->status }}">{{ $invoice->statusLabel() }}</div>

    {{-- Parties --}}
    <div class="parties">
        <div class="party">
            <div class="party-label">Émetteur</div>
            <div class="party-name">TeamTrack SAS</div>
            <div class="party-detail">
                Plateforme SaaS — Gestion de clubs sportifs<br>
                facturation@teamtrack.app<br>
                teamtrack.app
            </div>
        </div>
        <div class="party">
            <div class="party-label">Destinataire</div>
            <div class="party-name">{{ $invoice->tenant->name }}</div>
            <div class="party-detail">
                {{ $invoice->tenant->email }}<br>
                @if ($invoice->tenant->city || $invoice->tenant->country)
                    {{ $invoice->tenant->city }}{{ $invoice->tenant->city && $invoice->tenant->country ? ', ' : '' }}{{ $invoice->tenant->country }}<br>
                @endif
                @if ($invoice->tenant->phone)
                    {{ $invoice->tenant->phone }}<br>
                @endif
                Sous-domaine : {{ $invoice->tenant->subdomain }}.teamtrack.app
            </div>
        </div>
    </div>

    {{-- Tableau des prestations --}}
    <div class="section-title">Détail des prestations</div>
    <table>
        <thead class="table-head">
            <tr>
                <th style="width:50%">Description</th>
                <th style="width:25%">Période</th>
                <th style="width:25%">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="desc-main">Abonnement {{ $invoice->plan_name ?? 'TeamTrack' }}</div>
                    @if ($invoice->plan_description)
                        <div class="desc-sub">{{ $invoice->plan_description }}</div>
                    @else
                        <div class="desc-sub">Accès complet à la plateforme TeamTrack — gestion joueurs, planning, finances, médical, transferts, documents.</div>
                    @endif
                </td>
                <td style="color:#4b5563;">
                    Du {{ $invoice->period_start->format('d/m/Y') }}<br>
                    au {{ $invoice->period_end->format('d/m/Y') }}
                </td>
                <td>{{ number_format($invoice->amount / 100, 0, ',', ' ') }} XOF</td>
            </tr>
        </tbody>
    </table>

    {{-- Totaux --}}
    <div class="totals">
        <div class="totals-box">
            <div class="total-row ht">
                <span>Sous-total HT</span>
                <span>{{ number_format($invoice->amount / 100, 0, ',', ' ') }} XOF</span>
            </div>
            <div class="total-row ht">
                <span>TVA (0%)</span>
                <span>0 XOF</span>
            </div>
            <div class="total-row grand divider">
                <span>Total TTC</span>
                <span>{{ number_format($invoice->amount / 100, 0, ',', ' ') }} XOF</span>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if ($invoice->notes)
        <div class="notes-box">
            <strong>Note :</strong> {{ $invoice->notes }}
        </div>
    @endif

    {{-- Conditions --}}
    <div class="conditions">
        <div class="conditions-title">Conditions de paiement</div>
        Paiement à réception de facture · Délai maximum : 30 jours.<br>
        Modes acceptés : virement bancaire, mobile money (Wave, Orange Money), espèces.<br>
        En cas de retard, des pénalités de 1,5% par mois pourront être appliquées.<br>
        Facture exonérée de TVA conformément à la réglementation locale.
    </div>

</div>

{{-- Pied de page --}}
<div class="footer">
    <span>TeamTrack — Plateforme de gestion de clubs sportifs</span>
    <span>{{ $invoice->number }} · {{ $invoice->created_at->format('d/m/Y') }}</span>
    <span>teamtrack.app</span>
</div>

</body>
</html>
