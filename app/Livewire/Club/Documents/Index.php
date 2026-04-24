<?php

namespace App\Livewire\Club\Documents;

use App\Models\Document;
use App\Models\Player;
use App\Models\Staff;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.club')]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    #[Url]
    public string $search        = '';
    #[Url]
    public string $entityFilter  = '';   // player | staff | club
    #[Url]
    public string $typeFilter    = '';
    #[Url]
    public string $statusFilter  = '';   // expiring | expired | unsigned

    // Modale d'ajout de document
    public bool    $showUploadModal   = false;
    public string  $uploadEntityType  = 'player';
    public ?int    $uploadEntityId    = null;
    public string  $uploadDocType     = 'contrat';
    public string  $uploadTitle       = '';
    public string  $uploadExpiresAt   = '';
    public string  $uploadNotes       = '';
    public         $uploadFile        = null;
    // Versionnage : null = nouveau document, sinon UUID du groupe
    public ?string $uploadGroupId     = null;
    public string  $entitySearch      = '';

    // Modale de signature
    public bool $showSignModal    = false;
    public ?int $signingDocId     = null;

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingEntityFilter(): void { $this->resetPage(); }
    public function updatingTypeFilter(): void   { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    // ── Modale upload ────────────────────────────────────────────────────────

    public function openUploadModal(string $entityType = 'player', ?int $entityId = null, ?string $groupId = null): void
    {
        $this->uploadEntityType = $entityType;
        $this->uploadEntityId   = $entityId;
        $this->uploadGroupId    = $groupId;
        $this->uploadDocType    = 'contrat';
        $this->uploadTitle      = '';
        $this->uploadExpiresAt  = '';
        $this->uploadNotes      = '';
        $this->uploadFile       = null;
        $this->entitySearch     = '';

        // Pré-remplir le titre si c'est une nouvelle version
        if ($groupId) {
            $parent = Document::where('document_group_id', $groupId)->latest('version')->first();
            if ($parent) {
                $this->uploadDocType = $parent->document_type;
                $this->uploadTitle   = $parent->title;
            }
        }

        $this->showUploadModal = true;
    }

    public function saveDocument(): void
    {
        $this->validate([
            'uploadEntityType' => 'required|in:player,staff,club',
            'uploadEntityId'   => 'required_unless:uploadEntityType,club|nullable|integer',
            'uploadDocType'    => 'required|in:contrat,licence,certificat_medical,autorisation_parentale,passeport,autre',
            'uploadTitle'      => 'required|string|max:200',
            'uploadExpiresAt'  => 'nullable|date|after:today',
            'uploadNotes'      => 'nullable|string|max:1000',
            'uploadFile'       => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $tenant   = app('tenant');
        $groupId  = $this->uploadGroupId ?? Document::newGroupId();
        $version  = 1;

        if ($this->uploadGroupId) {
            $version = Document::where('document_group_id', $this->uploadGroupId)
                           ->max('version') + 1;
        }

        // Résolution de l'entité propriétaire
        [$morphType, $morphId] = $this->resolveEntity($tenant->id);

        $path = $this->uploadFile->store(
            'documents/'.$tenant->id,
            'local'
        );

        Document::create([
            'tenant_id'          => $tenant->id,
            'documentable_type'  => $morphType,
            'documentable_id'    => $morphId,
            'document_group_id'  => $groupId,
            'version'            => $version,
            'document_type'      => $this->uploadDocType,
            'title'              => $this->uploadTitle,
            'file_path'          => $path,
            'file_name'          => $this->uploadFile->getClientOriginalName(),
            'file_size'          => $this->uploadFile->getSize(),
            'mime_type'          => $this->uploadFile->getMimeType(),
            'expires_at'         => $this->uploadExpiresAt ?: null,
            'notes'              => $this->uploadNotes ?: null,
            'uploaded_by_user_id'=> auth()->id(),
        ]);

        $this->showUploadModal = false;
        $this->dispatch('toast', message: 'Document enregistré (v'.$version.').', type: 'success');
    }

    private function resolveEntity(int $tenantId): array
    {
        return match($this->uploadEntityType) {
            'player' => [Player::class, $this->uploadEntityId],
            'staff'  => [Staff::class,  $this->uploadEntityId],
            default  => [\App\Models\Tenant::class, $tenantId],
        };
    }

    // ── Signature ────────────────────────────────────────────────────────────

    public function openSignModal(int $docId): void
    {
        $this->signingDocId   = $docId;
        $this->showSignModal  = true;
    }

    public function confirmSign(): void
    {
        $doc = Document::where('tenant_id', app('tenant')->id)->findOrFail($this->signingDocId);

        $doc->update([
            'signed_at'         => now(),
            'signed_by_user_id' => auth()->id(),
            'signed_ip'         => request()->ip(),
        ]);

        $this->showSignModal = false;
        $this->dispatch('toast', message: 'Document signé avec succès.', type: 'success');
    }

    public function deleteDocument(int $docId): void
    {
        $doc = Document::where('tenant_id', app('tenant')->id)->findOrFail($docId);
        if (Storage::disk('local')->exists($doc->file_path)) {
            Storage::disk('local')->delete($doc->file_path);
        }
        $doc->delete();
        $this->dispatch('toast', message: 'Document supprimé.', type: 'success');
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $tenantId = app('tenant')->id;

        // Alertes : expirations imminentes
        $expiringCount = Document::where('tenant_id', $tenantId)
            ->latestVersions()
            ->expiringSoon(30)
            ->count();

        $expiredCount = Document::where('tenant_id', $tenantId)
            ->latestVersions()
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', now())
            ->count();

        // Documents paginés (dernière version uniquement)
        $documents = Document::where('tenant_id', $tenantId)
            ->latestVersions()
            ->with(['documentable', 'uploadedBy', 'signedBy'])
            ->when($this->search, fn($q) =>
                $q->where('title', 'like', '%'.$this->search.'%')
            )
            ->when($this->entityFilter === 'player', fn($q) =>
                $q->where('documentable_type', Player::class)
            )
            ->when($this->entityFilter === 'staff', fn($q) =>
                $q->where('documentable_type', Staff::class)
            )
            ->when($this->entityFilter === 'club', fn($q) =>
                $q->where('documentable_type', \App\Models\Tenant::class)
            )
            ->when($this->typeFilter, fn($q) =>
                $q->where('document_type', $this->typeFilter)
            )
            ->when($this->statusFilter === 'expiring', fn($q) =>
                $q->expiringSoon(30)
            )
            ->when($this->statusFilter === 'expired', fn($q) =>
                $q->whereNotNull('expires_at')->whereDate('expires_at', '<', now())
            )
            ->when($this->statusFilter === 'unsigned', fn($q) =>
                $q->whereNull('signed_at')
            )
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        // Listes pour la modale
        $players = Player::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->when($this->entitySearch, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('first_name', 'like', '%'.$this->entitySearch.'%')
                       ->orWhere('last_name',  'like', '%'.$this->entitySearch.'%')
                )
            )
            ->orderBy('last_name')
            ->limit(30)
            ->get();

        $staffList = Staff::where('tenant_id', $tenantId)
            ->orderBy('last_name')
            ->get();

        return view('livewire.club.documents.index', [
            'documents'      => $documents,
            'expiringCount'  => $expiringCount,
            'expiredCount'   => $expiredCount,
            'players'        => $players,
            'staffList'      => $staffList,
        ])->title('Documents & Administratif');
    }
}
