<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $selectedMonth = $this->selectedMonth($request->query('month'));
        $startDate = $selectedMonth->copy()->startOfMonth();
        $endDate = $selectedMonth->copy()->endOfMonth();
        $household = $request->user()->household;

        $monthlyTransactions = $household->transactions()
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()]);

        $totalIncome = (float) (clone $monthlyTransactions)
            ->where('type', Transaction::TYPE_INCOME)
            ->sum('amount');

        $totalExpense = (float) (clone $monthlyTransactions)
            ->where('type', Transaction::TYPE_EXPENSE)
            ->sum('amount');

        $colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16'];

        $expenseByCategory = (clone $monthlyTransactions)
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->where('type', Transaction::TYPE_EXPENSE)
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get()
            ->values()
            ->map(fn (Transaction $row, int $index) => [
                'category' => $row->category,
                'total' => (float) $row->total,
                'percentage' => $totalExpense > 0
                    ? round(((float) $row->total / $totalExpense) * 100)
                    : 0,
                'color' => $colors[$index % count($colors)],
            ]);

        $latestTransactions = (clone $monthlyTransactions)
            ->with('category')
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5)
            ->get();

        $sixMonthTrend = collect(range(5, 0))->map(function ($i) use ($selectedMonth, $household) {
            $month = $selectedMonth->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $transactions = $household->transactions()
                ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
                ->get();

            return [
                'label' => $month->translatedFormat('M Y'),
                'income' => (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount'),
                'expense' => (float) $transactions->where('type', Transaction::TYPE_EXPENSE)->sum('amount'),
            ];
        });

        return view('pages.reports.index', [
            'selectedMonth' => $selectedMonth,
            'selectedMonthValue' => $selectedMonth->format('Y-m'),
            'selectedMonthLabel' => $selectedMonth->translatedFormat('F Y'),
            'previousMonthValue' => $selectedMonth->copy()->subMonth()->format('Y-m'),
            'nextMonthValue' => $selectedMonth->copy()->addMonth()->format('Y-m'),
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'monthlyBalance' => $totalIncome - $totalExpense,
            'expenseByCategory' => $expenseByCategory,
            'latestTransactions' => $latestTransactions,
            'sixMonthTrend' => $sixMonthTrend,
        ]);
    }

    private function selectedMonth(mixed $month): Carbon
    {
        if (is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            try {
                return Carbon::createFromFormat('Y-m', $month, config('app.timezone'))->startOfMonth();
            } catch (\Throwable) {
            }
        }

        return now()->startOfMonth();
    }
}
