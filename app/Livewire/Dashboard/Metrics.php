<?php

namespace App\Livewire\Dashboard;

use App\Enums\AccountType;
use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Metrics extends Component
{
    public function render()
    {
        $user = Auth::user();
        $userAccountIds = $user->accounts()->pluck('id');

        $lastSalary = Transaction::query()
            ->whereIn('account_id', $userAccountIds)
            ->where('type', TransactionType::INCOME)
            ->where('description', 'LIKE', '%pay day%')
            ->where('transaction_date', '<=', now())
            ->latest('transaction_date')
            ->first();

        if ($lastSalary) {
            $cycleStartDate = $lastSalary->transaction_date;
            $cycleEndDate = $cycleStartDate->copy()->addMonth();
        } else {
            $cycleStartDate = now()->startOfMonth();
            $cycleEndDate = now()->endOfMonth();
        }

        $netWorth = $user->accounts()->sum('current_balance');

        $incomeThisMonth = Transaction::query()
            ->whereIn('account_id', $userAccountIds)
            ->where('type', TransactionType::INCOME)
            ->whereBetween('transaction_date', [$cycleStartDate, $cycleEndDate])
            ->where('description', 'NOT LIKE', '%Transfer from%')
            ->sum('amount');

        $expensesThisMonth = Transaction::query()
            ->whereIn('account_id', $userAccountIds)
            ->where('type', TransactionType::EXPENSE)
            ->whereBetween('transaction_date', [$cycleStartDate, $cycleEndDate])
            ->where('description', 'NOT LIKE', '%Transfer to%')
            ->sum('amount');

        $spendingByCategory = Transaction::query()
            ->with('category')
            ->whereIn('account_id', $userAccountIds)
            ->where('type', TransactionType::EXPENSE)
            ->whereBetween('transaction_date', [$cycleStartDate, $cycleEndDate])
            ->whereNotNull('category_id')
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $chartDataCollection = $spendingByCategory->take(5);
        $otherTotal = $spendingByCategory->skip(5)->sum('total');

        if ($otherTotal > 0) {
            $chartDataCollection->push((object) ['category' => (object) ['name' => 'Other'], 'total' => $otherTotal]);
        }

        $spendingChartData = [
            'labels' => $chartDataCollection->pluck('category.name')->toArray(),
            'values' => $chartDataCollection->pluck('total')->toArray(),
        ];

        $currentAccounts = $user->accounts()->whereNotIn('type', [AccountType::SAVINGS, AccountType::INVESTMENT])->get();
        $currentAccountIds = $currentAccounts->pluck('id');
        
        $dailyTransactions = Transaction::query()
            ->whereIn('account_id', $currentAccountIds)
            ->where('transaction_date', '>=', $cycleStartDate)
            ->orderBy('transaction_date')
            ->get()
            ->groupBy(fn ($transaction) => $transaction->transaction_date->format('Y-m-d'));

        $balanceOnCycleStart = $currentAccounts->sum('starting_balance')
            + Transaction::query()->whereIn('account_id', $currentAccountIds)->where('type', TransactionType::INCOME)->where('transaction_date', '<', $cycleStartDate)->sum('amount')
            - Transaction::query()->whereIn('account_id', $currentAccountIds)->where('type', TransactionType::EXPENSE)->where('transaction_date', '<', $cycleStartDate)->sum('amount');

        $trendLabels = [];
        $trendValues = [];
        $idealValues = [];
        $runningBalance = $balanceOnCycleStart;

        if ($lastSalary) {
            $daysInCycle = $cycleStartDate->diffInDays($cycleEndDate);
            $dailyBurnRate = $lastSalary->amount / $daysInCycle;

            for ($day = $cycleStartDate->copy(); $day <= now() && $day <= $cycleEndDate; $day->addDay()) {
                $dateString = $day->format('Y-m-d');
                $trendLabels[] = $day->format('j M');

                if (isset($dailyTransactions[$dateString])) {
                    foreach ($dailyTransactions[$dateString] as $transaction) {
                        if ($transaction->type === TransactionType::INCOME) {
                            $runningBalance += $transaction->amount;
                        } else {
                            $runningBalance -= $transaction->amount;
                        }
                    }
                }
                $trendValues[] = $runningBalance;
                
                $daysSincePayday = $cycleStartDate->diffInDays($day);
                $idealBalance = $lastSalary->amount - ($daysSincePayday * $dailyBurnRate);
                $idealValues[] = max(0, $idealBalance);
            }
        } else {
            for ($day = $cycleStartDate->copy(); $day <= now(); $day->addDay()) {
                 $dateString = $day->format('Y-m-d');
                $trendLabels[] = $day->format('j M');

                if (isset($dailyTransactions[$dateString])) {
                    foreach ($dailyTransactions[$dateString] as $transaction) {
                        if ($transaction->type === TransactionType::INCOME) {
                            $runningBalance += $transaction->amount;
                        } else {
                            $runningBalance -= $transaction->amount;
                        }
                    }
                }
                $trendValues[] = $runningBalance;
            }
        }

        $spendingTrendData = [
            'labels' => $trendLabels,
            'values' => $trendValues,
            'idealValues' => $idealValues,
        ];

        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        return view('livewire.dashboard.metrics', [
            'netWorth' => $netWorth,
            'incomeThisMonth' => $incomeThisMonth,
            'expensesThisMonth' => $expensesThisMonth,
            'spendingChartData' => $spendingChartData,
            'spendingTrendData' => $spendingTrendData,
            'recentTransactions' => $recentTransactions,
            'cycleStartDate' => $cycleStartDate,
            'cycleEndDate' => $cycleEndDate,
            'surplusAmount' => 0,
            'showSurplusTooltip' => false,
        ]);
    }
}