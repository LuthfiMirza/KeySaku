<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ManageAccounts extends Component
{
    // Form state
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name     = '';
    public string $type     = Account::TYPE_CASH;
    public string $balance  = '';
    public string $icon     = '';
    public string $color    = '#6366f1';

    // Delete confirmation
    public ?int $confirmDeleteId = null;

    // Status message
    public ?string $statusMessage = null;

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'balance', 'icon']);
        $this->type      = Account::TYPE_CASH;
        $this->color     = '#6366f1';
        $this->showForm  = true;
        $this->statusMessage = null;
    }

    public function openEdit(int $id): void
    {
        $account = $this->scopedAccount($id);
        if (! $account) {
            return;
        }

        $this->editingId = $id;
        $this->name      = $account->name;
        $this->type      = $account->type;
        $this->balance   = (string) $account->balance;
        $this->icon      = (string) ($account->icon ?? '');
        $this->color     = $account->color ?? '#6366f1';
        $this->showForm  = true;
        $this->statusMessage = null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name'    => ['required', 'string', 'max:100'],
            'type'    => ['required', Rule::in(Account::types())],
            'balance' => ['required', 'numeric', 'min:0'],
            'icon'    => ['nullable', 'string', 'max:50'],
            'color'   => ['required', 'string', 'max:20'],
        ]);

        $household = auth()->user()->household;

        if ($this->editingId) {
            $account = $this->scopedAccount($this->editingId);
            if (! $account) {
                return;
            }
            $account->update($validated);
            $this->statusMessage = 'Akun berhasil diperbarui.';
        } else {
            $household->accounts()->create([
                ...$validated,
                'user_id'   => auth()->id(),
                'is_active' => true,
            ]);
            $this->statusMessage = 'Akun berhasil ditambahkan.';
        }

        $this->reset(['showForm', 'editingId', 'name', 'balance', 'icon']);
        $this->type  = Account::TYPE_CASH;
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
            $account->delete();
            $this->statusMessage = 'Akun berhasil dihapus.';
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
        $this->type  = Account::TYPE_CASH;
        $this->color = '#6366f1';
    }

    public function render(): View
    {
        $household = auth()->user()->household;

        return view('livewire.accounts.manage-accounts', [
            'accounts'   => $household->accounts()->orderBy('name')->get(),
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
