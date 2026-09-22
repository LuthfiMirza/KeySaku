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

        $monthlySummary = (clone $monthlyTransactions)
            ->selectRaw('
                COALESCE(SUM(CASE WHEN type = ? THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = ? THEN amount ELSE 0 END), 0) as total_expense
            ', [Transaction::TYPE_INCOME, Transaction::TYPE_EXPENSE])
            ->first();

        $totalIncome = (float) ($monthlySummary?->total_income ?? 0);
        $totalExpense = (float) ($monthlySummary?->total_expense ?? 0);

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

        $trendStartDate = $selectedMonth->copy()->subMonths(5)->startOfMonth()->toDateString();
        $trendEndDate = $selectedMonth->copy()->endOfMonth()->toDateString();

        $aggregatedTrend = $household->transactions()
            ->whereBetween('transaction_date', [$trendStartDate, $trendEndDate])
            ->selectRaw('
                SUBSTR(transaction_date, 1, 7) as ym,
                SUM(CASE WHEN type = ? THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = ? THEN amount ELSE 0 END) as total_expense
            ', [Transaction::TYPE_INCOME, Transaction::TYPE_EXPENSE])
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        $sixMonthTrend = collect(range(5, 0))->map(function ($i) use ($selectedMonth, $aggregatedTrend) {
            $month = $selectedMonth->copy()->subMonths($i);
            $ym = $month->format('Y-m');
            $row = $aggregatedTrend->get($ym);

            return [
                'label' => $month->translatedFormat('M Y'),
                'income' => (float) ($row?->total_income ?? 0),
                'expense' => (float) ($row?->total_expense ?? 0),
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
