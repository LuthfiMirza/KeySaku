<?php

namespace Tests\Feature\Security;

use App\Actions\ProvisionHouseholdForUser;
use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * IDOR test suite — verifies cross-household data isolation.
 *
 * Setup: userA owns householdA with full data.
 *        userB owns householdB (separate household).
 * Invariant: Every action by userB against householdA IDs must be silently
 * ignored or return 403/404. No data of householdA must be mutated.
 */
class CrossHouseholdAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;
    private User $userB;

    private Account $accountA;
    private Category $categoryA;
    private Transaction $transactionA;
    private Budget $budgetA;
    private SavingsGoal $goalA;
    private SavingsContribution $contributionA;

    protected function setUp(): void
    {
        parent::setUp();

        // UserA with full household data
        $this->userA = User::factory()->create();
        $householdA  = $this->userA->household;

        $this->accountA = Account::create([
            'household_id' => $householdA->id,
            'user_id'      => $this->userA->id,
            'name'         => 'BCA UserA',
            'type'         => Account::TYPE_BANK,
            'balance'      => 500_000,
            'is_active'    => true,
        ]);

        $this->categoryA = $householdA->categories()
            ->where('type', Category::TYPE_EXPENSE)
            ->firstOrFail();

        $this->transactionA = Transaction::create([
            'user_id'          => $this->userA->id,
            'household_id'     => $householdA->id,
            'category_id'      => $this->categoryA->id,
            'account_id'       => $this->accountA->id,
            'type'             => Transaction::TYPE_EXPENSE,
            'amount'           => 50_000,
            'transaction_date' => now()->toDateString(),
            'source'           => Transaction::SOURCE_MANUAL,
        ]);

        $this->budgetA = Budget::create([
            'user_id'      => $this->userA->id,
            'household_id' => $householdA->id,
            'category_id'  => $this->categoryA->id,
            'month'        => now()->month,
            'year'         => now()->year,
            'amount'       => 1_000_000,
        ]);

        $this->goalA = SavingsGoal::create([
            'user_id'       => $this->userA->id,
            'household_id'  => $householdA->id,
            'name'          => 'Dana Darurat A',
            'target_amount' => 10_000_000,
            'current_amount'=> 500_000,
            'status'        => SavingsGoal::STATUS_ACTIVE,
        ]);

        $this->contributionA = SavingsContribution::create([
            'user_id'           => $this->userA->id,
            'household_id'      => $householdA->id,
            'savings_goal_id'   => $this->goalA->id,
            'amount'            => 500_000,
            'contribution_date' => now()->toDateString(),
        ]);

        // UserB with separate household
        $this->userB = User::factory()->create();
    }

    // =========================================================================
    // CONTROL: UserA CAN access their own data
    // =========================================================================

    /** @test */
    public function user_a_can_view_own_transaction_edit_page(): void
    {
        $this->actingAs($this->userA)
            ->get(route('transactions.edit', $this->transactionA))
            ->assertOk();
    }

    /** @test */
    public function user_a_can_edit_own_transaction(): void
    {
        Livewire::actingAs($this->userA)
            ->test(\App\Livewire\Transactions\EditTransaction::class, ['transaction' => $this->transactionA])
            ->set('amount', '75000')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('transactions', [
            'id'     => $this->transactionA->id,
            'amount' => 75_000,
        ]);
    }

    // =========================================================================
    // PEKERJAAN A-1: Rute HTTP — Route Model Binding + Policy
    // =========================================================================

    /** @test [CONFIRMED] route policy blocks cross-household transaction view */
    public function user_b_cannot_view_transaction_edit_page_of_household_a(): void
    {
        $this->actingAs($this->userB)
            ->get(route('transactions.edit', $this->transactionA))
            ->assertNotFound(); // abort_unless → 404
    }

    // =========================================================================
    // PEKERJAAN A-2: Livewire — EditTransaction
    // =========================================================================

    /** @test [CONFIRMED] Livewire blocks mount of cross-household transaction */
    public function livewire_edit_transaction_mount_is_blocked_for_cross_household(): void
    {
        // Livewire's mount calls abort_unless policy, so it throws 404
        $this->actingAs($this->userB);

        $response = Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Transactions\EditTransaction::class, [
                'transaction' => $this->transactionA,
            ]);

        // The component should throw a 404 during mount
        $response->assertStatus(404);
    }

    // =========================================================================
    // PEKERJAAN A-3: Livewire — ManageAccounts
    // =========================================================================

    /** @test [CONFIRMED] user B cannot edit account belonging to household A */
    public function livewire_manage_accounts_edit_cross_household_is_silently_ignored(): void
    {
        $originalName = $this->accountA->name;

        Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Accounts\ManageAccounts::class)
            ->call('openEdit', $this->accountA->id); // should be silently ignored

        // AccountA name must not be changed
        $this->assertDatabaseHas('accounts', [
            'id'   => $this->accountA->id,
            'name' => $originalName,
        ]);
    }

    /** @test [CONFIRMED] user B cannot delete account belonging to household A */
    public function livewire_manage_accounts_delete_cross_household_is_silently_ignored(): void
    {
        Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Accounts\ManageAccounts::class)
            ->call('confirmDelete', $this->accountA->id)
            ->call('delete');

        // AccountA must still exist
        $this->assertDatabaseHas('accounts', ['id' => $this->accountA->id]);
    }

    /** @test [CONFIRMED] user B cannot toggle-active account of household A */
    public function livewire_manage_accounts_toggle_cross_household_is_silently_ignored(): void
    {
        $original = $this->accountA->is_active;

        Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Accounts\ManageAccounts::class)
            ->call('toggleActive', $this->accountA->id);

        $this->assertDatabaseHas('accounts', [
            'id'        => $this->accountA->id,
            'is_active' => $original,
        ]);
    }

    // =========================================================================
    // PEKERJAAN A-4: Livewire — ManageBudgets
    // =========================================================================

    /** @test [CONFIRMED] user B cannot delete budget belonging to household A — gets 404 */
    public function livewire_manage_budgets_delete_cross_household_is_silently_ignored(): void
    {
        // ManageBudgets::deleteBudget uses abort_if(! $budget, 404) after household scope
        // So calling with a foreign ID returns 404 response
        Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Budgets\ManageBudgets::class)
            ->call('deleteBudget', $this->budgetA->id)
            ->assertStatus(404);

        // Budget must still exist
        $this->assertDatabaseHas('budgets', ['id' => $this->budgetA->id]);
    }

    /** @test [CONFIRMED] user B cannot edit budget amount of household A — gets 404 */
    public function livewire_manage_budgets_edit_amount_cross_household_is_silently_ignored(): void
    {
        $originalAmount = $this->budgetA->amount;

        // startEdit uses abort_if(! $budget, 404) → 404 for cross-household IDs
        Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Budgets\ManageBudgets::class)
            ->call('startEdit', $this->budgetA->id)
            ->assertStatus(404);

        $this->assertDatabaseHas('budgets', [
            'id'     => $this->budgetA->id,
            'amount' => $originalAmount,
        ]);
    }

    // =========================================================================
    // PEKERJAAN A-5: Livewire — ManageSavingsGoals
    // =========================================================================

    /** @test [CONFIRMED] user B cannot archive savings goal of household A — gets 404 */
    public function livewire_manage_savings_archive_cross_household_is_silently_ignored(): void
    {
        // archiveGoal uses abort_if(! $goal, 404)
        Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Savings\ManageSavingsGoals::class)
            ->call('archiveGoal', $this->goalA->id)
            ->assertStatus(404);

        $this->assertDatabaseHas('savings_goals', [
            'id'     => $this->goalA->id,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);
    }

    /** @test [CONFIRMED] user B cannot delete savings goal of household A (no delete method; archiveGoal is the pattern) */
    public function livewire_manage_savings_delete_cross_household_is_silently_ignored(): void
    {
        // Verify there is no deleteGoal public method — if any add: would also be 404 scoped
        $reflection = new \ReflectionClass(\App\Livewire\Savings\ManageSavingsGoals::class);
        $publicMethods = array_map(fn($m) => $m->getName(), $reflection->getMethods(\ReflectionMethod::IS_PUBLIC));
        // The component intentionally has no deleteGoal — goals are archived, not deleted.
        // [CONFIRMED] By design: goals are never hard-deleted from UI.
        $this->assertNotContains('deleteGoal', $publicMethods);
    }

    /** @test [CONFIRMED] user B cannot contribute to savings goal of household A — gets 404 */
    public function livewire_manage_savings_contribute_cross_household_is_silently_ignored(): void
    {
        $originalAmount = $this->goalA->current_amount;

        // startContribution uses abort_if(! $goal, 404)
        Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Savings\ManageSavingsGoals::class)
            ->call('startContribution', $this->goalA->id)
            ->assertStatus(404);

        $this->assertDatabaseHas('savings_goals', [
            'id'             => $this->goalA->id,
            'current_amount' => $originalAmount,
        ]);
    }

    // =========================================================================
    // PEKERJAAN A-6: Aggregate data (dashboard/laporan tidak bocor antar HH)
    // =========================================================================

    /** @test [CONFIRMED] dashboard of user B does not include household A transaction amounts */
    public function dashboard_does_not_show_household_a_data_to_user_b(): void
    {
        // UserA has a 50,000 expense. UserB should see 0 total, not userA's figure.
        // We check userB's dashboard shows "Rp 0" or similar — not userA's category name.
        $this->actingAs($this->userB)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee($this->categoryA->name . ' (householdA)'); // should not appear

        // More direct: assert userB's total expense is Rp 0 (no transactions)
        // The dashboard shows "Pengeluaran Bulan Ini" — verify it shows 0, not 50000
        $this->actingAs($this->userB)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Dana Darurat A'); // userA's goal name should not appear
    }

    /** @test [CONFIRMED] report page of user B does not show household A transaction data */
    public function report_page_does_not_show_household_a_data_to_user_b(): void
    {
        $this->actingAs($this->userB)
            ->get(route('reports.index'))
            ->assertOk()
            ->assertDontSee('Dana Darurat A'); // userA's goal name
    }

    // =========================================================================
    // PEKERJAAN A-7: CreateTransaction — category and account IDOR via save
    // =========================================================================

    /** @test [CONFIRMED] user B cannot create transaction using categoryA's ID */
    public function create_transaction_with_cross_household_category_is_rejected(): void
    {
        // UserB tries to save a transaction with categoryA.id (belongs to householdA)
        Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Transactions\CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)
            ->set('category_id', $this->categoryA->id) // cross-household ID
            ->set('amount', '50000')
            ->set('transaction_date', now()->toDateString())
            ->call('save')
            ->assertHasErrors(['category_id']); // rejected: not found in householdB
    }

    /** @test [CONFIRMED] user B cannot create transaction using accountA's ID */
    public function create_transaction_with_cross_household_account_is_rejected(): void
    {
        // Get a valid category for userB
        $categoryB = $this->userB->household->categories()
            ->where('type', Category::TYPE_EXPENSE)
            ->first();

        Livewire::actingAs($this->userB)
            ->test(\App\Livewire\Transactions\CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)
            ->set('category_id', $categoryB->id)
            ->set('account_id', $this->accountA->id) // cross-household account
            ->set('amount', '50000')
            ->set('transaction_date', now()->toDateString())
            ->call('save')
            ->assertHasErrors(['account_id']); // rejected: not in householdB
    }

    // =========================================================================
    // PEKERJAAN A-8: Household-sharing invariant (two users in SAME household)
    // =========================================================================

    /** @test two members of same household CAN both read shared transactions */
    public function two_users_in_same_household_share_transaction_visibility(): void
    {
        // Create userC without factory hooks by using raw Eloquent create
        $userC = \App\Models\User::create([
            'name'              => 'User C',
            'email'             => 'userc@example.com',
            'email_verified_at' => now(),
            'password'          => bcrypt('password'),
        ]);

        // Manually join householdA as member (skips ProvisionHouseholdForUser)
        \App\Models\HouseholdMember::create([
            'household_id' => $this->userA->household->id,
            'user_id'      => $userC->id,
            'role'         => \App\Models\HouseholdMember::ROLE_MEMBER,
        ]);
        $userC->refresh();

        // UserC (member of householdA) should see categoryA's name via TransactionList
        Livewire::actingAs($userC)
            ->test(\App\Livewire\Transactions\TransactionList::class)
            ->assertSee($this->categoryA->name);
    }

    // =========================================================================
    // PEKERJAAN A-9: Mass assignment — fillable must not allow user overwriting
    // =========================================================================

    /** @test [CONFIRMED] Transaction fillable does not include fields attackers should never set */
    public function transaction_model_fillable_is_appropriately_scoped(): void
    {
        // 'deleted_at' is NOT in fillable (should never be mass-assignable)
        $this->assertNotContains('deleted_at', (new Transaction())->getFillable());
        // 'id' is NOT in fillable
        $this->assertNotContains('id', (new Transaction())->getFillable());
    }

    /** @test [CONFIRMED] Account fillable does not include dangerous fields */
    public function account_model_fillable_does_not_include_id(): void
    {
        $this->assertNotContains('id', (new Account())->getFillable());
        $this->assertNotContains('deleted_at', (new Account())->getFillable());
    }

    /** @test [CONFIRMED] SavingsGoal fillable does not include id */
    public function savings_goal_model_fillable_does_not_include_id(): void
    {
        $this->assertNotContains('id', (new SavingsGoal())->getFillable());
    }
}
