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
                    @if($spendingChartLabels->isNotEmpty())
                        <div class="mt-4 h-80"
                             x-data="{
                                labels: @json($spendingChartLabels),
                                values: @json($spendingChartValues),
                                init() {
                                    new Chart(this.$refs.canvas, {
                                        type: 'doughnut',
                                        data: {
                                            labels: this.labels,
                                            datasets: [{
                                                data: this.values,
                                                backgroundColor: ['#34D399', '#60A5FA', '#FBBF24', '#F87171', '#A78BFA', '#A3E635'],
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { position: 'bottom' } }
                                        }
                                    })
                                }
                             }">
                            <canvas x-ref="canvas"></canvas>
                        </div>
                    @else
                        <div class="mt-4 flex h-80 items-center justify-center text-center">
                            <p class="text-gray-500">No expense data for this month yet.</p>
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
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $transaction->description }}</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->account->name }}</p>
                                </div>
                                <p class="font-semibold {{ $transaction->type === \App\Enums\TransactionType::INCOME ? 'text-green-600' : 'text-gray-800 dark:text-gray-200' }}">
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