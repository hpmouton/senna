<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Categories</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Organize your transactions and analyze your spending.
                </p>
            </div>
            <flux:button variant="primary" wire:click="create">
                New Category
            </flux:button>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($categories as $category)
                @php
                    $childSpending = $category->children->reduce(function ($carry, $child) use ($spendingByCategory) {
                        return $carry + ($spendingByCategory[$child->id]->total ?? 0);
                    }, 0);
                    $parentSpending = $spendingByCategory[$category->id]->total ?? 0;
                    $totalGroupSpending = $parentSpending + $childSpending;
                @endphp
                <div wire:key="{{ $category->id }}" class="rounded-lg border bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 flex flex-col">
                    <div class="flex items-center justify-between border-b p-4 dark:border-gray-700">
                        <h3 class="font-semibold">{{ $category->name }}</h3>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">
                            ${{ number_format($totalGroupSpending, 2) }}
                        </p>
                    </div>
                    <ul class="flex-grow divide-y divide-gray-200 dark:divide-gray-700">
                       @forelse($category->children as $child)
                            <li class="group flex items-center justify-between p-3 pl-6">
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $child->name }}</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">${{ number_format($spendingByCategory[$child->id]->total ?? 0, 2) }}</span>
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        <flux:button wire:click="edit({{ $child->id }})" icon="pencil" variant="ghost" size="sm" />
                                        <flux:button wire:click="delete({{ $child->id }})" wire:confirm="Are you sure?" icon="trash" variant="ghost" color="danger" size="sm" />
                                    </div>
                                </div>
                            </li>
                       @empty
                            <li class="p-4 text-center text-sm text-gray-400">No sub-categories.</li>
                       @endforelse
                    </ul>
                    <div class="p-2 text-right">
                         <flux:button wire:click="edit({{ $category->id }})" variant="subtle" size="sm">Edit Parent</flux:button>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 lg:col-span-3 text-center py-12 rounded-lg border-2 border-dashed dark:border-gray-700">
                    <p class="text-gray-500">No categories found. Create your first one!</p>
                </div>
            @endforelse
        </div>
    </div>

    <flux:modal wire:model="showModal" class="md:w-96">
        <div class="p-4 sm:p-6">
            <h2 class="text-lg font-semibold">
                {{ optional($this->editingCategory)->exists ? 'Edit Category' : 'Create Category' }}
            </h2>
            <div class="mt-4">
                <form wire:submit="save" class="space-y-4">
                    <flux:input wire:model="form.name" label="Category Name" required />
                    <flux:select wire:model="form.parent_id" label="Parent Category (Optional)">
                        <option value="">No Parent</option>
                        @foreach($parentCategoryOptions->whereNull('parent_id') as $option)
                            @if($option->id !== $editingCategory?->id)
                                <option value="{{ $option->id }}">{{ $option->name }}</option>
                            @endif
                        @endforeach
                    </flux:select>
                </form>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="save">Save Category</flux:button>
            </div>
        </div>
    </flux:modal>
</div>