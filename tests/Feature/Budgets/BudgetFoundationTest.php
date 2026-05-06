<?php

namespace Tests\Feature\Budgets;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BudgetFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_budgets_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('budgets', [
            'id',
            'user_id',
            'category_id',
            'month',
            'year',
            'amount',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_budget_relationships_are_available(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Makanan',
            'type' => Category::TYPE_EXPENSE,
            'color' => '#f97316',
            'icon' => '🍜',
        ]);

        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 1500000,
        ]);

        $this->assertTrue($user->budgets->contains($budget));
        $this->assertTrue($category->budgets->contains($budget));
        $this->assertTrue($budget->user->is($user));
        $this->assertTrue($budget->category->is($category));
        $this->assertSame('1500000.00', $budget->amount);
        $this->assertSame(5, $budget->month);
        $this->assertSame(2026, $budget->year);
    }

    public function test_budget_page_is_authenticated_and_shows_skeleton(): void
    {
        $this->get(route('budgets.index'))
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('budgets.index'))
            ->assertOk()
            ->assertSee('Anggaran Bulanan')
            ->assertSee('Atur batas pengeluaran per kategori.')
            ->assertSee('Belum ada anggaran untuk bulan ini.')
            ->assertSee('Buat anggaran per kategori agar KeySaku bisa memantau batas pengeluaranmu.')
            ->assertSee('Buat Anggaran');
    }
}
