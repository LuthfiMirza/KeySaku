<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditTransaction extends Component
{
    public Transaction $transaction;

    public string $type = Transaction::TYPE_EXPENSE;

    public ?int $category_id = null;

    public string $amount = '';

    public string $transaction_date = '';

    public string $note = '';

    public function mount(Transaction $transaction): void
    {
        abort_unless(auth()->user()->can('view', $transaction), 404);

        $this->transaction = $transaction;
        $this->type = $transaction->type;
        $this->category_id = $transaction->category_id;
        $this->amount = (string) $transaction->amount;
        $this->transaction_date = $transaction->transaction_date->toDateString();
        $this->note = $transaction->note ?? '';
    }

    public function updatedType(): void
    {
        $this->category_id = $this->categories()->first()?->id;
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can('update', $this->transaction), 404);

        $validated = $this->validate([
            'type' => ['required', Rule::in(Transaction::types())],
            'category_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $category = auth()->user()->household
            ->categories()
            ->whereKey($validated['category_id'])
            ->first();

        if (! $category) {
            $this->addError('category_id', 'Kategori tidak ditemukan.');
            return;
        }

        if ($category->type !== $validated['type']) {
            $this->addError('category_id', 'Kategori tidak sesuai dengan jenis transaksi.');
            return;
        }

        $this->transaction->update([
            'category_id' => $category->id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?: null,
        ]);

        session()->flash('transaction_status', 'Perubahan transaksi berhasil disimpan.');
    }

    public function delete()
    {
        abort_unless(auth()->user()->can('delete', $this->transaction), 404);

        $this->transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with('transaction_status', 'Transaksi berhasil dihapus dari riwayat.');
    }

    public function render(): View
    {
        return view('livewire.transactions.edit-transaction', [
            'categories' => $this->categories()->get(),
        ]);
    }

    private function categories()
    {
        return auth()->user()->household
            ->categories()
            ->where('type', $this->type)
            ->orderBy('name');
    }
}
