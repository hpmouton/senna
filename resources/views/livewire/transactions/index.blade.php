<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Transactions</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    A clear overview of your income and expenses.
                </p>
            </div>
            <flux:button variant="primary" wire:click="create">
                Add Transaction
            </flux:button>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <flux:select wire:model.live="filterAccount" label="Account">
                <option value="">All Accounts</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="filterType" label="Type">
                <option value="">All Types</option>
                @foreach($transactionTypes as $type)
                    <option value="{{ $type->value }}">{{ str($type->value)->title() }}</option>
                @endforeach
            </flux:select>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            
            <div class="rounded-lg border bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b p-4 dark:border-gray-700">
                    <h3 class="font-semibold">Income</h3>
                </div>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($incomes as $transaction)
                        <li wire:key="income-{{ $transaction->id }}" class="group flex items-center justify-between p-4">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $transaction->description }}</p>
                                <p class="text-sm text-gray-500">{{ $transaction->account->name }} &middot; {{ $transaction->transaction_date->format('M d') }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-green-600">+N${{ number_format($transaction->amount, 2) }}</span>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:button wire:click="edit({{ $transaction->id }})" icon="pencil" variant="ghost" size="sm" />
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="p-6 text-center text-sm text-gray-500">No income transactions found.</li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-lg border bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b p-4 dark:border-gray-700">
                    <h3 class="font-semibold">Expenses</h3>
                </div>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($expenses as $transaction)
                        <li wire:key="expense-{{ $transaction->id }}" class="group flex items-center justify-between p-4">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $transaction->description }}</p>
                                <p class="text-sm text-gray-500">{{ $transaction->account->name }} &middot; {{ $transaction->transaction_date->format('M d') }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-gray-800 dark:text-gray-200">-N${{ number_format($transaction->amount, 2) }}</span>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:button wire:click="edit({{ $transaction->id }})" icon="pencil" variant="ghost" size="sm" />
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="p-6 text-center text-sm text-gray-500">No expense transactions found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <flux:modal wire:model="showModal" class="md:w-96">
        <div class="p-4 sm:p-6">
            <h2 class="text-lg font-semibold">
                {{ optional($this->editingTransaction)->exists ? 'Edit Transaction' : 'Create Transaction' }}
            </h2>
            <div class="mt-4">
                <form wire:submit="save" class="space-y-4">
                    <flux:select wire:model="form.account_id" label="Account" required>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </flux:select>
                    
                    <flux:select wire:model="form.type" label="Type" required>
                        @foreach($transactionTypes as $type)
                            <option value="{{ $type->value }}">{{ str($type->value)->title() }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="form.amount" label="Amount" type="number" step="0.01" required />
                    <flux:input wire:model="form.description" label="Description" required />
                    
                    <flux:select wire:model="form.category_id" label="Category (Optional)">
                        <option value="">No Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->parent?->name . ' > ' }}{{ $category->name }}</option>
                        @endforeach
                    </flux:select>
                    
                    <flux:input wire:model="form.transaction_date" label="Date" type="date" required />
                </form>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="save">Save Transaction</flux:button>
            </div>
        </div>
    </flux:modal>
</div>