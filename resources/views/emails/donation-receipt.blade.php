<x-mail::message>
# Merci pour votre don !

Bonjour **{{ $donation->is_anonymous ? 'Donateur' : $donation->donor?->fullName() }}**,

Nous avons bien reçu votre don et vous en remercions chaleureusement.

<x-mail::panel>
**Reçu N° {{ $donation->receipt_number }}**

💰 Montant : **{{ number_format($donation->amount, 0, '.', ' ') }} {{ strtoupper($donation->currency) }}**
📅 Date : {{ $donation->created_at->translatedFormat('d F Y') }}
🔄 Type : {{ $donation->frequencyLabel() }}
@if($donation->campaign)
🎯 Campagne : {{ $donation->campaign->title }}
@endif
</x-mail::panel>

Votre reçu officiel est joint à cet email en pièce jointe (PDF).

@if($donation->frequency !== 'one_time')
Votre don **{{ $donation->frequencyLabel() === 'Mensuel' ? 'mensuel' : 'annuel' }}** sera reconduit automatiquement. Vous pouvez l'annuler à tout moment en nous contactant.
@endif

Votre générosité nous aide à soutenir nos joueurs et à développer notre club. Merci infiniment !

{{ config('app.name') }}
</x-mail::message>
