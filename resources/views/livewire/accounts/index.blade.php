<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Accounts</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    A compact overview of your financial accounts.
                </p>
            </div>
            <div class="flex items-center space-x-2">
                @if($accounts->count() >= 2)
                    <flux:button wire:click="$set('showTransferModal', true)">
                        Transfer
                    </flux:button>
                @endif
                <flux:button variant="primary" wire:click="create">
                    New Account
                </flux:button>
            </div>
        </div>

        <div class="mt-8 flow-root">
            @if($accounts->isNotEmpty())
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden rounded-lg border shadow-sm dark:border-gray-700">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($accounts as $account)
                                    <li wire:key="{{ $account->id }}" class="group flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-white/5">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                @if($account->type === \App\Enums\AccountType::SAVINGS)
                                                    <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                                                @elseif($account->type === \App\Enums\AccountType::CREDIT_CARD)
                                                    <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                                                @else
                                                    <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" /></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $account->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ str($account->type->value)->title() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <p class="text-right font-semibold text-gray-900 dark:text-white">
                                                N${{ number_format($account->current_balance, 2) }}
                                            </p>
                                            <div class="flex items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <flux:button wire:click="edit({{ $account->id }})" icon="pencil" variant="ghost" size="sm" />
                                                <flux:button wire:click="delete({{ $account->id }})"
                                                           wire:confirm="Are you sure you want to delete this account?"
                                                           icon="trash" variant="ghost" color="danger" size="sm" />
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-16 flex flex-col items-center justify-center text-center">
                    <div class="rounded-full bg-red-100 border border-red-400 p-4 dark:bg-gray-800">
                        <svg class="h-8 w-8 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">No Accounts Yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding your first financial account.</p>
                    <div class="mt-6">
                        <flux:button variant="primary" wire:click="create">Add New Account</flux:button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <flux:modal wire:model="showModal" class="md:w-96">
        <div class="p-6">
            <h2 class="text-lg font-semibold">
                {{ optional($this->editingAccount)->exists ? 'Edit Account' : 'Create Account' }}
            </h2>
            <div class="mt-4">
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <flux:input wire:model="form.name" label="Account Name" placeholder="e.g., Selekt Red: Daily" />
                    </div>
                    <div>
                        <flux:select wire:model="form.type" label="Account Type">
                            @foreach($accountTypes as $type)
                                <option value="{{ $type->value }}">{{ str($type->value)->title() }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div>
                        <flux:input wire:model="form.starting_balance" label="Starting Balance" type="number" step="1"
                                  :disabled="optional($this->editingAccount)->exists" />
                        @if(optional($this->editingAccount)->exists)
                            <span class="text-xs text-gray-500 dark:text-gray-400">Starting balance cannot be changed.</span>
                        @endif
                    </div>
                </form>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="save">Save Account</flux:button>
            </div>
        </div>
    </flux:modal>
    
    <flux:modal wire:model="showTransferModal" class="md:w-96">
        <div class="p-6">
            <h2 class="text-lg font-semibold">
                Transfer Funds
            </h2>
            <div class="mt-4">
                <form wire:submit="transfer" class="space-y-4">
                    <flux:select wire:model.live="from_account_id" label="From">
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} (${{ number_format($account->current_balance, 2) }})</option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model.live="to_account_id" label="To">
                        @foreach($accounts->where('id', '!=', $from_account_id) as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </flux:select>
                    <div>
                        <flux:input wire:model="amount" label="Amount" type="number" step="0.01" />
                    </div>
                </form>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:button variant="ghost" wire:click="$set('showTransferModal', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="transfer">Confirm Transfer</flux:button>
            </div>
        </div>
    </flux:modal>
</div>