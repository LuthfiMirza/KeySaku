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

        $currentMonthBudgets = $user->budgets()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->get(['category_id', 'amount']);

        $budgetCategoryIds = $currentMonthBudgets->pluck('category_id')->unique()->values();
        $budgetExpenseTotals = $budgetCategoryIds->isEmpty()
            ? collect()
            : $user->transactions()
                ->selectRaw('category_id, SUM(amount) as total')
                ->where('type', Transaction::TYPE_EXPENSE)
                ->whereIn('category_id', $budgetCategoryIds)
                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->groupBy('category_id')
                ->pluck('total', 'category_id');

        $currentMonthBudgetTotal = (float) $currentMonthBudgets->sum('amount');
        $currentMonthBudgetUsed = (float) $currentMonthBudgets->sum(
            fn ($budget) => (float) ($budgetExpenseTotals[$budget->category_id] ?? 0)
        );

        return view('pages.dashboard', [
            'thisMonthIncome' => $thisMonthIncome,
            'thisMonthExpense' => $thisMonthExpense,
            'thisMonthBalance' => $thisMonthIncome - $thisMonthExpense,
            'netBalance' => $allTimeIncome - $allTimeExpense,
            'monthlyExpenseByCategory' => $monthlyExpenseByCategory,
            'currentMonthBudgetInsight' => [
                'hasBudgets' => $currentMonthBudgets->isNotEmpty(),
                'totalBudget' => $currentMonthBudgetTotal,
                'totalUsed' => $currentMonthBudgetUsed,
                'totalRemaining' => $currentMonthBudgetTotal - $currentMonthBudgetUsed,
                'overLimitCount' => $currentMonthBudgets
                    ->filter(fn ($budget) => (float) ($budgetExpenseTotals[$budget->category_id] ?? 0) > (float) $budget->amount)
                    ->count(),
            ],
            'latestTransactions' => $user->transactions()
                ->with('category')
                ->latest('transaction_date')
                ->latest('id')
                ->limit(3)
                ->get(),
        ]);
    }
}
