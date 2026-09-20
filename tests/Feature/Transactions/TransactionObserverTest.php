<?php

namespace Tests\Feature\Transactions;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionObserverTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Account $account;
    private Category $expenseCategory;
    private Category $incomeCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $household = $this->user->household;

        $this->account = Account::create([
            'household_id' => $household->id,
            'user_id' => $this->user->id,
            'name' => 'Dompet Utama',
            'type' => Account::TYPE_CASH,
            'balance' => 100000,
        ]);

        $this->expenseCategory = Category::where('household_id', $household->id)
            ->where('type', Category::TYPE_EXPENSE)
            ->first() ?? Category::factory()->create([
                'household_id' => $household->id,
                'user_id' => $this->user->id,
                'type' => Category::TYPE_EXPENSE,
            ]);

        $this->incomeCategory = Category::where('household_id', $household->id)
            ->where('type', Category::TYPE_INCOME)
            ->first() ?? Category::factory()->create([
                'household_id' => $household->id,
                'user_id' => $this->user->id,
                'type' => Category::TYPE_INCOME,
            ]);
    }

    public function test_creating_expense_transaction_decreases_account_balance(): void
    {
        Transaction::create([
            'household_id' => $this->user->household->id,
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->expenseCategory->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 20000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        $this->assertEquals(80000, $this->account->fresh()->balance);
    }

    public function test_creating_income_transaction_increases_account_balance(): void
    {
        Transaction::create([
            'household_id' => $this->user->household->id,
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->incomeCategory->id,
            'type' => Transaction::TYPE_INCOME,
            'amount' => 50000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        $this->assertEquals(150000, $this->account->fresh()->balance);
    }

    public function test_updating_transaction_adjusts_account_balance_correctly(): void
    {
        $transaction = Transaction::create([
            'household_id' => $this->user->household->id,
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->expenseCategory->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 20000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        $this->assertEquals(80000, $this->account->fresh()->balance);

        // Update to higher amount
        $transaction->update(['amount' => 30000]);
        $this->assertEquals(70000, $this->account->fresh()->balance);

        // Update to income
        $transaction->update([
            'type' => Transaction::TYPE_INCOME,
            'category_id' => $this->incomeCategory->id,
        ]);
        $this->assertEquals(130000, $this->account->fresh()->balance);
    }

    public function test_deleting_transaction_restores_account_balance(): void
    {
        $transaction = Transaction::create([
            'household_id' => $this->user->household->id,
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->expenseCategory->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 20000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        $this->assertEquals(80000, $this->account->fresh()->balance);

        $transaction->delete();

        $this->assertEquals(100000, $this->account->fresh()->balance);
    }
}
