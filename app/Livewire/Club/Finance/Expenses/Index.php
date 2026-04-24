<?php

namespace App\Livewire\Club\Finance\Expenses;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.club')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $categoryFilter = '';
    #[Url]
    public string $yearFilter = '';
    #[Url]
    public string $monthFilter = '';
    public string $search = '';

    // Modale de dépense
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $amount      = '';
    public string $date        = '';
    public string $description = '';
    public string $categoryId  = '';
    public string $reference   = '';
    public string $paid_by     = '';
    public string $notes       = '';

    // Modale de catégorie
    public bool $showCatModal = false;
    public ?int $editingCatId = null;
    public string $catName  = '';
    public string $catColor = '#6B7280';

    public function mount(): void
    {
        $this->yearFilter = (string) now()->year;
        $this->date = today()->format('Y-m-d');

        // Initialiser les catégories par défaut si aucune n'existe
        $tenantId = app('tenant')->id;
        if (ExpenseCategory::where('tenant_id', $tenantId)->doesntExist()) {
            foreach (ExpenseCategory::defaults() as $cat) {
                ExpenseCategory::create(array_merge($cat, ['tenant_id' => $tenantId, 'is_default' => true]));
            }
        }
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedCategoryFilter(): void { $this->resetPage(); }
    public function updatedYearFilter(): void { $this->resetPage(); }
    public function updatedMonthFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'amount', 'description', 'reference', 'paid_by', 'notes']);
        $this->date = today()->format('Y-m-d');
        $this->categoryId = '';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $e = Expense::findOrFail($id);
        $this->editingId   = $id;
        $this->amount      = $e->amount;
        $this->date        = $e->date->format('Y-m-d');
        $this->description = $e->description;
        $this->categoryId  = $e->category_id ?? '';
        $this->reference   = $e->reference ?? '';
        $this->paid_by     = $e->paid_by ?? '';
        $this->notes       = $e->notes ?? '';
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->validate([
            'amount'      => 'required|integer|min:1',
            'date'        => 'required|date',
            'description' => 'required|string|max:255',
            'categoryId'  => 'nullable|exists:expense_categories,id',
            'reference'   => 'nullable|string|max:100',
            'paid_by'     => 'nullable|string|max:100',
        ]);

        $data = [
            'tenant_id'   => app('tenant')->id,
            'amount'      => $this->amount,
            'date'        => $this->date,
            'description' => $this->description,
            'category_id' => $this->categoryId ?: null,
            'reference'   => $this->reference ?: null,
            'paid_by'     => $this->paid_by ?: null,
            'notes'       => $this->notes ?: null,
        ];

        if ($this->editingId) {
            Expense::findOrFail($this->editingId)->update($data);
        } else {
            Expense::create($data);
        }

        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        Expense::findOrFail($id)->delete();
    }

    // ── Categories ────────────────────────────────────────────
    public function openCreateCat(): void
    {
        $this->reset(['editingCatId', 'catName']);
        $this->catColor    = '#6B7280';
        $this->showCatModal = true;
    }

    public function openEditCat(int $id): void
    {
        $c = ExpenseCategory::findOrFail($id);
        $this->editingCatId = $id;
        $this->catName      = $c->name;
        $this->catColor     = $c->color;
        $this->showCatModal = true;
    }

    public function saveCat(): void
    {
        $this->validate([
            'catName'  => 'required|string|max:80',
            'catColor' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $data = ['tenant_id' => app('tenant')->id, 'name' => $this->catName, 'color' => $this->catColor];

        if ($this->editingCatId) {
            ExpenseCategory::findOrFail($this->editingCatId)->update($data);
        } else {
            ExpenseCategory::create($data);
        }

        $this->showCatModal = false;
    }

    public function deleteCat(int $id): void
    {
        ExpenseCategory::findOrFail($id)->delete();
    }

    public function render()
    {
        $tenantId = app('tenant')->id;

        $expenses = Expense::where('tenant_id', $tenantId)
            ->with('category')
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->yearFilter, fn ($q) => $q->whereYear('date', $this->yearFilter))
            ->when($this->monthFilter, fn ($q) => $q->whereMonth('date', $this->monthFilter))
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%")
                ->orWhere('paid_by', 'like', "%{$this->search}%"))
            ->orderByDesc('date')
            ->paginate(20);

        $totalFiltered = Expense::where('tenant_id', $tenantId)
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->yearFilter, fn ($q) => $q->whereYear('date', $this->yearFilter))
            ->when($this->monthFilter, fn ($q) => $q->whereMonth('date', $this->monthFilter))
            ->sum('amount');

        $categories = ExpenseCategory::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('livewire.club.finance.expenses.index', compact('expenses', 'totalFiltered', 'categories'));
    }
}
