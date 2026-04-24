<?php

namespace App\Livewire\Club\Medical;

use App\Models\Injury;
use App\Models\MedicalCertificate;
use App\Models\MedicalClearance;
use App\Models\Player;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.club')]
class Record extends Component
{
    use WithFileUploads;

    public Player $player;

    // Formulaire d'aptitude
    public bool  $showClearanceModal = false;
    public string $clearanceStatus  = 'fit';
    public string $clearanceReason  = '';
    public string $clearanceDate    = '';
    public string $clearanceReview  = '';

    // Formulaire de blessure
    public bool   $showInjuryModal    = false;
    public ?int   $editingInjuryId    = null;
    public string $injuryType         = 'musculaire';
    public string $injuryDescription  = '';
    public string $injuryStartDate    = '';
    public string $injuryReturnEst    = '';
    public string $injuryReturnActual = '';
    public string $injuryTreatment    = '';
    public string $injuryStatus       = 'active';

    // Formulaire de certificat
    public bool   $showCertModal    = false;
    public ?int   $editingCertId    = null;
    public string $certType         = 'aptitude';
    public string $certIssuedAt     = '';
    public string $certExpiresAt    = '';
    public string $certNotes        = '';
    public $certFile                = null;

    public function mount(int $player): void
    {
        abort_unless(
            auth()->user()->hasAnyRole(['admin_club', 'staff_medical']),
            403
        );

        $this->player = Player::where('tenant_id', app('tenant')->id)
            ->findOrFail($player);

        $this->clearanceDate = now()->toDateString();
    }

    // ── Clearance ───────────────────────────────────────────────────────────

    public function openClearanceModal(): void
    {
        $latest = $this->player->latestClearance;
        $this->clearanceStatus = $latest?->status ?? 'fit';
        $this->clearanceReason = $latest?->reason ?? '';
        $this->clearanceDate   = now()->toDateString();
        $this->clearanceReview = '';
        $this->showClearanceModal = true;
    }

    public function saveClearance(): void
    {
        $this->validate([
            'clearanceStatus' => 'required|in:fit,unfit,conditional',
            'clearanceDate'   => 'required|date',
            'clearanceReview' => 'nullable|date|after:clearanceDate',
            'clearanceReason' => 'nullable|string|max:500',
        ]);

        MedicalClearance::create([
            'tenant_id'      => app('tenant')->id,
            'player_id'      => $this->player->id,
            'status'         => $this->clearanceStatus,
            'reason'         => $this->clearanceReason ?: null,
            'effective_date' => $this->clearanceDate,
            'review_date'    => $this->clearanceReview ?: null,
            'set_by_user_id' => auth()->id(),
        ]);

        $this->showClearanceModal = false;
        $this->player->refresh();
        $this->dispatch('toast', message: 'Aptitude mise à jour.', type: 'success');
    }

    // ── Injuries ─────────────────────────────────────────────────────────────

    public function openInjuryModal(?int $injuryId = null): void
    {
        $this->editingInjuryId = $injuryId;

        if ($injuryId) {
            $injury = Injury::findOrFail($injuryId);
            $this->injuryType         = $injury->injury_type;
            $this->injuryDescription  = $injury->description ?? '';
            $this->injuryStartDate    = $injury->start_date->toDateString();
            $this->injuryReturnEst    = $injury->estimated_return_date?->toDateString() ?? '';
            $this->injuryReturnActual = $injury->actual_return_date?->toDateString() ?? '';
            $this->injuryTreatment    = $injury->treatment ?? '';
            $this->injuryStatus       = $injury->status;
        } else {
            $this->injuryType         = 'musculaire';
            $this->injuryDescription  = '';
            $this->injuryStartDate    = now()->toDateString();
            $this->injuryReturnEst    = '';
            $this->injuryReturnActual = '';
            $this->injuryTreatment    = '';
            $this->injuryStatus       = 'active';
        }

        $this->showInjuryModal = true;
    }

