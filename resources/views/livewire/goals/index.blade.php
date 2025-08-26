<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Financial Goals</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Set targets and track your progress towards a brighter financial future.
                </p>
            </div>
            <flux:button variant="primary" wire:click="create">
                New Goal
            </flux:button>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($goals as $goal)
                @php
                    $percentage = $goal->target_amount > 0 ? min(100, ($goal->current_amount / $goal->target_amount) * 100) : 100;
                @endphp
                <div wire:key="goal-{{ $goal->id }}" class="rounded-lg border bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <h3 class="font-semibold">{{ $goal->name }}</h3>
                            <div class="flex items-center space-x-1">
                                <flux:button wire:click="edit({{ $goal->id }})" icon="pencil" variant="ghost" size="sm" />
                                <flux:button wire:click="delete({{ $goal->id }})" wire:confirm="Are you sure you want to delete this goal?" icon="trash" variant="ghost" size="sm" color="danger" />
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Target: ${{ number_format($goal->target_amount, 2) }}
                        </p>

                        <div class="mt-4">
                            <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-2 rounded-full bg-process_cyan" style="width: {{ $percentage }}%;"></div>
                            </div>
                            <div class="mt-1 flex justify-between text-sm">
                                <span>{{ number_format($percentage, 1) }}% Complete</span>
                                <span class="font-medium text-gray-500">
                                    ${{ number_format($goal->current_amount, 2) }}
                                </span>
                            </div>
                        </div>

                        @if ($goal->linkedAccount)
                            <p class="text-xs text-gray-400 mt-2">Linked to: <strong>{{ $goal->linkedAccount->name }}</strong></p>
                        @endif
                    </div>

                    @if ($goal->linkedAccount)
                        <div class="mt-6">
                            <form wire:submit="contribute({{ $goal->id }})" class="space-y-2">
                                <flux:select wire:model.live="contributionSourceAccountId" label="Contribute from:">
                                    @foreach ($fundingAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }} (${{ number_format($account->current_balance, 2) }})</option>
                                    @endforeach
                                </flux:select>
                                
                                <div class="flex items-center space-x-2">
                                    <div class="flex-grow">
                                        <flux:input wire:model.live="contributionAmount.{{ $goal->id }}" type="number" step="0.01" placeholder="Amount..." />
                                    </div>
                                    <flux:button type="submit" variant="primary">Add</flux:button>
                                </div>
                            </form>
                        </div>
                    @else
                         <div class="mt-6 text-center text-xs text-gray-400 border-t pt-4 dark:border-gray-700">
                            Link this goal to a savings account to enable contributions.
                        </div>
                    @endif
                </div>
            @empty
                <div class="md:col-span-2 lg:col-span-3 text-center py-12 rounded-lg border-2 border-dashed dark:border-gray-700">
                    <p class="font-semibold text-gray-700 dark:text-gray-300">No Goals Yet</p>
                    <p class="mt-1 text-sm text-gray-500">Create a goal to start tracking your financial targets.</p>
                </div>
            @endforelse
        </div>
    </div>

    <flux:modal wire:model="showModal" class="md:w-96">
        <div class="p-4 sm:p-6">
            <h2 class="text-lg font-semibold">
                {{ optional($this->editingGoal)->exists ? 'Edit Goal' : 'Create Goal' }}
            </h2>
            <div class="mt-4">
                <form wire:submit="save" class="space-y-4">
                    <flux:input wire:model="form.name" label="Goal Name" required />
                    <flux:input wire:model="form.target_amount" label="Target Amount" type="number" step="0.01" required />
                    <flux:input wire:model="form.target_date" label="Target Date (Optional)" type="date" />

                    <flux:select wire:model="form.linked_account_id" label="Link to Savings Account (Optional)">
                        <option value="">Do not link</option>
                        @foreach ($savingsAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </flux:select>
                </form>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="save">Save Goal</flux:button>
            </div>
        </div>
    </flux:modal>
</div>