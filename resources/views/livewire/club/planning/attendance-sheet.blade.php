<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('club.planning.edit', $event->id) }}" wire:navigate class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Feuille de présence</h1>
            <p class="text-gray-500 text-sm mt-0.5">{{ $event->title }} · {{ $event->starts_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        @php $colors = ['total' => 'gray', 'present' => 'emerald', 'absent' => 'red', 'excused' => 'amber']; @endphp
        @foreach(['total' => 'Total', 'present' => 'Présents', 'absent' => 'Absents', 'excused' => 'Excusés'] as $key => $label)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-extrabold text-{{ $colors[$key] }}-600">{{ $stats[$key] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Liste joueurs --}}
    <form wire:submit="save">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Liste des joueurs</h2>
            </div>
            @if($players->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="text-gray-400">Aucun joueur convoqué pour cet entraînement.</p>
                    <a href="{{ route('club.planning.edit', $event->id) }}" wire:navigate
                       class="mt-2 inline-block text-sm font-semibold text-[#1E3A5F] hover:underline">
                        Ajouter des joueurs →
                    </a>
                </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($players as $ep)
                <div class="flex items-center gap-4 px-5 py-3">
                    <div class="w-9 h-9 rounded-full bg-[#1E3A5F]/10 flex items-center justify-center text-xs font-bold text-[#1E3A5F] flex-shrink-0">
                        {{ $ep->player->jersey_number ?? strtoupper(substr($ep->player->first_name,0,1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800">{{ $ep->player->first_name }} {{ $ep->player->last_name }}</p>
                        <p class="text-xs text-gray-400">{{ $ep->player->position ?? '—' }}</p>
                    </div>
                    <div class="flex gap-1.5">
                        @foreach(['present' => ['Présent', 'emerald'], 'absent' => ['Absent', 'red'], 'excused' => ['Excusé', 'amber']] as $status => [$label, $color])
                        <button type="button"
                                wire:click="setStatus({{ $ep->player_id }}, '{{ $status }}')"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-colors
                                       {{ ($attendance[(string)$ep->player_id] ?? 'convoked') === $status
                                            ? "border-{$color}-300 bg-{$color}-50 text-{$color}-700"
                                            : 'border-gray-200 text-gray-400 hover:bg-gray-50' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        @if($players->isNotEmpty())
        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white rounded-xl shadow-sm"
                style="background-color: var(--club-primary);">
            <span wire:loading.remove wire:target="save">Enregistrer les présences</span>
            <span wire:loading wire:target="save">Enregistrement…</span>
        </button>
        @endif
    </form>
</div>
