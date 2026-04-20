<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Joueurs</h1>
            <p class="text-gray-500 text-sm mt-0.5">{{ $players->total() }} joueur(s) au total</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportCsv"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </button>
            <a href="{{ route('club.players.create') }}" wire:navigate
               class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl transition-colors"
               style="background-color: var(--club-primary);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau joueur
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="relative sm:col-span-2 lg:col-span-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search"
                   type="text" placeholder="Nom, prénom, numéro…"
                   class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
        </div>
        <select wire:model.live="filterCat" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
            <option value="">Toutes catégories</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterTeam" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
            <option value="">Toutes équipes</option>
            @foreach($teams as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterPos" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
            <option value="">Tous postes</option>
            @foreach($positions as $p)
                <option value="{{ $p }}">{{ $p }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]/20 focus:border-[#1E3A5F]">
            <option value="">Tous statuts</option>
            @foreach($statuses as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Joueur</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden md:table-cell">Catégorie / Équipe</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden lg:table-cell">Poste</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide hidden lg:table-cell">Licence</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wide">Statut</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($players as $player)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                @if($player->photo)
                                    <img src="{{ Storage::url($player->photo) }}"
                                         class="w-9 h-9 rounded-full object-cover flex-shrink-0" alt="">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-[#1E3A5F]/10 flex items-center justify-center text-xs font-bold text-[#1E3A5F] flex-shrink-0">
                                        {{ strtoupper(substr($player->first_name,0,1).substr($player->last_name,0,1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $player->first_name }} {{ $player->last_name }}</p>
                                    <p class="text-xs text-gray-400">N°{{ $player->jersey_number ?? '—' }} · {{ $player->age() ? $player->age().' ans' : '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <p class="font-medium text-gray-700">{{ $player->category?->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $player->team?->name ?? 'Sans équipe' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-gray-600 hidden lg:table-cell">{{ $player->position ?? '—' }}</td>
                        <td class="px-4 py-3.5 hidden lg:table-cell">
                            @if($player->license_number)
                                <p class="text-gray-700 text-xs font-mono">{{ $player->license_number }}</p>
                                @if($player->license_expires_at)
                                    <p class="text-xs {{ $player->license_expires_at->isPast() ? 'text-red-500' : ($player->license_expires_at->diffInDays() < 30 ? 'text-amber-500' : 'text-gray-400') }}">
                                        Exp. {{ $player->license_expires_at->format('d/m/Y') }}
                                    </p>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click="open = !open"
                                        class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $player->statusColor() }} cursor-pointer">
                                    {{ $player->statusLabel() }}
                                </button>
                                <div x-show="open" @click.outside="open = false"
                                     class="absolute left-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-10 py-1 w-36"
                                     x-cloak>
                                    @foreach(['active' => 'Actif','injured' => 'Blessé','suspended' => 'Suspendu','loaned' => 'Prêté','former' => 'Ancien'] as $val => $label)
                                        <button wire:click="changeStatus({{ $player->id }}, '{{ $val }}')"
                                                @click="open = false"
                                                class="w-full text-left px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1 justify-end">
                                <a href="{{ route('club.players.edit', $player) }}" wire:navigate
                                   class="p-1.5 text-gray-400 hover:text-[#1E3A5F] hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                                <button wire:click="delete({{ $player->id }})"
                                        wire:confirm="Supprimer {{ $player->first_name }} {{ $player->last_name }} ?"
                                        class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-gray-500 font-medium">Aucun joueur trouvé</p>
                            <a href="{{ route('club.players.create') }}" wire:navigate class="mt-2 inline-block text-sm font-semibold text-[#1E3A5F] hover:underline">Ajouter le premier joueur →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($players->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $players->links() }}
        </div>
        @endif
    </div>
</div>
