<?php

namespace App\Livewire\Budgets;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ManageBudgets extends Component
{
    public int $month;

    public int $year;

    public ?int $category_id = null;

    public string $amount = '';

    public ?int $editingBudgetId = null;

    public string $editAmount = '';

    public function mount(): void
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
        $this->category_id = $this->expenseCategories()->first()?->id;
    }

    public function updatedMonth(): void
    {
        $this->resetValidation();
    }

    public function updatedYear(): void
    {
        $this->resetValidation();
    }

    public function startEdit(int $budgetId): void
    {
        $budget = auth()->user()->household
            ->budgets()
            ->whereKey($budgetId)
            ->first();

        abort_if(! $budget, 404);

        $this->editingBudgetId = $budget->id;
        $this->editAmount = (string) (float) $budget->amount;
        $this->resetValidation('editAmount');
    }

    public function cancelEdit(): void
    {
        $this->editingBudgetId = null;
        $this->editAmount = '';
        $this->resetValidation('editAmount');
    }

    public function updateBudget(): void
    {
        $validated = $this->validate([
            'editingBudgetId' => ['required', 'integer'],
            'editAmount' => ['required', 'numeric', 'gt:0'],
        ], [
            'editAmount.required' => 'Nominal anggaran wajib diisi.',
            'editAmount.numeric' => 'Nominal anggaran harus berupa angka.',
            'editAmount.gt' => 'Nominal anggaran harus lebih dari 0.',
        ]);

        $budget = auth()->user()->household
            ->budgets()
            ->whereKey($validated['editingBudgetId'])
            ->first();

        abort_if(! $budget, 404);

        $budget->update([
            'amount' => $validated['editAmount'],
        ]);

        $this->cancelEdit();

        session()->flash('budget_status', 'Anggaran berhasil diperbarui.');
    }

    public function deleteBudget(int $budgetId): void
    {
        $budget = auth()->user()->household
            ->budgets()
            ->whereKey($budgetId)
            ->first();

        abort_if(! $budget, 404);

        $budget->delete();

        if ($this->editingBudgetId === $budgetId) {
            $this->cancelEdit();
        }

        session()->flash('budget_status', 'Anggaran berhasil dihapus.');
    }

    public function copyPreviousMonthBudgets(): void
    {
        $targetDate = Carbon::create($this->year, $this->month, 1);
        $sourceDate = $targetDate->copy()->subMonthNoOverflow();

        $household = auth()->user()->household;

        $sourceBudgets = $household
            ->budgets()
            ->where('month', $sourceDate->month)
            ->where('year', $sourceDate->year)
            ->orderBy('id')
            ->get();

        if ($sourceBudgets->isEmpty()) {
            session()->flash('budget_status', 'Tidak ada anggaran bulan lalu untuk disalin.');
            return;
        }

        $targetCategoryIds = $household
            ->budgets()
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->pluck('category_id')
            ->all();

        $copiedCount = 0;

        foreach ($sourceBudgets as $sourceBudget) {
            if (in_array($sourceBudget->category_id, $targetCategoryIds, true)) {
                continue;
            }

            Budget::create([
                'user_id' => auth()->id(),
                'household_id' => $household->id,
                'category_id' => $sourceBudget->category_id,
                'month' => $this->month,
                'year' => $this->year,
                'amount' => $sourceBudget->amount,
            ]);

            $targetCategoryIds[] = $sourceBudget->category_id;
            $copiedCount++;
        }

        if ($copiedCount === 0) {
            session()->flash('budget_status', 'Semua kategori dari bulan lalu sudah punya anggaran bulan ini.');
            return;
        }

        session()->flash('budget_status', $copiedCount.' anggaran berhasil disalin dari bulan lalu.');
    }

    public function save(): void
    {
        $currentYear = (int) now()->year;

        $validated = $this->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:'.($currentYear - 5).','.($currentYear + 5)],
            'category_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ], [
            'month.required' => 'Bulan wajib dipilih.',
            'month.between' => 'Bulan tidak valid.',
            'year.required' => 'Tahun wajib diisi.',
            'year.between' => 'Tahun anggaran tidak valid.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'amount.required' => 'Nominal anggaran wajib diisi.',
            'amount.numeric' => 'Nominal anggaran harus berupa angka.',
            'amount.gt' => 'Nominal anggaran harus lebih dari 0.',
        ]);

        $household = auth()->user()->household;

        $category = $household
            ->categories()
            ->whereKey($validated['category_id'])
            ->first();

        if (! $category || $category->type !== Category::TYPE_EXPENSE) {
            $this->addError('category_id', 'Kategori pengeluaran tidak ditemukan.');
            return;
        }

        $exists = $household
            ->budgets()
            ->where('category_id', $category->id)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            $this->addError('category_id', 'Anggaran untuk kategori ini pada bulan tersebut sudah ada.');
            return;
        }

        Budget::create([
            'user_id' => auth()->id(),
            'household_id' => $household->id,
            'category_id' => $category->id,
            'month' => $validated['month'],
            'year' => $validated['year'],
            'amount' => $validated['amount'],
        ]);

        $this->amount = '';
        $this->category_id = $this->expenseCategories()->first()?->id;
        $this->resetValidation();

        session()->flash('budget_status', 'Anggaran berhasil disimpan.');
    }

    public function render(): View
    {
        $budgets = $this->budgetsWithProgress();

        return view('livewire.budgets.manage-budgets', [
            'expenseCategories' => $this->expenseCategories()->get(),
            'budgets' => $budgets,
            'summary' => $this->summaryForBudgets($budgets),
            'selectedMonthLabel' => now()->setDate($this->year, $this->month, 1)->translatedFormat('F Y'),
        ]);
    }

    private function expenseCategories()
    {
        return auth()->user()->household
            ->categories()
            ->where('type', Category::TYPE_EXPENSE)
            ->orderBy('name');
    }

    private function budgets()
    {
        return auth()->user()->household
            ->budgets()
            ->with('category')
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->orderByDesc('amount')
            ->orderBy('id');
    }

    private function budgetsWithProgress()
    {
        $budgets = $this->budgets()->get();
        $expenseTotals = $this->expenseTotalsByCategory();

        return $budgets->map(function (Budget $budget) use ($expenseTotals) {
            $budgetAmount = (float) $budget->amount;
            $usedAmount = (float) ($expenseTotals[$budget->category_id] ?? 0);
            $remainingAmount = $budgetAmount - $usedAmount;
            $percentage = $budgetAmount > 0 ? round(($usedAmount / $budgetAmount) * 100) : 0;
            $status = $this->statusForPercentage($percentage);

            return [
                'budget' => $budget,
                'amount' => $budgetAmount,
                'used' => $usedAmount,
                'remaining' => $remainingAmount,
                'percentage' => $percentage,
                'barPercentage' => min(100, max(0, $percentage)),
                'statusLabel' => $status['label'],
                'statusClass' => $status['class'],
                'barClass' => $status['bar'],
            ];
        });
    }

    private function summaryForBudgets($budgets): array
    {
        $totalBudget = (float) $budgets->sum('amount');
        $totalUsed = (float) $budgets->sum('used');
        $totalRemaining = $totalBudget - $totalUsed;

        return [
            'totalBudget' => $totalBudget,
            'totalUsed' => $totalUsed,
            'totalRemaining' => $totalRemaining,
            'overLimitCount' => $budgets->filter(fn (array $item) => $item['used'] > $item['amount'])->count(),
        ];
    }

    private function expenseTotalsByCategory()
    {
        $date = Carbon::create($this->year, $this->month, 1);
        $budgetCategoryIds = $this->budgets()
            ->pluck('category_id')
            ->unique()
            ->values();

        if ($budgetCategoryIds->isEmpty()) {
            return collect();
        }

        return auth()->user()->household
            ->transactions()
            ->selectRaw('category_id, SUM(amount) as total')
            ->where('type', Transaction::TYPE_EXPENSE)
            ->whereIn('category_id', $budgetCategoryIds)
            ->whereBetween('transaction_date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ])
            ->groupBy('category_id')
            ->pluck('total', 'category_id');
    }

    private function statusForPercentage(float|int $percentage): array
    {
        if ($percentage > 100) {
            return [
                'label' => 'Lewat Batas',
                'class' => 'bg-rose-50 text-rose-600',
                'bar' => 'bg-gradient-to-r from-rose-500 to-pink-600',
            ];
        }

        if ($percentage >= 80) {
            return [
                'label' => 'Mendekati Batas',
                'class' => 'bg-amber-50 text-amber-600',
                'bar' => 'bg-gradient-to-r from-amber-400 to-orange-500',
            ];
        }

        return [
            'label' => 'Aman',
            'class' => 'bg-emerald-50 text-emerald-600',
            'bar' => 'bg-gradient-to-r from-emerald-400 to-teal-500',
        ];
    }
}
