<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ManageAccounts extends Component
{
    // Selected cardholder state
    public ?int $selectedAccountId = null;

    // Form state
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $type = Account::TYPE_CASH;

    public string $balance = '';

    public string $icon = '';

    public string $color = '#6366f1';

    // Delete confirmation
    public ?int $confirmDeleteId = null;

    // Status message
    public ?string $statusMessage = null;

    public function mount(?int $accountId = null): void
    {
        $household = auth()->user()?->household;
        if (! $household) {
            return;
        }

        $targetId = $accountId ?: (int) request()->query('account_id');
        if ($targetId && $household->accounts()->where('id', $targetId)->exists()) {
            $this->selectedAccountId = $targetId;
        } else {
            $this->selectedAccountId = $household->accounts()->where('is_active', true)->first()?->id
                ?? $household->accounts()->first()?->id;
        }
    }

    public function selectAccount(int $id): void
    {
        if ($this->scopedAccount($id)) {
            $this->selectedAccountId = $id;
        }
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'balance', 'icon']);
        $this->type = Account::TYPE_CASH;
        $this->color = '#6366f1';
        $this->showForm = true;
        $this->statusMessage = null;
    }

    public function openEdit(int $id): void
    {
        $account = $this->scopedAccount($id);
        if (! $account) {
            return;
        }

        $this->editingId = $id;
        $this->name = $account->name;
        $this->type = $account->type;
        $this->balance = (string) $account->balance;
        $this->icon = (string) ($account->icon ?? '');
        $this->color = $account->color ?? '#6366f1';
        $this->showForm = true;
        $this->statusMessage = null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(Account::types())],
            'balance' => ['required', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:20'],
        ]);

        $household = auth()->user()->household;

        if ($this->editingId) {
            $account = $this->scopedAccount($this->editingId);
            if (! $account) {
                return;
            }
            $account->update($validated);
            $this->selectedAccountId = $account->id;
            $this->statusMessage = 'Akun berhasil diperbarui.';
        } else {
            $newAccount = $household->accounts()->create([
                ...$validated,
                'user_id' => auth()->id(),
                'is_active' => true,
            ]);
            $this->selectedAccountId = $newAccount->id;
            $this->statusMessage = 'Akun berhasil ditambahkan.';
        }

        $this->reset(['showForm', 'editingId', 'name', 'balance', 'icon']);
        $this->type = Account::TYPE_CASH;
        $this->color = '#6366f1';
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function delete(): void
    {
        if (! $this->confirmDeleteId) {
            return;
        }

        $account = $this->scopedAccount($this->confirmDeleteId);
        if ($account) {
            $deletedId = $account->id;
            $account->delete();
            $this->statusMessage = 'Akun berhasil dihapus.';

            if ($this->selectedAccountId === $deletedId) {
                $household = auth()->user()->household;
                $this->selectedAccountId = $household->accounts()->where('is_active', true)->first()?->id
                    ?? $household->accounts()->first()?->id;
            }
        }

        $this->confirmDeleteId = null;
    }

    public function toggleActive(int $id): void
    {
        $account = $this->scopedAccount($id);
        if ($account) {
            $account->update(['is_active' => ! $account->is_active]);
        }
    }

    public function cancelForm(): void
    {
        $this->reset(['showForm', 'editingId', 'name', 'balance', 'icon']);
        $this->type = Account::TYPE_CASH;
        $this->color = '#6366f1';
    }

    public function render(): View
    {
        $household = auth()->user()->household;
        $accounts = $household->accounts()->orderBy('name')->get();

        if (! $this->selectedAccountId && $accounts->isNotEmpty()) {
            $this->selectedAccountId = $accounts->firstWhere('is_active', true)?->id
                ?? $accounts->first()?->id;
        }

        $selectedAccount = $accounts->firstWhere('id', $this->selectedAccountId) ?? $accounts->first();

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $selectedMonthlyIncome = 0.0;
        $selectedMonthlyExpense = 0.0;
        $selectedTransactions = collect();

        if ($selectedAccount) {
            $selectedTransactions = $selectedAccount->transactions()
                ->with('category')
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->take(6)
                ->get();

            $selectedMonthlyIncome = (float) $selectedAccount->transactions()
                ->where('type', Transaction::TYPE_INCOME)
                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $selectedMonthlyExpense = (float) $selectedAccount->transactions()
                ->where('type', Transaction::TYPE_EXPENSE)
                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
        }

        $totalBalance = (float) $accounts->where('is_active', true)->sum('balance');
        $activeCount = $accounts->where('is_active', true)->count();

        return view('livewire.accounts.manage-accounts', [
            'accounts' => $accounts,
            'selectedAccount' => $selectedAccount,
            'selectedTransactions' => $selectedTransactions,
            'selectedMonthlyIncome' => $selectedMonthlyIncome,
            'selectedMonthlyExpense' => $selectedMonthlyExpense,
            'totalBalance' => $totalBalance,
            'activeCount' => $activeCount,
            'typeLabels' => collect(Account::types())->mapWithKeys(
                fn (string $t) => [$t => Account::typeLabel($t)]
            ),
        ]);
    }

    private function scopedAccount(int $id): ?Account
    {
        $household = auth()->user()->household;

        return $household->accounts()->find($id);
    }
}
