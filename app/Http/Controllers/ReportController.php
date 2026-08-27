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

        $expenseByCategory = (clone $monthlyTransactions)
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->where('type', Transaction::TYPE_EXPENSE)
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn (Transaction $row) => [
                'category' => $row->category,
                'total' => (float) $row->total,
                'percentage' => $totalExpense > 0
                    ? round(((float) $row->total / $totalExpense) * 100)
                    : 0,
            ]);

        $latestTransactions = (clone $monthlyTransactions)
            ->with('category')
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5)
            ->get();

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
        ]);
    }

    private function selectedMonth(mixed $month): Carbon
    {
        if (is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            try {
                return Carbon::createFromFormat('Y-m', $month, date_default_timezone_get())->startOfMonth();
            } catch (\Throwable) {
            }
        }

        return now()->startOfMonth();
    }
}
