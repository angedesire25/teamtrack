<x-slot name="header">Suivi Médical</x-slot>

<div>
    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Blessés actifs --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Blessés actifs / rééducation</p>
            <p class="text-2xl font-bold text-red-600">{{ $activeInjuries }}</p>
        </div>

        {{-- Aptitude --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Aptes</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $clearanceCounts['fit'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Inaptes / sous réserve</p>
            <p class="text-2xl font-bold text-amber-600">{{ ($clearanceCounts['unfit'] ?? 0) + ($clearanceCounts['conditional'] ?? 0) }}</p>
        </div>

        {{-- Certificats --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Certificats expirés / &lt; 30j</p>
            <div class="flex items-baseline gap-2">
                <p class="text-2xl font-bold text-red-600">{{ $expiredCerts }}</p>
                @if($expiringSoonCerts > 0)
                    <span class="text-sm text-amber-500">+ {{ $expiringSoonCerts }} bientôt</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Barre d'outils --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 flex-1">
                <input wire:model.live.debounce.300ms="search"
                       type="text" placeholder="Rechercher un joueur…"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64">

                <select wire:model.live="clearanceFilter"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Toutes les aptitudes</option>
                    <option value="fit">Apte</option>
                    <option value="unfit">Inapte</option>
                    <option value="conditional">Sous réserve</option>
                    <option value="none">Sans évaluation</option>
                </select>
            </div>

            <a href="{{ route('club.medical.report-pdf') }}" target="_blank"
               class="inline-flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rapport hebdo PDF
            </a>
        </div>
    </div>

    {{-- Table joueurs --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Joueur</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aptitude</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Blessures actives</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dernier certificat</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($players as $player)
                    @php
                        $clearance = $player->latestClearance;
                        $activeInj = $player->injuries;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $player->last_name }} {{ $player->first_name }}</div>
                            <div class="text-xs text-gray-400">{{ $player->position }} · {{ $player->age() }} ans</div>
                        </td>

                        <td class="px-4 py-3">
                            @if($clearance)
                                @php
                                    $colors = ['fit' => 'bg-emerald-100 text-emerald-700', 'unfit' => 'bg-red-100 text-red-700', 'conditional' => 'bg-amber-100 text-amber-700'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$clearance->status] }}">
                                    {{ $clearance->statusLabel() }}
                                </span>
                                @if($clearance->review_date)
                                    <div class="text-xs text-gray-400 mt-0.5">Révision {{ $clearance->review_date->isoFormat('D MMM') }}</div>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">Non évalué</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            @if($activeInj->isEmpty())
                                <span class="text-xs text-gray-400">Aucune</span>
                            @else
                                @foreach($activeInj as $inj)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $inj->status === 'active' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }} mr-1 mb-1">
                                        {{ $inj->typeLabel() }}
                                    </span>
                                @endforeach
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            @php
                                $cert = $player->medicalCertificates->sortByDesc('issued_at')->first();
                            @endphp
                            @if($cert)
                                <div class="text-xs text-gray-700">{{ $cert->typeLabel() }}</div>
                                @if($cert->expires_at)
                                    @php $es = $cert->expiryStatus(); @endphp
                                    <div class="text-xs {{ $es === 'expired' ? 'text-red-500' : ($es === 'soon' ? 'text-amber-500' : 'text-gray-400') }}">
                                        Exp. {{ $cert->expires_at->isoFormat('D MMM YYYY') }}
                                        @if($es === 'expired') (expiré) @elseif($es === 'soon') (bientôt) @endif
                                    </div>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">Aucun</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('club.medical.record', $player) }}" wire:navigate
                               class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                Dossier
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm">
                            Aucun joueur trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($players->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $players->links() }}
            </div>
        @endif
    </div>
</div>
