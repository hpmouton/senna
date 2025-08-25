<?php

namespace App\Livewire\Budgets;

use App\Enums\TransactionType;
use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public Carbon $currentDate;
    public bool $showModal = false;
    public ?Budget $editingBudget = null;

    public array $form = [
        'category_id' => '',
        'amount' => '',
    ];

    public function mount(): void
    {
        $this->currentDate = now()->startOfMonth();
    }

    public function changeMonth(int $months): void
    {
        $this->currentDate->addMonths($months);
    }

    public function create(): void
    {
        $this->editingBudget = new Budget();
        $this->reset('form');
        $this->showModal = true;
    }

    public function edit(Budget $budget): void
    {
        $this->editingBudget = $budget;
        $this->form = [
            'category_id' => $budget->category_id,
            'amount' => $budget->amount,
        ];
        $this->showModal = true;
    }
    
    public function save(): void
    {
        $isEditing = $this->editingBudget->exists;

        $this->validate([
            'form.category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', Auth::id()),
                Rule::unique('budgets', 'category_id')->where('user_id', Auth::id())
                    ->where('month', $this->currentDate->month)
                    ->where('year', $this->currentDate->year)
                    ->ignore($this->editingBudget->id),
            ],
            'form.amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($isEditing) {
            $this->editingBudget->update($this->form);
        } else {
            Auth::user()->budgets()->create([
                'category_id' => $this->form['category_id'],
                'amount' => $this->form['amount'],
                'month' => $this->currentDate->month,
                'year' => $this->currentDate->year,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Budget saved successfully!');
    }

    public function delete(Budget $budget): void
    {
        $budget->delete();
        $this->dispatch('notify', 'Budget deleted successfully!');
    }

    public function render()
    {
        $user = Auth::user();
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();

        $budgets = $user->budgets()
            ->with('category')
            ->where('year', $this->currentDate->year)
            ->where('month', $this->currentDate->month)
            ->get();

        $budgetedCategoryIds = $budgets->pluck('category_id');

        // Get total spending for each budgeted category
        $spending = $user->transactions()
            ->where('transactions.type', TransactionType::EXPENSE) // Changed from 'type'
            ->whereIn('category_id', $budgetedCategoryIds)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category_id, sum(amount) as total_spent')
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');

        $availableCategories = $user->categories()->whereNotIn('id', $budgetedCategoryIds)->get();

        return view('livewire.budgets.index', [
            'budgets' => $budgets,
            'spending' => $spending,
            'availableCategories' => $availableCategories,
        ]);
    }
}