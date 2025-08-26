<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div>
            <h1 class="text-2xl font-semibold">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Here's a summary of your financial health for {{ $cycleStartDate->format('M j') }} - {{ $cycleEndDate->format('M j, Y') }}.
            </p>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('accounts.index') }}" wire:navigate class="block rounded-lg border bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Worth</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                            N${{ number_format($netWorth, 2) }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-polynesian_blue/20">
                        <svg class="h-6 w-6 text-polynesian_blue dark:text-process_cyan" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 3a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 9m18 3V9" /></svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('transactions.index') }}" wire:navigate class="block rounded-lg border bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Income This Month</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-green-600">
                            +N${{ number_format($incomeThisMonth, 2) }}
                        </p>
                    </div>
                     <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-nyanza/20">
                        <svg class="h-6 w-6 text-green-600 dark:text-nyanza" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('transactions.index') }}" wire:navigate class="block rounded-lg border bg-white p-6 shadow-sm transition-all hover:shadow-lg hover:-translate-y-1 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Expenses This Month</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-red-600">
                            -N${{ number_format($expensesThisMonth, 2) }}
                        </p>
                    </div>
                     <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-imperial_red/20">
                        <svg class="h-6 w-6 text-red-600 dark:text-imperial_red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>
                    </div>
                </div>
            </a>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <div class="relative rounded-lg border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="font-semibold">Spending Trend</h3>
                    
                    @if ($showSurplusTooltip)
                        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
                             class="absolute inset-0 z-10 flex items-center justify-center p-4 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm">
                            <div class="w-full max-w-md">
                                <flux:callout 
                                    variant="success" 
                                    icon="check-circle" 
                                    heading="Great Job! You have a surplus of ${{ number_format($surplusAmount, 2) }}."
                                >
                                    <p class="text-sm">Now is a great time to put that extra money towards one of your financial goals.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('goals.index') }}" wire:navigate class="font-bold text-green-600 hover:underline">
                                            View Goals &rarr;
                                        </a>
                                    </div>
                                    <x-slot:actions>
                                        <button @click="show = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                        </button>
                                    </x-slot:actions>
                                </flux:callout>
                            </div>
                        </div>
                    @endif

                     @if (!empty($spendingTrendData['values']))
                        <div class="mt-4">
                            <x-charts.line :data="$spendingTrendData" />
                        </div>
                    @else
                        <div class="mt-4 flex h-80 items-center justify-center text-center">
                             <p class="text-sm text-gray-500">Not enough data to show a trend yet.</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="lg:col-span-2">
                <div class="rounded-lg border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="font-semibold">Spending by Category</h3>
                    @if (!empty($spendingChartData['values']))
                        <div class="mt-4">
                            <x-charts.doughnut :data="$spendingChartData" />
                        </div>
                    @else
                        <div class="mt-4 flex h-80 items-center justify-center text-center">
                            <p class="text-sm text-gray-500">No expense data to display for this month.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-8">
            <div class="rounded-lg border bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="px-6 pt-6 font-semibold">Recent Transactions</h3>
                <ul class="mt-4 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentTransactions as $transaction)
                        <li class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-white/5">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $transaction->description }}</p>
                                <p class="text-sm text-gray-500">{{ $transaction->account->name }}</p>
                            </div>
                            <p class="font-semibold {{ $transaction->type === \App\Enums\TransactionType::INCOME ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type === \App\Enums\TransactionType::INCOME ? '+' : '-' }}N${{ number_format($transaction->amount, 2) }}
                            </p>
                        </li>
                    @empty
                        <li class="p-4 text-center text-gray-500">No transactions recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>