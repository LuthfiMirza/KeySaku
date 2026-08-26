<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInitialBalancePromptTest extends TestCase
{
    use RefreshDatabase;

    public function test_prompt_appears_when_transactions_exist_but_initial_balance_is_unset(): void
    {
        $user = User::factory()->create();
        $this->transactionFor($user);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Saldo awal belum diatur');
    }

    public function test_prompt_is_hidden_once_an_initial_balance_is_set(): void
    {
        $user = User::factory()->create();
        $user->household->update(['initial_balance' => 500000]);
        $this->transactionFor($user);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Saldo awal belum diatur');
    }

    public function test_prompt_is_hidden_for_a_brand_new_household_with_no_transactions(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Saldo awal belum diatur');
    }

    private function transactionFor(User $user): Transaction
    {
        $category = $user->household->categories()->where('type', Category::TYPE_EXPENSE)->firstOrFail();

        return Transaction::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $category->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 25000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);
    }
}
