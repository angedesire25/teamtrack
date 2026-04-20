<?php

namespace App\Livewire\Club\Categories;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.club')]
class Index extends Component
{
    // --- État modal ---
    public bool    $showModal   = false;
    public ?int    $editingId   = null;
    public string  $name        = '';
    public ?int    $min_age     = null;
    public ?int    $max_age     = null;
    public string  $gender      = 'male';
    public int     $sort_order  = 0;

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'min_age', 'max_age', 'gender', 'sort_order']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingId  = $id;
        $this->name       = $cat->name;
        $this->min_age    = $cat->min_age;
        $this->max_age    = $cat->max_age;
        $this->gender     = $cat->gender;
        $this->sort_order = $cat->sort_order;
        $this->showModal  = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'       => 'required|string|max:80',
            'min_age'    => 'nullable|integer|min:4|max:99',
            'max_age'    => 'nullable|integer|min:4|max:99',
            'gender'     => 'in:male,female,mixed',
            'sort_order' => 'integer',
        ]);

        $data = [
            'name'       => $this->name,
            'min_age'    => $this->min_age,
            'max_age'    => $this->max_age,
            'gender'     => $this->gender,
            'sort_order' => $this->sort_order,
        ];

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: 'Catégorie mise à jour.', type: 'success');
        } else {
            Category::create($data);
            $this->dispatch('toast', message: 'Catégorie créée.', type: 'success');
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'min_age', 'max_age', 'gender', 'sort_order']);
    }

    public function delete(int $id): void
    {
        $cat = Category::withCount(['teams', 'players'])->findOrFail($id);

        if ($cat->teams_count > 0 || $cat->players_count > 0) {
            $this->dispatch('toast', message: 'Impossible : des équipes ou joueurs sont rattachés à cette catégorie.', type: 'error');
            return;
        }

        $cat->delete();
        $this->dispatch('toast', message: 'Catégorie supprimée.', type: 'success');
    }

    public function render()
    {
        return view('livewire.club.categories.index', [
            'categories' => Category::withCount(['teams', 'players'])
                ->orderBy('sort_order')->orderBy('name')->get(),
        ])->title('Catégories');
    }
}
