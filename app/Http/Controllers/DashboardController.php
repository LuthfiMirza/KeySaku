<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $thisMonthIncome = $user->transactions()
            ->where('type', Transaction::TYPE_INCOME)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $thisMonthExpense = $user->transactions()
            ->where('type', Transaction::TYPE_EXPENSE)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $allTimeIncome = $user->transactions()
            ->where('type', Transaction::TYPE_INCOME)
            ->sum('amount');

        $allTimeExpense = $user->transactions()
            ->where('type', Transaction::TYPE_EXPENSE)
            ->sum('amount');

        $monthlyExpenseByCategory = $user->transactions()
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->where('type', Transaction::TYPE_EXPENSE)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'total' => (float) $row->total,
                'percentage' => $thisMonthExpense > 0
                    ? round(((float) $row->total / (float) $thisMonthExpense) * 100)
                    : 0,
            ]);

        return view('pages.dashboard', [
            'thisMonthIncome' => $thisMonthIncome,
            'thisMonthExpense' => $thisMonthExpense,
            'thisMonthBalance' => $thisMonthIncome - $thisMonthExpense,
            'netBalance' => $allTimeIncome - $allTimeExpense,
            'monthlyExpenseByCategory' => $monthlyExpenseByCategory,
            'latestTransactions' => $user->transactions()
                ->with('category')
                ->latest('transaction_date')
                ->latest('id')
                ->limit(3)
                ->get(),
        ]);
    }
}
