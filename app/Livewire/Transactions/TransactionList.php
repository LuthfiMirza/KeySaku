<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class TransactionList extends Component
{
    public string $month = '';

    public string $type = 'all';

    public string $categoryId = 'all';

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function updatedType(): void
    {
        $this->categoryId = 'all';
    }

    public function render(): View
    {
        return view('livewire.transactions.transaction-list', [
            'transactions' => $this->transactions(),
            'categories' => $this->categories(),
        ]);
    }

    private function transactions()
    {
        $query = auth()->user()
            ->transactions()
            ->with('category')
            ->latest('transaction_date')
            ->latest('id');

        if ($this->month) {
            $date = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
            $query->whereBetween('transaction_date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ]);
        }

        if (in_array($this->type, Transaction::types(), true)) {
            $query->where('type', $this->type);
        }

        if ($this->categoryId !== 'all') {
            $query->where('category_id', $this->categoryId);
        }

        return $query->get();
    }

    private function categories()
    {
        $query = auth()->user()->categories()->orderBy('name');

        if (in_array($this->type, Transaction::types(), true)) {
            $query->where('type', $this->type);
        }

        return $query->get();
    }
}
