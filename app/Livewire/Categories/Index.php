<?php

namespace App\Livewire\Categories;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public bool $showModal = false;
    public ?Category $editingCategory = null;

    public array $form = [
        'name' => '',
        'parent_id' => null,
    ];

    public function create(): void
    {
        $this->editingCategory = new Category();
        $this->reset('form');
        $this->showModal = true;
    }

    public function edit(Category $category): void
    {
        $this->editingCategory = $category;
        $this->form = [
            'name' => $category->name,
            'parent_id' => $category->parent_id,
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'form.name' => ['required', 'string', 'max:255'],
            'form.parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where('user_id', Auth::id()),
                // Prevent a category from being its own parent
                Rule::notIn([$this->editingCategory?->id]),
            ],
        ]);

        if ($this->editingCategory->exists) {
            $this->editingCategory->update($this->form);
        } else {
            Auth::user()->categories()->create($this->form);
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Category saved successfully!');
    }

    public function delete(Category $category): void
    {
        // Safety check: Prevent deletion if the category has transactions.
        if ($category->transactions()->exists()) {
            $this->dispatch('notify', 'Cannot delete a category with transactions.', 'error');
            return;
        }

        // Safety check: Prevent deletion if the category has sub-categories.
        if ($category->children()->exists()) {
            $this->dispatch('notify', 'Cannot delete a parent category. Please delete its sub-categories first.', 'error');
            return;
        }

        $category->delete();
        $this->dispatch('notify', 'Category deleted successfully!');
    }

    public function render()
    {
        $user = Auth::user();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $allCategories = $user->categories()->with('children')->get();
        $parentCategories = $allCategories->whereNull('parent_id');

        $spendingByCategory = Transaction::query()
            ->whereIn('account_id', $user->accounts()->pluck('id'))
            ->where('type', TransactionType::EXPENSE)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('category_id')
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');

        return view('livewire.categories.index', [
            'categories' => $parentCategories,
            'spendingByCategory' => $spendingByCategory,
            'parentCategoryOptions' => $allCategories,
        ]);
    }
}