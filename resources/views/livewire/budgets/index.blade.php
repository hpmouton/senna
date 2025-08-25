<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Budgets</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Set and track your monthly spending goals.
                </p>
            </div>
            <flux:button variant="primary" wire:click="create">
                New Budget
            </flux:button>
        </div>

        <div class="mt-6 flex items-center justify-center space-x-4">
            <flux:button wire:click="changeMonth(-1)" icon="chevron-left" variant="ghost" />
            <span class="text-lg font-semibold">{{ $this->currentDate->format('F Y') }}</span>
            <flux:button wire:click="changeMonth(1)" icon="chevron-right" variant="ghost" />
        </div>

        <div class="mt-8 space-y-6">
            @forelse($budgets as $budget)
                @php
                    $spent = $spending[$budget->category_id]->total_spent ?? 0;
                    $remaining = $budget->amount - $spent;
                    $percentage = $budget->amount > 0 ? min(100, ($spent / $budget->amount) * 100) : 100;

                    $colorClass = match (true) {
                        $percentage >= 100 => 'bg-red-500',
                        $percentage >= 75 => 'bg-yellow-500',
                        default => 'bg-green-500',
                    };
                @endphp
                <div wire:key="{{ $budget->id }}">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">{{ $budget->category->name }}</span>
                        <div class="flex items-center space-x-2">
                             <span class="text-sm text-gray-500">${{ number_format($spent, 2) }} of ${{ number_format($budget->amount, 2) }}</span>
                             <flux:button wire:click="edit({{ $budget->id }})" icon="pencil" variant="ghost" size="sm" />
                             <flux:button wire:click="delete({{ $budget->id }})" wire:confirm="Are you sure?" icon="trash" variant="ghost" color="danger" size="sm" />
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-2 rounded-full {{ $colorClass }}" style="width: {{ $percentage }}%;"></div>
                        </div>
                        <p class="mt-1 text-right text-sm font-medium {{ $remaining < 0 ? 'text-red-600' : 'text-gray-500' }}">
                            @if($remaining >= 0)
                                ${{ number_format($remaining, 2) }} remaining
                            @else
                                ${{ number_format(abs($remaining), 2) }} over budget
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-gray-500">No budgets set for this month.</p>
                </div>
            @endforelse
        </div>
    </div>

    <flux:modal wire:model="showModal" class="md:w-96">
        <div class="p-4 sm:p-6">
            <h2 class="text-lg font-semibold">
                {{ optional($this->editingBudget)->exists ? 'Edit Budget' : 'Create Budget' }}
            </h2>
            <div class="mt-4">
                <form wire:submit="save" class="space-y-4">
                    <flux:select wire:model="form.category_id" label="Category" :disabled="optional($this->editingBudget)->exists" required>
                        @if(optional($this->editingBudget)->exists)
                            <option value="{{ $this->editingBudget->category_id }}">{{ $this->editingBudget->category->name }}</option>
                        @else
                            <option value="">Select a category...</option>
                            @foreach($availableCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        @endif
                    </flux:select>
                    <flux:input wire:model="form.amount" label="Budget Amount" type="number" step="0.01" required />
                </form>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="save">Save Budget</flux:button>
            </div>
        </div>
    </flux:modal>
</div>