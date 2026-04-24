@component('mail::message')
# Rappel de cotisation

Bonjour **{{ $subscription->player->fullName() }}**,

Nous vous rappelons que votre cotisation pour la saison **{{ $subscription->season }}** est en attente de règlement.

@component('mail::panel')
**Montant dû :** {{ number_format($subscription->amount_due, 0, '.', ' ') }} F CFA
**Déjà payé :** {{ number_format($subscription->amount_paid, 0, '.', ' ') }} F CFA
**Reste à payer :** {{ number_format($subscription->amountRemaining(), 0, '.', ' ') }} F CFA
**Échéance :** {{ $subscription->due_date->translatedFormat('d F Y') }}
@endcomponent

@if($subscription->stripe_checkout_url)
Vous pouvez régler directement en ligne en cliquant sur le bouton ci-dessous :

@component('mail::button', ['url' => $subscription->stripe_checkout_url, 'color' => 'primary'])
Payer en ligne
@endcomponent
@endif

Pour tout paiement en espèces ou mobile money, veuillez vous rapprocher du responsable administratif du club.

Merci de votre fidélité et de votre soutien.

Cordialement,
**L'équipe administrative**
@endcomponent