    public function saveInjury(): void
    {
        $this->validate([
            'injuryType'         => 'required|in:musculaire,osseuse,ligamentaire,articulaire,tendon,autre',
            'injuryStartDate'    => 'required|date',
            'injuryReturnEst'    => 'nullable|date|after_or_equal:injuryStartDate',
            'injuryReturnActual' => 'nullable|date|after_or_equal:injuryStartDate',
            'injuryStatus'       => 'required|in:active,recovering,recovered',
            'injuryDescription'  => 'nullable|string|max:1000',
            'injuryTreatment'    => 'nullable|string|max:1000',
        ]);

        $data = [
            'tenant_id'             => app('tenant')->id,
            'player_id'             => $this->player->id,
            'injury_type'           => $this->injuryType,
            'description'           => $this->injuryDescription ?: null,
            'start_date'            => $this->injuryStartDate,
            'estimated_return_date' => $this->injuryReturnEst ?: null,
            'actual_return_date'    => $this->injuryReturnActual ?: null,
            'treatment'             => $this->injuryTreatment ?: null,
            'status'                => $this->injuryStatus,
            'reported_by_user_id'   => auth()->id(),
        ];

        if ($this->editingInjuryId) {
            Injury::findOrFail($this->editingInjuryId)->update($data);
            $msg = 'Blessure mise à jour.';
        } else {
            Injury::create($data);
            $msg = 'Blessure enregistrée.';
        }

        $this->showInjuryModal = false;
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function deleteInjury(int $injuryId): void
    {
        Injury::where('player_id', $this->player->id)->findOrFail($injuryId)->delete();
        $this->dispatch('toast', message: 'Blessure supprimée.', type: 'success');
    }

    // ── Certificates ─────────────────────────────────────────────────────────

    public function openCertModal(?int $certId = null): void
    {
        $this->editingCertId = $certId;
        $this->certFile      = null;

        if ($certId) {
            $cert = MedicalCertificate::findOrFail($certId);
            $this->certType      = $cert->certificate_type;
            $this->certIssuedAt  = $cert->issued_at->toDateString();
            $this->certExpiresAt = $cert->expires_at?->toDateString() ?? '';
            $this->certNotes     = $cert->notes ?? '';
        } else {
            $this->certType      = 'aptitude';
            $this->certIssuedAt  = now()->toDateString();
            $this->certExpiresAt = '';
            $this->certNotes     = '';
        }

        $this->showCertModal = true;
    }

    public function saveCert(): void
    {
        $this->validate([
            'certType'      => 'required|string|max:50',
            'certIssuedAt'  => 'required|date',
            'certExpiresAt' => 'nullable|date|after:certIssuedAt',
            'certNotes'     => 'nullable|string|max:1000',
            'certFile'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'tenant_id'           => app('tenant')->id,
            'player_id'           => $this->player->id,
            'certificate_type'    => $this->certType,
            'issued_at'           => $this->certIssuedAt,
            'expires_at'          => $this->certExpiresAt ?: null,
            'notes'               => $this->certNotes ?: null,
            'uploaded_by_user_id' => auth()->id(),
        ];

        if ($this->certFile) {
            $path = $this->certFile->store(
                'medical/'.app('tenant')->id.'/certificates',
                'local'
            );
            $data['file_path'] = $path;
        }

        if ($this->editingCertId) {
            MedicalCertificate::findOrFail($this->editingCertId)->update($data);
            $msg = 'Certificat mis à jour.';
        } else {
            MedicalCertificate::create($data);
            $msg = 'Certificat enregistré.';
        }

        $this->showCertModal = false;
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function deleteCert(int $certId): void
    {
        $cert = MedicalCertificate::where('player_id', $this->player->id)->findOrFail($certId);
        if ($cert->file_path) {
            Storage::disk('local')->delete($cert->file_path);
        }
        $cert->delete();
        $this->dispatch('toast', message: 'Certificat supprimé.', type: 'success');
    }

    public function render()
    {
        $injuries = Injury::where('player_id', $this->player->id)
            ->orderBy('start_date', 'desc')
            ->get();

        $certificates = MedicalCertificate::where('player_id', $this->player->id)
            ->orderBy('issued_at', 'desc')
            ->get();

        $clearanceHistory = MedicalClearance::where('player_id', $this->player->id)
            ->with('setBy')
            ->orderBy('effective_date', 'desc')
            ->take(10)
            ->get();

        return view('livewire.club.medical.record', [
            'injuries'         => $injuries,
            'certificates'     => $certificates,
            'clearanceHistory' => $clearanceHistory,
            'latestClearance'  => $clearanceHistory->first(),
        ])->title($this->player->first_name.' '.$this->player->last_name.' — Dossier médical');
    }
}
