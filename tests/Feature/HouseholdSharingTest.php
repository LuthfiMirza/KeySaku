<?php

namespace Tests\Feature;

use App\Livewire\Budgets\ManageBudgets;
use App\Livewire\CategoryManager;
use App\Livewire\Savings\ManageSavingsGoals;
use App\Livewire\Transactions\CreateTransaction;
use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HouseholdSharingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_transaction_logged_by_one_member_is_visible_to_another(): void
    {
        [$owner, $member] = $this->householdOf2();
        $category = $owner->household->categories()->where('type', Category::TYPE_EXPENSE)->first();

        Livewire::actingAs($owner)
            ->test(CreateTransaction::class)
            ->set('category_id', $category->id)
            ->set('amount', '25000')
            ->set('note', 'Dicatat pemilik')
            ->call('save')
            ->assertHasNoErrors();

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dicatat pemilik');
    }

    public function test_a_budget_created_by_one_member_is_visible_to_another(): void
    {
        [$owner, $member] = $this->householdOf2();
        $category = $owner->household->categories()->where('type', Category::TYPE_EXPENSE)->first();

        Budget::create([
            'user_id' => $owner->id,
            'household_id' => $owner->household->id,
            'category_id' => $category->id,
            'month' => (int) now()->month,
            'year' => (int) now()->year,
            'amount' => 500000,
        ]);

        Livewire::actingAs($member)
            ->test(ManageBudgets::class)
            ->assertSee('Rp 500.000');
    }

    public function test_a_savings_goal_created_by_one_member_is_visible_to_another(): void
    {
        [$owner, $member] = $this->householdOf2();

        SavingsGoal::create([
            'user_id' => $owner->id,
            'household_id' => $owner->household->id,
            'name' => 'Dana Darurat Bersama',
            'target_amount' => 1000000,
            'current_amount' => 0,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        Livewire::actingAs($member)
            ->test(ManageSavingsGoals::class)
            ->assertSee('Dana Darurat Bersama');
    }

    public function test_a_category_created_by_one_member_is_editable_by_another(): void
    {
        [$owner, $member] = $this->householdOf2();

        Livewire::actingAs($owner)
            ->test(CategoryManager::class)
            ->set('name', 'Langganan Streaming')
            ->set('type', Category::TYPE_EXPENSE)
            ->call('save');

        $category = $owner->household->categories()->where('name', 'Langganan Streaming')->firstOrFail();

        Livewire::actingAs($member)
            ->test(CategoryManager::class)
            ->call('edit', $category->id)
            ->assertSet('name', 'Langganan Streaming');
    }

    public function test_a_user_from_a_different_household_cannot_see_this_households_budget(): void
    {
        [$owner] = $this->householdOf2();
        $category = $owner->household->categories()->where('type', Category::TYPE_EXPENSE)->first();

        $budget = Budget::create([
            'user_id' => $owner->id,
            'household_id' => $owner->household->id,
            'category_id' => $category->id,
            'month' => (int) now()->month,
            'year' => (int) now()->year,
            'amount' => 500000,
        ]);

        $outsider = User::factory()->create();

        Livewire::actingAs($outsider)
            ->test(ManageBudgets::class)
            ->call('startEdit', $budget->id)
            ->assertStatus(404);
    }

    private function householdOf2(): array
    {
        $owner = User::factory()->create();

        $this->post('/register', [
            'name' => 'Anggota Kedua',
            'email' => 'anggota-kedua-'.uniqid().'@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invite_code' => $owner->household->invite_code,
        ]);

        $member = User::where('name', 'Anggota Kedua')->latest('id')->firstOrFail();

        return [$owner, $member];
    }
}
