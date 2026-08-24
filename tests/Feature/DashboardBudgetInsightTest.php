<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardBudgetInsightTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_current_month_budget_insight(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $foodCategory = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);
        $transportCategory = $this->category($user, 'Transportasi', Category::TYPE_EXPENSE);
        $unbudgetedCategory = $this->category($user, 'Hiburan', Category::TYPE_EXPENSE);
        $incomeCategory = $this->category($user, 'Gaji', Category::TYPE_INCOME);
        $otherCategory = $this->category($otherUser, 'Makanan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $foodCategory->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 100000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $transportCategory->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 200000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $unbudgetedCategory->id,
            'month' => now()->subMonth()->month,
            'year' => now()->subMonth()->year,
            'amount' => 999000,
        ]);

        $currentMonthDate = now()->startOfMonth()->addDays(5)->toDateString();
        $previousMonthDate = now()->subMonth()->startOfMonth()->addDays(5)->toDateString();

        $this->transaction($user, $foodCategory, Transaction::TYPE_EXPENSE, 125000, $currentMonthDate, 'makan');
        $this->transaction($user, $transportCategory, Transaction::TYPE_EXPENSE, 50000, $currentMonthDate, 'bensin');
        $this->transaction($user, $unbudgetedCategory, Transaction::TYPE_EXPENSE, 999000, $currentMonthDate, 'film');
        $this->transaction($user, $foodCategory, Transaction::TYPE_EXPENSE, 999000, $previousMonthDate, 'bulan lalu');
        $this->transaction($user, $incomeCategory, Transaction::TYPE_INCOME, 999000, $currentMonthDate, 'gaji');
        $this->transaction($otherUser, $otherCategory, Transaction::TYPE_EXPENSE, 999000, $currentMonthDate, 'user lain');
        $deleted = $this->transaction($user, $transportCategory, Transaction::TYPE_EXPENSE, 25000, $currentMonthDate, 'hapus');
        $deleted->delete();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan Anggaran')
            ->assertSee('Lihat anggaran')
            ->assertSee('Total Anggaran')
            ->assertSee('Terpakai')
            ->assertSee('Sisa')
            ->assertSee('Lewat Batas')
            ->assertSee('Rp 300.000')
            ->assertSee('Rp 175.000')
            ->assertSee('Rp 125.000')
            ->assertSee('1 kategori')
            ->assertDontSee('Belum ada anggaran bulan ini.');
    }

    public function test_dashboard_shows_budget_empty_state_when_current_month_has_no_budgets(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $category->id,
            'month' => now()->subMonth()->month,
            'year' => now()->subMonth()->year,
            'amount' => 100000,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada anggaran bulan ini.')
            ->assertSee('Buat anggaran bulan ini untuk mulai memantau batas pengeluaran.')
            ->assertSee('Lihat anggaran')
            ->assertSee('Buat Anggaran');
    }

    private function category(User $user, string $name, string $type): Category
    {
        return $user->categories()->where('name', $name)->where('type', $type)->firstOrFail();
    }

    private function transaction(
        User $user,
        Category $category,
        string $type,
        int $amount,
        string $date,
        string $note
    ): Transaction {
        return Transaction::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $category->id,
            'type' => $type,
            'amount' => $amount,
            'transaction_date' => $date,
            'note' => $note,
            'source' => Transaction::SOURCE_MANUAL,
            'raw_voice_text' => null,
        ]);
    }
}
