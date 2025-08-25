<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

use App\Enums\TransactionType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

class Metrics extends Component
{

    public function render()
    {
        $user = Auth::user();
        $startOfMonth = now()->startOfMonth();

        $netWorth = $user->accounts()->sum('current_balance');

        $incomeThisMonth = $user->transactions()
            ->where('transactions.type', TransactionType::INCOME) 
            ->where('transaction_date', '>=', $startOfMonth)
            ->sum('amount');

        $expensesThisMonth = $user->transactions()
            ->where('transactions.type', TransactionType::EXPENSE) 
            ->where('transaction_date', '>=', $startOfMonth)
            ->sum('amount');

        $spendingByCategory = $user->transactions()
            ->with('category')
            ->where('transactions.type', TransactionType::EXPENSE) 
            ->where('transaction_date', '>=', $startOfMonth)
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $chartData = $spendingByCategory->take(5);
        $otherTotal = $spendingByCategory->skip(5)->sum('total');

        if ($otherTotal > 0) {
            $chartData->push(['category' => ['name' => 'Other'], 'total' => $otherTotal]);
        }

        $spendingChartLabels = $chartData->pluck('category.name');
        $spendingChartValues = $chartData->pluck('total');
        
        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        return view('livewire.dashboard.metrics', [
            'netWorth' => $netWorth,
            'incomeThisMonth' => $incomeThisMonth,
            'expensesThisMonth' => $expensesThisMonth,
            'spendingChartLabels' => $spendingChartLabels,
            'spendingChartValues' => $spendingChartValues,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
