<?php

namespace App\Livewire\Dashboard;

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

        $dailySpending = Transaction::query()
            ->whereIn('account_id', $userAccountIds)
            ->where('type', TransactionType::EXPENSE)
            ->whereBetween('transaction_date', [$cycleStartDate, $cycleEndDate])
            ->where('description', 'NOT LIKE', '%Transfer to%')
            ->select(
                DB::raw("DATE(transaction_date) as date"),
                DB::raw("SUM(amount) as total")
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $trendLabels = [];
        $trendValues = [];
        $idealValues = [];
        $surplusAmount = 0;
        $showSurplusTooltip = false;

        if ($lastSalary) {
            $runningBalance = $lastSalary->amount;
            $daysInCycle = $cycleStartDate->diffInDays($cycleEndDate);
            $dailyBurnRate = $lastSalary->amount / $daysInCycle;

            for ($day = $cycleStartDate->copy(); $day <= $cycleEndDate; $day->addDay()) {
                $dateString = $day->format('Y-m-d');
                $trendLabels[] = $day->format('j M');
                $runningBalance -= ($dailySpending[$dateString]->total ?? 0);
                $trendValues[] = $runningBalance;
                $daysSincePayday = $cycleStartDate->diffInDays($day);
                $idealBalance = $lastSalary->amount - ($daysSincePayday * $dailyBurnRate);
                $idealValues[] = max(0, $idealBalance);
            }

            if (now()->isSameDay($cycleEndDate)) {
                $totalCycleExpenses = $dailySpending->sum('total');
                $surplusAmount = $lastSalary->amount - $totalCycleExpenses;
                if ($surplusAmount > 0) {
                    $showSurplusTooltip = true;
                }
            }
        } else {
            $totalExpensesToDate = 0;
            for ($day = $cycleStartDate->copy(); $day <= now(); $day->addDay()) {
                $dateString = $day->format('Y-m-d');
                $trendLabels[] = $day->format('j M');
                $totalExpensesToDate += ($dailySpending[$dateString]->total ?? 0);
                $trendValues[] = $totalExpensesToDate;
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
            'surplusAmount' => $surplusAmount,
            'showSurplusTooltip' => $showSurplusTooltip,
        ]);
    }
}