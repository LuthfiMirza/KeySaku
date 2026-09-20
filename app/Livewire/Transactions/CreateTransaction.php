<?php

namespace App\Livewire\Transactions;

use App\Models\Category;
use App\Models\Transaction;
use App\Services\CategorySuggester;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateTransaction extends Component
{
    public string $type = Transaction::TYPE_EXPENSE;

    public ?int $category_id = null;
    
    public ?int $account_id = null;

    public string $amount = '';

    public string $transaction_date = '';

    public string $note = '';

    public function mount(): void
    {
        $this->transaction_date = now()->toDateString();
        $this->category_id = $this->categories()->first()?->id;
        $this->account_id = $this->accounts()->first()?->id;
    }

    public function updatedType(): void
    {
        $this->category_id = $this->categories()->first()?->id;
    }

    public function updatedNote(CategorySuggester $suggester): void
    {
        if (trim($this->note) === '') {
            return;
        }

        $suggestion = $suggester->suggest($this->note, $this->type, $this->categories()->get());

        if ($suggestion !== null && $suggestion->confidence >= CategorySuggester::MINIMUM_CONFIDENCE) {
            $this->category_id = $suggestion->categoryId;
        }
    }

    public function save(): void
    {
        $this->amount = $this->normalizeAmount($this->amount);

        $validated = $this->validate([
            'type' => ['required', Rule::in(Transaction::types())],
            'category_id' => ['required', 'integer'],
            'account_id' => ['nullable', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $household = auth()->user()->household;

        $category = $household
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
        
        if (!empty($validated['account_id'])) {
            $account = $household->accounts()->whereKey($validated['account_id'])->first();
            if (! $account) {
                $this->addError('account_id', 'Akun tidak ditemukan.');
                return;
            }
        }

        Transaction::create([
            'user_id' => auth()->id(),
            'household_id' => $household->id,
            'category_id' => $category->id,
            'account_id' => $validated['account_id'] ?: null,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?: null,
            'source' => Transaction::SOURCE_MANUAL,
            'raw_voice_text' => null,
        ]);

        $this->reset(['amount', 'note']);
        $this->category_id = $this->categories()->first()?->id;
        $this->account_id = $this->accounts()->first()?->id;
        $this->resetValidation();

        session()->flash('transaction_status', 'Transaksi berhasil disimpan.');
    }

    public function render(): View
    {
        return view('livewire.transactions.create-transaction', [
            'categories' => $this->categories()->get(),
            'accounts' => $this->accounts()->get(),
        ]);
    }

    private function categories()
    {
        return auth()->user()->household
            ->categories()
            ->where('type', $this->type)
            ->orderBy('name');
    }
    
    private function accounts()
    {
        return auth()->user()->household
            ->accounts()
            ->where('is_active', true)
            ->orderBy('name');
    }

    private function normalizeAmount(string $amount): string
    {
        return preg_replace('/\D+/', '', $amount) ?: '';
    }
}
