<div x-data="{ show: true }" x-show="show" @onboarding-complete.window="show = false"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-140 flex items-center justify-center p-4">

    @if ($currentStep === 1)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl p-6 max-w-sm text-center">
            <h2 class="text-xl font-semibold">Welcome to Budgets!</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Let's quickly walk through how to set up your first monthly budget.</p>
            <div class="mt-6">
                <flux:button variant="primary" wire:click="nextStep">Let's Go!</flux:button>
            </div>
        </div>
    @endif

    @if ($currentStep === 2)
        <div class="relative w-full h-full">
            <div class="absolute top-8 right-8" style="z-index: 51;">
                <div class="relative">
                    <div class="absolute -inset-2 rounded-lg bg-white dark:bg-gray-700 animate-pulse"></div>
                    <flux:button variant="primary" class="relative">New Budget</flux:button>
                </div>
            </div>

            <div class="absolute top-24 right-8 bg-white dark:bg-gray-800 rounded-lg shadow-2xl p-4 max-w-xs" style="z-index: 52;">
                <p class="font-semibold">Start Here</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Click the "New Budget" button to create a spending goal for one of your categories.</p>
                <div class="mt-4 text-right">
                    <flux:button variant="primary" wire:click="nextStep">Next</flux:button>
                </div>
            </div>
        </div>
    @endif

     @if ($currentStep === 3)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl p-6 max-w-sm text-center">
            <h2 class="text-xl font-semibold">You're All Set!</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">That's all you need to know to get started. You can now create budgets and track your spending.</p>
            <div class="mt-6">
                <flux:button variant="primary" wire:click="finish">Finish Tutorial</flux:button>
            </div>
        </div>
    @endif
</div>