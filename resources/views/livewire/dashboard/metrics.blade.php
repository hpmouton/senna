<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div>
            <h1 class="text-2xl font-semibold">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Here's a summary of your financial health for {{ now()->format('F Y') }}.
            </p>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-lg border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">Net Worth</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    N${{ number_format($netWorth, 2) }}
                </p>
            </div>
            <div class="rounded-lg border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">Income This Month</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-green-600">
                    +N${{ number_format($incomeThisMonth, 2) }}
                </p>
            </div>
            <div class="rounded-lg border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">Expenses This Month</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-red-600">
                    -N${{ number_format($expensesThisMonth, 2) }}
                </p>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">


            <div class="lg:col-span-2">
                <div class="rounded-lg border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="font-semibold">Spending by Category</h3>

                    @if (!empty($spendingChartData['values']))
                        <div class="mt-4">
                            <x-charts.doughnut :data="$spendingChartData" />
                        </div>
                    @else
                        <div class="mt-4 flex h-80 items-center justify-center text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No expense data to display for this month.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>



            <div class="lg:col-span-1">
                <div class="rounded-lg border bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="px-6 pt-6 font-semibold">Recent Transactions</h3>
                    <ul class="mt-4 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentTransactions as $transaction)
                            <li class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-white/5">
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ $transaction->description }}</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->account->name }}</p>
                                </div>
                                <p
                                    class="font-semibold {{ $transaction->type === \App\Enums\TransactionType::INCOME ? 'text-green-600' : 'text-gray-800 dark:text-gray-200' }}">
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
</div>
