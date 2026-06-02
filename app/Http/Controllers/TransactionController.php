<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'amount' => $this->normalizeAmount((string) $request->input('amount', '')),
        ]);

        $validated = $request->validate([
            'type' => ['required', Rule::in(Transaction::types())],
            'category_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->seedDefaultCategoriesIfNeeded($request);

        $category = $request->user()
            ->categories()
            ->whereKey($validated['category_id'])
            ->first();

        if (! $category) {
            return back()
                ->withErrors(['category_id' => 'Kategori tidak ditemukan.'])
                ->withInput();
        }

        if ($category->type !== $validated['type']) {
            return back()
                ->withErrors(['category_id' => 'Kategori tidak sesuai dengan jenis transaksi.'])
                ->withInput();
        }

        Transaction::create([
            'user_id' => $request->user()->id,
            'category_id' => $category->id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?: null,
            'source' => Transaction::SOURCE_MANUAL,
            'raw_voice_text' => null,
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('transaction_status', 'Transaksi berhasil disimpan.');
    }

    private function normalizeAmount(string $amount): string
    {
        return preg_replace('/\D+/', '', $amount) ?: '';
    }

    private function seedDefaultCategoriesIfNeeded(Request $request): void
    {
        foreach ($this->defaultCategories() as $category) {
            $request->user()->categories()->firstOrCreate(
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
