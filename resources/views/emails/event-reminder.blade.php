<x-mail::message>
# Rappel : {{ $event->typeLabel() }}

Bonjour **{{ $player->first_name }} {{ $player->last_name }}**,

Vous avez **{{ $event->typeLabel() }}** demain :

<x-mail::panel>
**{{ $event->title }}**

📅 {{ $event->starts_at->translatedFormat('l d F Y') }} à {{ $event->starts_at->format('H:i') }}
@if($event->field)
📍 {{ $event->field->name }}@if($event->field->address) — {{ $event->field->address }}@endif
@endif
@if($event->type === 'match' && $event->opponent)
⚽ {{ $event->home_away === 'home' ? 'Domicile' : 'Extérieur' }} vs **{{ $event->opponent }}**
@if($event->competition)
🏆 {{ $event->competition }}
@endif
@endif
</x-mail::panel>

@if($event->notes)
**Note :** {{ $event->notes }}
@endif

Bonne préparation !

{{ config('app.name') }}
</x-mail::message>
