<?php

namespace App\Livewire\Club\Players;

use App\Models\Category;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.club')]
class Form extends Component
{
    use WithFileUploads;

    public ?Player $player = null;

    // Champs du formulaire
    public string  $first_name        = '';
    public string  $last_name         = '';
    public string  $birth_date        = '';
    public string  $nationality       = 'CI';
    public ?int    $category_id       = null;
    public ?int    $team_id           = null;
    public string  $position          = '';
    public ?int    $jersey_number     = null;
    public string  $foot              = 'right';
    public string  $status            = 'active';
    public string  $phone             = '';
    public string  $email             = '';
    public string  $emergency_contact = '';
    public string  $emergency_phone   = '';
    public string  $license_number    = '';
    public string  $license_expires_at = '';
    public         $photo             = null; // fichier uploadé (Livewire)
    public ?string $existingPhoto     = null;

    public function mount(?Player $player = null): void
    {
        if ($player && $player->exists) {
            $this->player = $player;
            $this->fill([
                'first_name'        => $player->first_name,
                'last_name'         => $player->last_name,
                'birth_date'        => $player->birth_date?->format('Y-m-d') ?? '',
                'nationality'       => $player->nationality,
                'category_id'       => $player->category_id,
                'team_id'           => $player->team_id,
                'position'          => $player->position ?? '',
                'jersey_number'     => $player->jersey_number,
                'foot'              => $player->foot,
                'status'            => $player->status,
                'phone'             => $player->phone ?? '',
                'email'             => $player->email ?? '',
                'emergency_contact' => $player->emergency_contact ?? '',
                'emergency_phone'   => $player->emergency_phone ?? '',
                'license_number'    => $player->license_number ?? '',
                'license_expires_at' => $player->license_expires_at?->format('Y-m-d') ?? '',
                'existingPhoto'     => $player->photo,
            ]);
        }
    }

    public function save(): void
    {
        $this->validate([
            'first_name'         => 'required|string|max:100',
            'last_name'          => 'required|string|max:100',
            'birth_date'         => 'nullable|date',
            'category_id'        => 'nullable|exists:categories,id',
            'team_id'            => 'nullable|exists:teams,id',
            'position'           => 'nullable|string|max:50',
            'jersey_number'      => 'nullable|integer|min:1|max:99',
            'foot'               => 'in:right,left,both',
            'status'             => 'in:active,injured,suspended,loaned,transferred,former',
            'phone'              => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:150',
            'license_number'     => 'nullable|string|max:50',
            'license_expires_at' => 'nullable|date',
            'photo'              => 'nullable|image|max:2048',
        ]);

        $data = [
            'first_name'         => $this->first_name,
            'last_name'          => $this->last_name,
            'birth_date'         => $this->birth_date ?: null,
            'nationality'        => $this->nationality,
            'category_id'        => $this->category_id,
            'team_id'            => $this->team_id,
            'position'           => $this->position ?: null,
            'jersey_number'      => $this->jersey_number,
            'foot'               => $this->foot,
            'status'             => $this->status,
            'phone'              => $this->phone ?: null,
            'email'              => $this->email ?: null,
            'emergency_contact'  => $this->emergency_contact ?: null,
            'emergency_phone'    => $this->emergency_phone ?: null,
            'license_number'     => $this->license_number ?: null,
            'license_expires_at' => $this->license_expires_at ?: null,
        ];

        // Traitement de la photo
        if ($this->photo) {
            if ($this->existingPhoto) {
                Storage::disk('public')->delete($this->existingPhoto);
            }
            $data['photo'] = $this->photo->store('players', 'public');
        }

        if ($this->player && $this->player->exists) {
            $this->player->update($data);
            $this->dispatch('toast', message: 'Joueur mis à jour.', type: 'success');
        } else {
            Player::create($data);
            $this->dispatch('toast', message: 'Joueur créé avec succès.', type: 'success');
        }

        $this->redirect(route('club.players.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.club.players.form', [
            'categories' => Category::orderBy('sort_order')->orderBy('name')->get(),
            'teams'      => Team::when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
                               ->orderBy('name')->get(),
            'isEdit'     => $this->player && $this->player->exists,
        ])->title($this->player?->exists ? 'Modifier joueur' : 'Nouveau joueur');
    }
}
