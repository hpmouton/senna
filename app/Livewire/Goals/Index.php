<?php

namespace App\Livewire\Goals;

use App\Enums\AccountType;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Goal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public bool $showModal = false;
    public ?Goal $editingGoal = null;

    public array $form = [
        'name' => '',
        'target_amount' => '',
        'target_date' => '',
        'linked_account_id' => null,
    ];

    public array $contributionAmount = [];
    public ?int $contributionSourceAccountId = null;
    public Collection $savingsAccounts;
    public Collection $fundingAccounts;

    public function mount(): void
    {
        $accounts = Auth::user()->accounts;
        $this->savingsAccounts = $accounts->where('type', AccountType::SAVINGS);
        $this->fundingAccounts = $accounts->whereIn('type', [AccountType::CHECKING, AccountType::SAVINGS]);
        $this->contributionSourceAccountId = $this->fundingAccounts->first()?->id;
        $this->syncAllGoalBalances();
    }

    #[On('goal-updated')]
    public function syncAllGoalBalances(): void
    {
        $goals = Auth::user()->goals()->whereNotNull('linked_account_id')->with('linkedAccount')->get();
        foreach ($goals as $goal) {
            if ($goal->linkedAccount) {
                $goal->update(['current_amount' => $goal->linkedAccount->current_balance]);
            }
        }
    }

    public function create(): void
    {
        $this->editingGoal = new Goal();
        $this->reset('form');
        $this->showModal = true;
    }

    public function edit(Goal $goal): void
    {
        $this->editingGoal = $goal;
        $this->form = [
            'name' => $goal->name,
            'target_amount' => $goal->target_amount,
            'target_date' => $goal->target_date?->format('Y-m-d'),
            'linked_account_id' => $goal->linked_account_id,
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'form.name' => ['required', 'string', 'max:255'],
            'form.target_amount' => ['required', 'numeric', 'min:1'],
            'form.target_date' => ['nullable', 'date'],
            'form.linked_account_id' => ['nullable', 'exists:accounts,id'],
        ]);

        if ($this->editingGoal->exists) {
            $this->editingGoal->update($this->form);
        } else {
            Auth::user()->goals()->create($this->form);
        }

        $this->dispatch('goal-updated');
        $this->showModal = false;
        $this->dispatch('notify', 'Goal saved successfully!');
    }

    public function delete(Goal $goal): void
    {
        $goal->delete();
        $this->dispatch('notify', 'Goal deleted successfully!');
    }

    public function contribute(Goal $goal): void
    {
        $amount = $this->contributionAmount[$goal->id] ?? 0;

        $this->validate([
            'contributionSourceAccountId' => ['required', 'exists:accounts,id'],
        ]);
        validator(['amount' => $amount], ['amount' => ['required', 'numeric', 'min:0.01']])->validate();

        if (!$goal->linkedAccount) {
            $this->dispatch('notify', 'This goal is not linked to a savings account.', 'error');
            return;
        }

        $sourceAccount = Account::find($this->contributionSourceAccountId);
        $destinationAccount = $goal->linkedAccount;

        if ($sourceAccount->current_balance < $amount) {
            $this->dispatch('notify', 'Insufficient funds in source account.', 'error');
            return;
        }

        DB::transaction(function () use ($sourceAccount, $destinationAccount, $amount) {
            $sourceAccount->transactions()->create([
                'type' => TransactionType::EXPENSE,
                'amount' => $amount,
                'description' => 'Transfer to ' . $destinationAccount->name,
                'transaction_date' => now(),
            ]);
            $sourceAccount->decrement('current_balance', $amount);

            $destinationAccount->transactions()->create([
                'type' => TransactionType::INCOME,
                'amount' => $amount,
                'description' => 'Transfer from ' . $sourceAccount->name,
                'transaction_date' => now(),
            ]);
            $destinationAccount->increment('current_balance', $amount);
        });

        $this->dispatch('goal-updated');
        unset($this->contributionAmount[$goal->id]);
        $this->dispatch('notify', 'Contribution successful!');
    }

    public function render()
    {
        return view('livewire.goals.index', [
            'goals' => Auth::user()->goals()->with('linkedAccount')->get(),
        ]);
    }
}