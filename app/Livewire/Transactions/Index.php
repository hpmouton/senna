<?php

namespace App\Livewire\Transactions;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?Transaction $editingTransaction = null;

    // Form state
    public array $form = [];

    // Filter properties
    public string $filterAccount = '';
    public string $filterType = '';

    // Data for dropdowns
    public Collection $accounts;
    public Collection $categories;

    public function mount(): void
    {
        $this->accounts = Auth::user()->accounts()->get();
        $this->categories = Auth::user()->categories()->get();
        $this->resetForm();
    }

    public function create(): void
    {
        $this->editingTransaction = new Transaction();
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(Transaction $transaction): void
    {
        $this->editingTransaction = $transaction;
        $this->form = [
            'account_id' => $transaction->account_id,
            'category_id' => $transaction->category_id,
            'type' => $transaction->type->value,
            'amount' => $transaction->amount,
            'description' => $transaction->description,
            'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'form.account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
            'form.category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', Auth::id())],
            'form.type' => ['required', Rule::enum(TransactionType::class)],
            'form.amount' => ['required', 'numeric', 'min:0.01'],
            'form.description' => ['required', 'string', 'max:255'],
            'form.transaction_date' => ['required', 'date'],
        ]);

        DB::transaction(function () {
            if ($this->editingTransaction->exists) {
                $originalAmount = $this->editingTransaction->amount;
                $originalType = $this->editingTransaction->type;
                $account = Account::find($this->editingTransaction->account_id);

                if ($originalType === TransactionType::INCOME) {
                    $account->decrement('current_balance', $originalAmount);
                } else {
                    $account->increment('current_balance', $originalAmount);
                }
                
                $this->editingTransaction->update($this->form);

                if ($this->form['type'] === TransactionType::INCOME->value) {
                    $account->increment('current_balance', $this->form['amount']);
                } else {
                    $account->decrement('current_balance', $this->form['amount']);
                }
            } else {
                $account = Account::find($this->form['account_id']);
                $account->transactions()->create($this->form);

                if ($this->form['type'] === TransactionType::INCOME->value) {
                    $account->increment('current_balance', $this->form['amount']);
                } else {
                    $account->decrement('current_balance', $this->form['amount']);
                }
            }
        });

        $this->showModal = false;
        $this->dispatch('notify', 'Transaction saved successfully!');
    }
    
    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $account = $transaction->account;
            
            if ($transaction->type === TransactionType::INCOME) {
                $account->decrement('current_balance', $transaction->amount);
            } else {
                $account->increment('current_balance', $transaction->amount);
            }
            
            $transaction->delete();
        });

        $this->dispatch('notify', 'Transaction deleted successfully!');
    }

    private function resetForm(): void
    {
        $this->form = [
            'account_id' => $this->accounts->first()->id ?? '',
            'category_id' => '',
            'type' => TransactionType::EXPENSE->value,
            'amount' => '',
            'description' => '',
            'transaction_date' => now()->format('Y-m-d'),
        ];
    }

    public function render()
    {
        $baseQuery = Transaction::query()->whereIn('account_id', $this->accounts->pluck('id'))
            ->with(['account', 'category']);

        if ($this->filterAccount) {
            $baseQuery->where('account_id', $this->filterAccount);
        }

        $incomeQuery = (clone $baseQuery)->where('type', TransactionType::INCOME);

        $expenseQuery = (clone $baseQuery)->where('type', TransactionType::EXPENSE);
        
        if ($this->filterType === TransactionType::INCOME->value) {
            $expenses = collect(); 
            $incomes = $incomeQuery->latest('transaction_date')->get();
        } elseif ($this->filterType === TransactionType::EXPENSE->value) {
            $incomes = collect(); 
            $expenses = $expenseQuery->latest('transaction_date')->get();
        } else {
            $incomes = $incomeQuery->latest('transaction_date')->get();
            $expenses = $expenseQuery->latest('transaction_date')->get();
        }
        
        return view('livewire.transactions.index', [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'transactionTypes' => TransactionType::cases(),
        ]);
    }
}