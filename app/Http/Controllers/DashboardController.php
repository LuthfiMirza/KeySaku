<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $household = auth()->user()->household;
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $thisMonthIncome = $household->transactions()
            ->where('type', Transaction::TYPE_INCOME)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $thisMonthExpense = $household->transactions()
            ->where('type', Transaction::TYPE_EXPENSE)
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $allTimeIncome = $household->transactions()
            ->where('type', Transaction::TYPE_INCOME)
            ->sum('amount');

        $allTimeExpense = $household->transactions()
            ->where('type', Transaction::TYPE_EXPENSE)
            ->sum('amount');

        $monthlyExpenseByCategory = $household->transactions()
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

        $currentMonthBudgets = $household->budgets()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->get(['category_id', 'amount']);

        $budgetCategoryIds = $currentMonthBudgets->pluck('category_id')->unique()->values();
        $budgetExpenseTotals = $budgetCategoryIds->isEmpty()
            ? collect()
            : $household->transactions()
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

        $activeSavingsGoals = $household->savingsGoals()
            ->where('status', SavingsGoal::STATUS_ACTIVE)
            ->latest('id')
            ->get();

        $totalSavingsTarget = (float) $activeSavingsGoals->sum('target_amount');
        $totalSavingsCurrent = (float) $activeSavingsGoals->sum('current_amount');
        $savingsProgressPercentage = $totalSavingsTarget > 0
            ? round(($totalSavingsCurrent / $totalSavingsTarget) * 100)
            : 0;

        $topSavingsGoals = $activeSavingsGoals
            ->map(function ($goal) {
                $progressPercentage = (float) $goal->target_amount > 0
                    ? round(((float) $goal->current_amount / (float) $goal->target_amount) * 100)
                    : 0;

                return [
                    'goal' => $goal,
                    'progressPercentage' => $progressPercentage,
                    'barPercentage' => min(100, max(0, $progressPercentage)),
                ];
            })
            ->sortByDesc('progressPercentage')
            ->take(3)
            ->values();

        $netBalance = (float) $household->initial_balance + $allTimeIncome - $allTimeExpense;

        return view('pages.dashboard', [
            'thisMonthIncome' => $thisMonthIncome,
            'thisMonthExpense' => $thisMonthExpense,
            'thisMonthBalance' => $thisMonthIncome - $thisMonthExpense,
            'netBalance' => $netBalance,
            'availableBalance' => $netBalance - $totalSavingsCurrent,
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
            'savingsGoalInsight' => [
                'hasGoals' => $activeSavingsGoals->isNotEmpty(),
                'activeSavingsGoalCount' => $activeSavingsGoals->count(),
                'totalSavingsTarget' => $totalSavingsTarget,
                'totalSavingsCurrent' => $totalSavingsCurrent,
                'savingsProgressPercentage' => $savingsProgressPercentage,
                'barPercentage' => min(100, max(0, $savingsProgressPercentage)),
                'topSavingsGoals' => $topSavingsGoals,
            ],
            'latestTransactions' => $household->transactions()
                ->with('category')
                ->latest('transaction_date')
                ->latest('id')
                ->limit(3)
                ->get(),
        ]);
    }
}
