<?php

namespace Tests\Feature;

use App\Livewire\Budgets\ManageBudgets;
use App\Livewire\CategoryManager;
use App\Livewire\Savings\ManageSavingsGoals;
use App\Livewire\Transactions\EditTransaction;
use App\Models\Budget;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // === HTTP routes with parameters ===

    public function test_user_a_gets_404_editing_user_b_transaction(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bTransaction = $this->transactionFor($userB);

        $this->actingAs($userA)
            ->get(route('transactions.edit', $bTransaction))
            ->assertNotFound();
    }

    // === Livewire component ID methods ===

    public function test_user_a_cannot_edit_user_b_budget(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bBudget = $this->budgetFor($userB);

        Livewire::actingAs($userA)
            ->test(ManageBudgets::class)
            ->call('startEdit', $bBudget->id)
            ->assertStatus(404);
    }

    public function test_user_a_cannot_update_user_b_budget(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bBudget = $this->budgetFor($userB);

        Livewire::actingAs($userA)
            ->test(ManageBudgets::class)
            ->set('editingBudgetId', $bBudget->id)
            ->set('editAmount', '999999')
            ->call('updateBudget')
            ->assertStatus(404);
    }

    public function test_user_a_cannot_delete_user_b_budget(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bBudget = $this->budgetFor($userB);

        Livewire::actingAs($userA)
            ->test(ManageBudgets::class)
            ->call('deleteBudget', $bBudget->id)
            ->assertStatus(404);

        $this->assertNotNull(Budget::find($bBudget->id));
    }

    public function test_user_a_cannot_edit_user_b_savings_goal(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bGoal = $this->goalFor($userB);

        Livewire::actingAs($userA)
            ->test(ManageSavingsGoals::class)
            ->call('startEditGoal', $bGoal->id)
            ->assertStatus(404);
    }

    public function test_user_a_cannot_archive_user_b_savings_goal(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bGoal = $this->goalFor($userB);

        Livewire::actingAs($userA)
            ->test(ManageSavingsGoals::class)
            ->call('archiveGoal', $bGoal->id)
            ->assertStatus(404);

        $this->assertSame(SavingsGoal::STATUS_ACTIVE, $bGoal->fresh()->status);
    }

    public function test_user_a_cannot_contribute_to_user_b_savings_goal(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bGoal = $this->goalFor($userB);

        Livewire::actingAs($userA)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $bGoal->id)
            ->assertStatus(404);
    }

    public function test_user_a_cannot_mount_edit_transaction_of_user_b(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bTransaction = $this->transactionFor($userB);

        Livewire::actingAs($userA)
            ->test(EditTransaction::class, ['transaction' => $bTransaction])
            ->assertStatus(404);
    }

    public function test_user_a_cannot_edit_user_b_category(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bCategory = $userB->categories()->where('name', 'Makanan')->firstOrFail();

        Livewire::actingAs($userA)
            ->test(CategoryManager::class)
            ->call('edit', $bCategory->id)
            ->assertStatus(404);
    }

    public function test_user_a_cannot_delete_user_b_category(): void
    {
        [$userA, $userB] = $this->twoUsers();
        $bCategory = $userB->categories()->where('name', 'Makanan')->firstOrFail();

        Livewire::actingAs($userA)
            ->test(CategoryManager::class)
            ->call('delete', $bCategory->id)
            ->assertStatus(404);

        $this->assertNotNull($bCategory->fresh());
    }

    // === helpers ===

    private function twoUsers(): array
    {
        return [User::factory()->create(), User::factory()->create()];
    }

    private function transactionFor(User $user): Transaction
    {
        $category = $user->categories()->where('name', 'Makanan')->firstOrFail();

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
        $category = $user->categories()->where('name', 'Makanan')->firstOrFail();

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
