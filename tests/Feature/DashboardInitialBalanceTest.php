<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInitialBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_net_balance_includes_initial_balance(): void
    {
        $user = User::factory()->create(['initial_balance' => 12000000]);
        $category = $user->categories()->where('type', Category::TYPE_EXPENSE)->firstOrFail();

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 25000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 11.975.000');
    }

    public function test_available_balance_excludes_earmarked_savings(): void
    {
        $user = User::factory()->create(['initial_balance' => 1000000]);

        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Dana Darurat',
            'target_amount' => 2000000,
            'current_amount' => 300000,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Saldo Tersedia')
            ->assertSee('Rp 1.000.000')
            ->assertSee('Rp 700.000');
    }
}
