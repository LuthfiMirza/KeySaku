<?php

namespace App\Livewire\Transactions;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateTransaction extends Component
{
    public string $type = Transaction::TYPE_EXPENSE;

    public ?int $category_id = null;

    public string $amount = '';

    public string $transaction_date = '';

    public string $note = '';

    public function mount(): void
    {
        $this->seedDefaultsIfEmpty();

        $this->transaction_date = now()->toDateString();
        $this->category_id = $this->categories()->first()?->id;
    }

    public function updatedType(): void
    {
        $this->category_id = $this->categories()->first()?->id;
    }

    public function save()
    {
        $this->amount = $this->normalizeAmount($this->amount);

        $validated = $this->validate([
            'type' => ['required', Rule::in(Transaction::types())],
            'category_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $category = auth()->user()
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

        Transaction::create([
            'user_id' => auth()->id(),
            'category_id' => $category->id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?: null,
            'source' => Transaction::SOURCE_MANUAL,
            'raw_voice_text' => null,
        ]);

        $this->reset(['amount', 'note']);
        $this->category_id = $this->categories()->first()?->id;
        $this->resetValidation();

        return redirect()
            ->route('transactions.index')
            ->with('transaction_status', 'Transaksi berhasil disimpan.');
    }

    public function render(): View
    {
        return view('livewire.transactions.create-transaction', [
            'categories' => $this->categories()->get(),
        ]);
    }

    private function categories()
    {
        return auth()->user()
            ->categories()
            ->where('type', $this->type)
            ->orderBy('name');
    }

    private function normalizeAmount(string $amount): string
    {
        return preg_replace('/\D+/', '', $amount) ?: '';
    }

    private function seedDefaultsIfEmpty(): void
    {
        foreach ($this->defaultCategories() as $category) {
            auth()->user()->categories()->firstOrCreate(
                [
                    'type' => $category['type'],
                    'name' => $category['name'],
                ],
                [
                    'color' => $category['color'],
                    'icon' => $category['icon'],
                ]
            );
        }
    }

    private function defaultCategories(): array
    {
        return [
            ['name' => 'Makanan', 'type' => Category::TYPE_EXPENSE, 'color' => '#f97316', 'icon' => '🍜'],
            ['name' => 'Transportasi', 'type' => Category::TYPE_EXPENSE, 'color' => '#3b82f6', 'icon' => '🚗'],
            ['name' => 'Belanja Dapur', 'type' => Category::TYPE_EXPENSE, 'color' => '#22c55e', 'icon' => '🛒'],
            ['name' => 'Tagihan', 'type' => Category::TYPE_EXPENSE, 'color' => '#ef4444', 'icon' => '🧾'],
            ['name' => 'Kesehatan', 'type' => Category::TYPE_EXPENSE, 'color' => '#14b8a6', 'icon' => '🏥'],
            ['name' => 'Pendidikan', 'type' => Category::TYPE_EXPENSE, 'color' => '#8b5cf6', 'icon' => '🎓'],
            ['name' => 'Hiburan', 'type' => Category::TYPE_EXPENSE, 'color' => '#ec4899', 'icon' => '🎮'],
            ['name' => 'Lainnya', 'type' => Category::TYPE_EXPENSE, 'color' => '#64748b', 'icon' => '📦'],
            ['name' => 'Gaji', 'type' => Category::TYPE_INCOME, 'color' => '#10b981', 'icon' => '💼'],
            ['name' => 'Bonus', 'type' => Category::TYPE_INCOME, 'color' => '#f59e0b', 'icon' => '🎁'],
            ['name' => 'Usaha', 'type' => Category::TYPE_INCOME, 'color' => '#06b6d4', 'icon' => '🏪'],
            ['name' => 'Hadiah', 'type' => Category::TYPE_INCOME, 'color' => '#d946ef', 'icon' => '🎉'],
            ['name' => 'Lainnya', 'type' => Category::TYPE_INCOME, 'color' => '#64748b', 'icon' => '💰'],
        ];
    }
}
