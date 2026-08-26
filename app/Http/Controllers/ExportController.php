<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyReportExport;
use App\Exports\TransactionsExport;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function transactionsCsv(Request $request)
    {
        return Excel::download(
            new TransactionsExport($request->user()->household),
            'transaksi-'.now()->format('Y-m-d').'.csv'
        );
    }

    public function transactionsXlsx(Request $request)
    {
        return Excel::download(
            new TransactionsExport($request->user()->household),
            'transaksi-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function monthlyReportCsv(Request $request)
    {
        $household = $request->user()->household;
        $month = $this->selectedMonth($request->query('month'));

        return Excel::download(
            new MonthlyReportExport($household, $month),
            'laporan-'.$month->format('Y-m').'.csv'
        );
    }

    public function monthlyReportXlsx(Request $request)
    {
        $household = $request->user()->household;
        $month = $this->selectedMonth($request->query('month'));

        return Excel::download(
            new MonthlyReportExport($household, $month),
            'laporan-'.$month->format('Y-m').'.xlsx'
        );
    }

    public function monthlyReportPdf(Request $request): Response
    {
        $household = $request->user()->household;
        $month = $this->selectedMonth($request->query('month'));
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

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

        $transactions = (clone $monthlyTransactions)
            ->with('category')
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView('exports.monthly-report-pdf', [
            'household' => $household,
            'monthLabel' => $month->translatedFormat('F Y'),
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'expenseByCategory' => $expenseByCategory,
            'transactions' => $transactions,
        ]);

        return $pdf->download('laporan-'.$month->format('Y-m').'.pdf');
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
