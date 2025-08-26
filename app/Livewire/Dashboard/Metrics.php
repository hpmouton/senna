<?php

namespace App\Livewire\Dashboard;

use App\Models\Transaction;
use Livewire\Component;

use App\Enums\TransactionType;
use Illuminate\Support\Facades\Auth;

class Metrics extends Component
{

    public function render()
    {
        
        $user = Auth::user();
        $userAccountIds = $user->accounts()->pluck('id');
        $startOfMonth = now()->startOfMonth();

        // 1. Key Metrics
        $netWorth = $user->accounts()->sum('current_balance');

        $incomeThisMonth = Transaction::query()
            ->whereIn('account_id', $userAccountIds)
            ->where('type', TransactionType::INCOME)
            ->where('transaction_date', '>=', $startOfMonth)
            ->sum('amount');

        $expensesThisMonth = Transaction::query()
            ->whereIn('account_id', $userAccountIds)
            ->where('type', TransactionType::EXPENSE)
            ->where('transaction_date', '>=', $startOfMonth)
            ->sum('amount');

        // 2. Data for Spending Chart
        $spendingByCategory = Transaction::query()
            ->with('category')
            ->whereIn('account_id', $userAccountIds)
            ->where('type', TransactionType::EXPENSE)
            ->where('transaction_date', '>=', $startOfMonth)
            ->whereNotNull('category_id')
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $chartDataCollection = $spendingByCategory->take(5);
        $otherTotal = $spendingByCategory->skip(5)->sum('total');

        if ($otherTotal > 0) {
            $chartDataCollection->push((object)['category' => (object)['name' => 'Other'], 'total' => $otherTotal]);
        }

        $spendingChartData = [
            'labels' => $chartDataCollection->pluck('category.name')->toArray(),
            'values' => $chartDataCollection->pluck('total')->toArray(),
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
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
