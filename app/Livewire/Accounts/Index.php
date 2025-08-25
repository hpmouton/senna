<?php

namespace App\Livewire\Accounts;

use App\Enums\AccountType;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
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

    public function render()
    {
        return view('livewire.accounts.index', [
            'accounts' => Auth::user()->accounts()->get(),
            'accountTypes' => AccountType::cases(),
        ]);
    }
}