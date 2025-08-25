<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Transactions</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Log and manage your income and expenses.
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

        <div class="mt-8 flow-root">
             <div class="overflow-x-auto rounded-lg border dark:border-gray-700">
                <table class="min-w-full divide-y-2 divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium">Date</th>
                            <th class="px-4 py-2 text-left font-medium">Description</th>
                            <th class="px-4 py-2 text-left font-medium">Category</th>
                            <th class="px-4 py-2 text-left font-medium">Account</th>
                            <th class="px-4 py-2 text-right font-medium">Amount</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transactions as $transaction)
                            <tr wire:key="{{ $transaction->id }}">
                                <td class="px-4 py-2">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                <td class="px-4 py-2 font-medium">{{ $transaction->description }}</td>
                                <td class="px-4 py-2">{{ $transaction->category?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $transaction->account->name }}</td>
                                <td class="px-4 py-2 text-right font-semibold 
                                    {{ $transaction->type === \App\Enums\TransactionType::INCOME ? 'text-green-600' : 'text-gray-800 dark:text-gray-200' }}">
                                    {{ $transaction->type === \App\Enums\TransactionType::INCOME ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <flux:button wire:click="edit({{ $transaction->id }})" icon="pencil" variant="ghost" size="sm" />
                                    <flux:button wire:click="delete({{ $transaction->id }})" 
                                               wire:confirm="Are you sure you want to delete this transaction?"
                                               icon="trash" variant="ghost" color="danger" size="sm" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-6">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
             </div>
             <div class="mt-4">
                 {{ $transactions->links() }}
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
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
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