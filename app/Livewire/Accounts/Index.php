<?php

namespace App\Livewire\Accounts;

use App\Enums\AccountType;
use App\Enums\TransactionType;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public bool $showModal = false;
    public ?Account $editingAccount = null;
    public array $form = [
        'name' => '',
        'type' => '',
        'starting_balance' => 0,
    ];

    public bool $showTransferModal = false;
    public ?int $from_account_id = null;
    public ?int $to_account_id = null;
    public $amount = 0;

    public function create(): void
    {
        $this->editingAccount = new Account();
        $this->form = [
            'name' => '',
            'type' => AccountType::CHECKING->value,
            'starting_balance' => 0,
        ];
        $this->showModal = true;
    }

    public function edit(Account $account): void
    {
        $this->editingAccount = $account;
        $this->form = [
            'name' => $account->name,
            'type' => $account->type->value,
            'starting_balance' => $account->starting_balance,
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'form.name' => ['required', 'string', 'max:255'],
            'form.type' => ['required', Rule::enum(AccountType::class)],
            'form.starting_balance' => ['required', 'numeric'],
        ]);

        if ($this->editingAccount->exists) {
            $this->editingAccount->update($this->form);
        } else {
            $data = $this->form;
            $data['current_balance'] = $this->form['starting_balance'];
            Auth::user()->accounts()->create($data);
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Account saved successfully!');
    }

    public function delete(Account $account): void
    {
        if ($account->transactions()->exists()) {
            $this->dispatch('notify', 'Cannot delete account with transactions.', 'error');
            return;
        }
        $account->delete();
        $this->dispatch('notify', 'Account deleted successfully!');
    }

    public function transfer(): void
    {
        $this->validate([
            'from_account_id' => ['required', 'exists:accounts,id'],
            'to_account_id' => ['required', 'exists:accounts,id', 'different:from_account_id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $fromAccount = Account::find($this->from_account_id);
        $toAccount = Account::find($this->to_account_id);

        if ($fromAccount->current_balance < $this->amount) {
            $this->dispatch('notify', 'Insufficient funds in the source account.', 'error');
            return;
        }

        DB::transaction(function () use ($fromAccount, $toAccount) {
            $fromAccount->transactions()->create([
                'type' => TransactionType::EXPENSE,
                'amount' => $this->amount,
                'description' => 'Transfer to ' . $toAccount->name,
                'transaction_date' => now(),
            ]);
            $fromAccount->decrement('current_balance', $this->amount);

            $toAccount->transactions()->create([
                'type' => TransactionType::INCOME,
                'amount' => $this->amount,
                'description' => 'Transfer from ' . $fromAccount->name,
                'transaction_date' => now(),
            ]);
            $toAccount->increment('current_balance', $this->amount);
        });

        $this->showTransferModal = false;
        $this->reset(['from_account_id', 'to_account_id', 'amount']);
        $this->dispatch('notify', 'Transfer completed successfully!');
    }

    public function render()
    {
        $accounts = Auth::user()->accounts()->get();

        if (!$this->from_account_id && $accounts->isNotEmpty()) {
            $this->from_account_id = $accounts->first()->id;
        }

        return view('livewire.accounts.index', [
            'accounts' => $accounts,
            'accountTypes' => AccountType::cases(),
        ]);
    }
}