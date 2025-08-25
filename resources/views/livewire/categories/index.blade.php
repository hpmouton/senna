<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Categories</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Organize your transactions by creating categories and sub-categories.
                </p>
            </div>
            <flux:button variant="primary" wire:click="create">
                New Category
            </flux:button>
        </div>

        <div class="mt-8 flow-root">
             <div class="overflow-hidden rounded-lg border dark:border-gray-700">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($categories as $category)
                        <li wire:key="{{ $category->id }}" class="group flex items-center justify-between p-4">
                            <span class="font-medium">{{ $category->name }}</span>
                            <div class="flex items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <flux:button wire:click="edit({{ $category->id }})" icon="pencil" variant="ghost" size="sm" />
                                <flux:button wire:click="delete({{ $category->id }})" 
                                           wire:confirm="Are you sure you want to delete this category?"
                                           icon="trash" variant="ghost" color="danger" size="sm" />
                            </div>
                        </li>
                        @if($category->children->isNotEmpty())
                            @foreach($category->children as $child)
                                <li wire:key="{{ $child->id }}" class="group flex items-center justify-between p-4 pl-10 bg-gray-50 dark:bg-gray-800/50">
                                    <div class="flex items-center">
                                        <span class="mr-2 text-gray-400">└</span>
                                        <span>{{ $child->name }}</span>
                                    </div>
                                    <div class="flex items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <flux:button wire:click="edit({{ $child->id }})" icon="pencil" variant="ghost" size="sm" />
                                        <flux:button wire:click="delete({{ $child->id }})" 
                                                   wire:confirm="Are you sure you want to delete this category?"
                                                   icon="trash" variant="ghost" color="danger" size="sm" />
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    @empty
                        <li class="text-center p-6">No categories found. Create your first one!</li>
                    @endforelse
                </ul>
             </div>
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
                        @foreach($parentCategoryOptions as $option)
                            @if($option->id !== $editingCategory?->id)
                                <option value="{{ $option->id }}">
                                    {{ $option->parent ? $option->parent->name . ' > ' : '' }}{{ $option->name }}
                                </option>
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