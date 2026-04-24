<?php

namespace App\Livewire\Club\Documents;

use App\Models\Document;
use App\Models\Player;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.club')]
class PlayerDossier extends Component
{
    use WithFileUploads;

    public Player $player;

    // Modale d'upload
    public bool    $showUploadModal  = false;
    public ?string $uploadGroupId    = null;   // null = nouveau document
    public string  $uploadDocType    = 'contrat';
    public string  $uploadTitle      = '';
    public string  $uploadExpiresAt  = '';
    public string  $uploadNotes      = '';
    public         $uploadFile       = null;

    // Modale de signature
    public bool $showSignModal = false;
    public ?int $signingDocId  = null;

    public function mount(int $player): void
    {
        $this->player = Player::where('tenant_id', app('tenant')->id)->findOrFail($player);
    }

    // ── Upload ───────────────────────────────────────────────────────────────

    public function openUploadModal(?string $groupId = null): void
    {
        $this->uploadGroupId   = $groupId;
        $this->uploadFile      = null;
        $this->uploadExpiresAt = '';
        $this->uploadNotes     = '';

        if ($groupId) {
            // Nouvelle version : pré-remplir le type et le titre depuis la version précédente
            $parent = Document::where('document_group_id', $groupId)->latest('version')->first();
            $this->uploadDocType = $parent?->document_type ?? 'contrat';
            $this->uploadTitle   = $parent?->title ?? '';
        } else {
            $this->uploadDocType = 'contrat';
            $this->uploadTitle   = '';
        }

        $this->showUploadModal = true;
    }

    public function saveDocument(): void
    {
        $this->validate([
            'uploadDocType'   => 'required|in:contrat,licence,certificat_medical,autorisation_parentale,passeport,autre',
            'uploadTitle'     => 'required|string|max:200',
            'uploadExpiresAt' => 'nullable|date|after:today',
            'uploadNotes'     => 'nullable|string|max:1000',
            'uploadFile'      => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $tenantId = app('tenant')->id;
        $groupId  = $this->uploadGroupId ?? Document::newGroupId();
        $version  = 1;

        if ($this->uploadGroupId) {
            $version = Document::where('document_group_id', $this->uploadGroupId)->max('version') + 1;
        }

        $path = $this->uploadFile->store('documents/'.$tenantId.'/players/'.$this->player->id, 'local');

        Document::create([
            'tenant_id'           => $tenantId,
            'documentable_type'   => Player::class,
            'documentable_id'     => $this->player->id,
            'document_group_id'   => $groupId,
            'version'             => $version,
            'document_type'       => $this->uploadDocType,
            'title'               => $this->uploadTitle,
            'file_path'           => $path,
            'file_name'           => $this->uploadFile->getClientOriginalName(),
            'file_size'           => $this->uploadFile->getSize(),
            'mime_type'           => $this->uploadFile->getMimeType(),
            'expires_at'          => $this->uploadExpiresAt ?: null,
            'notes'               => $this->uploadNotes ?: null,
            'uploaded_by_user_id' => auth()->id(),
        ]);

        $this->showUploadModal = false;
        $this->dispatch('toast', message: 'Document enregistré (v'.$version.').', type: 'success');
    }

    // ── Signature ────────────────────────────────────────────────────────────

    public function openSignModal(int $docId): void
    {
        $this->signingDocId  = $docId;
        $this->showSignModal = true;
    }

    public function confirmSign(): void
    {
        Document::where('documentable_type', Player::class)
            ->where('documentable_id', $this->player->id)
            ->findOrFail($this->signingDocId)
            ->update([
                'signed_at'         => now(),
                'signed_by_user_id' => auth()->id(),
                'signed_ip'         => request()->ip(),
            ]);

        $this->showSignModal = false;
        $this->dispatch('toast', message: 'Document signé électroniquement.', type: 'success');
    }

    public function deleteDocument(int $docId): void
    {
        $doc = Document::where('documentable_type', Player::class)
            ->where('documentable_id', $this->player->id)
            ->findOrFail($docId);

        if (Storage::disk('local')->exists($doc->file_path)) {
            Storage::disk('local')->delete($doc->file_path);
        }

        $doc->delete();
        $this->dispatch('toast', message: 'Document supprimé.', type: 'success');
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        // Tous les documents du joueur, groupés par document_group_id
        $allDocs = Document::where('documentable_type', Player::class)
            ->where('documentable_id', $this->player->id)
            ->with(['uploadedBy', 'signedBy'])
            ->orderBy('document_type')
            ->orderBy('document_group_id')
            ->orderBy('version', 'desc')
            ->get();

        // Groupement : clé = document_group_id, valeur = collection de versions (desc)
        $groups = $allDocs->groupBy('document_group_id')
            ->map(fn(Collection $versions) => [
                'latest'   => $versions->first(),          // version la plus haute
                'history'  => $versions->slice(1)->values(), // versions précédentes
            ])
            ->values();

        // Alertes d'expiration
        $expiredDocs  = $allDocs->filter(fn($d) => $d->isExpired() && $d->version === $allDocs->where('document_group_id', $d->document_group_id)->max('version'));
        $expiringSoon = $allDocs->filter(fn($d) => $d->expiresWithinDays(30) && !$d->isExpired());

        return view('livewire.club.documents.player-dossier', [
            'groups'       => $groups,
            'expiredDocs'  => $expiredDocs,
            'expiringSoon' => $expiringSoon,
        ])->title('Dossier — '.$this->player->last_name.' '.$this->player->first_name);
    }
}
