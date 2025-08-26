<div>
    @if (!auth()->user()->has_completed_budget_onboarding)
        <livewire:onboarding.budget-wizard />
    @endif

    <div>
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Monthly Budget</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Allocate your income and track your spending goals.
                    </p>
                </div>
                @if ($budgets->isNotEmpty())
                    <flux:button variant="primary" wire:click="create">
                        New Budget
                    </flux:button>
                @endif
            </div>

            <div class="mt-6 flex items-center justify-center space-x-4">
                <flux:button wire:click="changeMonth(-1)" icon="chevron-left" variant="ghost" />
                <span class="text-lg font-semibold">{{ $this->currentDate->format('F Y') }}</span>
                <flux:button wire:click="changeMonth(1)" icon="chevron-right" variant="ghost" />
            </div>

            @if ($budgets->isNotEmpty())
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="rounded-lg border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-sm text-gray-500">Total Income</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-green-600">
                            ${{ number_format($totalIncome, 2) }}
                        </p>
                    </div>
                    <div class="rounded-lg border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-sm text-gray-500">Total Budgeted</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                            ${{ number_format($budgets->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-semibold">Expense Budgets</h3>
                    <div class="mt-4 space-y-4">
                        @foreach ($budgets as $budget)
                            @php
                                $spent = $spending[$budget->category_id]->total_spent ?? 0;
                                $remaining = $budget->amount - $spent;
                                $percentage = $budget->amount > 0 ? min(100, ($spent / $budget->amount) * 100) : 100;
                                $colorClass = match (true) {
                                    $percentage >= 100 => 'bg-red-500',
                                    $percentage >= 80 => 'bg-yellow-500',
                                    default => 'bg-green-500',
                                };
                            @endphp
                            <div wire:key="{{ $budget->id }}"
                                class="group rounded-lg border bg-white p-4 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center">
                                    <div
                                        class="mr-4 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                        <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                        </svg>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between">
                                            <p class="font-semibold">{{ $budget->category->name }}</p>
                                            <div
                                                class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <flux:button wire:click="edit({{ $budget->id }})" icon="pencil"
                                                    variant="ghost" size="sm" />
                                                <flux:button wire:click="delete({{ $budget->id }})"
                                                    wire:confirm="Are you sure?" icon="trash" variant="ghost"
                                                    color="danger" size="sm" />
                                            </div>
                                        </div>
                                        <div class="mt-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                                            <div class="h-2 rounded-full {{ $colorClass }}"
                                                style="width: {{ $percentage }}%;"></div>
                                        </div>
                                        <div class="mt-1 flex justify-between text-sm">
                                            <span class="text-gray-500">${{ number_format($spent, 2) }} spent</span>
                                            <span
                                                class="font-medium {{ $remaining < 0 ? 'text-red-600' : 'text-gray-500' }}">
                                                ${{ number_format(abs($remaining), 2) }}
                                                {{ $remaining < 0 ? 'over' : 'left' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif(auth()->user()->has_completed_budget_onboarding)
                <div class="mt-16 text-center py-12 rounded-lg border-2 border-dashed dark:border-gray-700">
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-nyanza dark:bg-polynesian_blue/20">
                        <svg class="h-8 w-8 text-polynesian_blue dark:text-process_cyan"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                        </svg>
                    </div>
                    <h2 class="mt-6 text-xl font-semibold">No Budgets Set</h2>
                    <p class="mt-2 max-w-md mx-auto text-gray-600 dark:text-gray-400">
                        You haven't set any budgets for this month. Click the button below to create your first one.
                    </p>
                    <div class="mt-8">
                        <flux:button variant="primary" wire:click="create">
                            Create a Budget
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>

        <flux:modal wire:model="showModal" class="md:w-96">
            <div class="p-4 sm:p-6">
                <h2 class="text-lg font-semibold">
                    {{ optional($this->editingBudget)->exists ? 'Edit Budget' : 'Create Budget' }}
                </h2>
                <div class="mt-4">
                    <form wire:submit="save" class="space-y-4">
                        <flux:select wire:model="form.category_id" label="Category"
                            :disabled="optional($this->editingBudget)->exists" required>
                            @if (optional($this->editingBudget)->exists)
                                <option value="{{ $this->editingBudget->category_id }}">
                                    {{ $this->editingBudget->category->name }}</option>
                            @else
                                <option value="">Select a category...</option>
                                @foreach ($availableCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </flux:select>
                        <flux:input wire:model="form.amount" label="Budget Amount" type="number" step="0.01"
                            required />
                    </form>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
                    <flux:button variant="primary" wire:click="save">Save Budget</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
