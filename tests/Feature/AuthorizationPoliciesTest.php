<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationPoliciesTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_policy_only_allows_the_owner(): void
    {
        [$owner, $other] = $this->twoUsers();
        $category = $owner->categories()->where('type', Category::TYPE_EXPENSE)->first();

        $this->assertTrue($owner->can('view', $category));
        $this->assertTrue($owner->can('update', $category));
        $this->assertTrue($owner->can('delete', $category));
        $this->assertFalse($other->can('view', $category));
        $this->assertFalse($other->can('update', $category));
        $this->assertFalse($other->can('delete', $category));
    }

    public function test_transaction_policy_only_allows_the_owner(): void
    {
        [$owner, $other] = $this->twoUsers();
        $transaction = $this->transactionFor($owner);

        $this->assertTrue($owner->can('view', $transaction));
        $this->assertTrue($owner->can('update', $transaction));
        $this->assertTrue($owner->can('delete', $transaction));
        $this->assertFalse($other->can('view', $transaction));
        $this->assertFalse($other->can('update', $transaction));
        $this->assertFalse($other->can('delete', $transaction));
    }

    public function test_budget_policy_only_allows_the_owner(): void
    {
        [$owner, $other] = $this->twoUsers();
        $budget = $this->budgetFor($owner);

        $this->assertTrue($owner->can('view', $budget));
        $this->assertTrue($owner->can('update', $budget));
        $this->assertTrue($owner->can('delete', $budget));
        $this->assertFalse($other->can('view', $budget));
        $this->assertFalse($other->can('update', $budget));
        $this->assertFalse($other->can('delete', $budget));
    }

    public function test_savings_goal_policy_only_allows_the_owner(): void
    {
        [$owner, $other] = $this->twoUsers();
        $goal = $this->goalFor($owner);

        $this->assertTrue($owner->can('view', $goal));
        $this->assertTrue($owner->can('update', $goal));
        $this->assertTrue($owner->can('delete', $goal));
        $this->assertFalse($other->can('view', $goal));
        $this->assertFalse($other->can('update', $goal));
        $this->assertFalse($other->can('delete', $goal));
    }

    public function test_savings_contribution_policy_only_allows_the_owner(): void
    {
        [$owner, $other] = $this->twoUsers();
        $goal = $this->goalFor($owner);
        $contribution = SavingsContribution::create([
            'savings_goal_id' => $goal->id,
            'user_id' => $owner->id,
            'amount' => 50000,
            'contribution_date' => now()->toDateString(),
        ]);

        $this->assertTrue($owner->can('view', $contribution));
        $this->assertTrue($owner->can('update', $contribution));
        $this->assertTrue($owner->can('delete', $contribution));
        $this->assertFalse($other->can('view', $contribution));
        $this->assertFalse($other->can('update', $contribution));
        $this->assertFalse($other->can('delete', $contribution));
    }

    private function twoUsers(): array
    {
        return [User::factory()->create(), User::factory()->create()];
    }

    private function transactionFor(User $user): Transaction
    {
        $category = $user->categories()->where('type', Category::TYPE_EXPENSE)->firstOrFail();

        return Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 15000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);
    }

    private function budgetFor(User $user): Budget
    {
        $category = $user->categories()->where('type', Category::TYPE_EXPENSE)->firstOrFail();

        return Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => (int) now()->month,
            'year' => (int) now()->year,
            'amount' => 1000000,
        ]);
    }

    private function goalFor(User $user): SavingsGoal
    {
        return SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Liburan',
            'target_amount' => 5000000,
            'current_amount' => 0,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);
    }
}
