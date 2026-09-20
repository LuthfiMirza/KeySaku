<?php

namespace Tests\Feature\Transactions;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * B1: Transfer antar akun
 *
 * [CONFIRMED] Transfer antar akun TIDAK DIIMPLEMENTASIKAN dalam kode.
 *
 * Bukti:
 * - Transaction::types() hanya mengembalikan ['income', 'expense']
 * - Tidak ada TYPE_TRANSFER constant di Transaction model
 * - docs/EXECUTION-PLAN.md:196 mencatat: "Tipe transaksi transfer — ditolak, terlalu invasif"
 * - Tidak ada Livewire komponen TransferTransaction
 * - CreateTransaction.php tidak memiliki logika transfer
 *
 * Test ini mendokumentasikan state yang ada (tidak ada transfer).
 * Transfer akan menjadi fitur masa depan.
 */
class TransferTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Account $accountA;
    private Account $accountB;
    private Category $categoryExpense;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $household  = $this->user->household;

        $this->accountA = Account::create([
            'household_id' => $household->id,
            'user_id'      => $this->user->id,
            'name'         => 'BCA',
            'type'         => Account::TYPE_BANK,
            'balance'      => 500_000,
            'is_active'    => true,
        ]);

        $this->accountB = Account::create([
            'household_id' => $household->id,
            'user_id'      => $this->user->id,
            'name'         => 'GoPay',
            'type'         => Account::TYPE_EWALLET,
            'balance'      => 100_000,
            'is_active'    => true,
        ]);

        $this->categoryExpense = $household->categories()
            ->where('type', Category::TYPE_EXPENSE)
            ->firstOrFail();
    }

    // =========================================================================
    // [CONFIRMED] Transfer tidak ada — Dokumentasi state saat ini
    // =========================================================================

    /** @test [CONFIRMED] Transaction model does not have a transfer type */
    public function transaction_model_has_no_transfer_type(): void
    {
        $this->assertNotContains('transfer', Transaction::types());
        $this->assertSame(['income', 'expense'], Transaction::types());
    }

    /** @test [CONFIRMED] CreateTransaction component does not support transfer type */
    public function create_transaction_rejects_transfer_type(): void
    {
        \Livewire\Livewire::actingAs($this->user)
            ->test(\App\Livewire\Transactions\CreateTransaction::class)
            ->set('type', 'transfer')
            ->set('amount', '50000')
            ->set('transaction_date', now()->toDateString())
            ->call('save')
            ->assertHasErrors(['type']); // 'transfer' not in Rule::in(Transaction::types())
    }

    /** @test [CONFIRMED] There is no TransferTransaction Livewire component */
    public function no_transfer_livewire_component_exists(): void
    {
        $this->assertFalse(
            class_exists(\App\Livewire\Transactions\TransferTransaction::class),
            'TransferTransaction Livewire component should not exist yet (feature not implemented)'
        );
    }

    // =========================================================================
    // Account balance integrity via Observer (income/expense only)
    // =========================================================================

    /** @test [CONFIRMED] creating an expense decrements account balance */
    public function creating_expense_decrements_account_balance(): void
    {
        $originalBalance = (float) $this->accountA->balance;

        Transaction::create([
            'user_id'          => $this->user->id,
            'household_id'     => $this->user->household->id,
            'category_id'      => $this->categoryExpense->id,
            'account_id'       => $this->accountA->id,
            'type'             => Transaction::TYPE_EXPENSE,
            'amount'           => 50_000,
            'transaction_date' => now()->toDateString(),
            'source'           => Transaction::SOURCE_MANUAL,
        ]);

        $this->assertDatabaseHas('accounts', [
            'id'      => $this->accountA->id,
            'balance' => $originalBalance - 50_000,
        ]);
    }

    /** @test [CONFIRMED] soft deleting expense restores account balance */
    public function soft_deleting_expense_restores_account_balance(): void
    {
        $originalBalance = (float) $this->accountA->balance;

        $tx = Transaction::create([
            'user_id'          => $this->user->id,
            'household_id'     => $this->user->household->id,
            'category_id'      => $this->categoryExpense->id,
            'account_id'       => $this->accountA->id,
            'type'             => Transaction::TYPE_EXPENSE,
            'amount'           => 50_000,
            'transaction_date' => now()->toDateString(),
            'source'           => Transaction::SOURCE_MANUAL,
        ]);

        $tx->delete();

        $this->assertDatabaseHas('accounts', [
            'id'      => $this->accountA->id,
            'balance' => $originalBalance, // restored
        ]);
    }

    /** @test [CONFIRMED] restoring a soft-deleted expense decrements balance again */
    public function restoring_soft_deleted_expense_decrements_balance_again(): void
    {
        $originalBalance = (float) $this->accountA->balance;

        $tx = Transaction::create([
            'user_id'          => $this->user->id,
            'household_id'     => $this->user->household->id,
            'category_id'      => $this->categoryExpense->id,
            'account_id'       => $this->accountA->id,
            'type'             => Transaction::TYPE_EXPENSE,
            'amount'           => 50_000,
            'transaction_date' => now()->toDateString(),
            'source'           => Transaction::SOURCE_MANUAL,
        ]);

        $tx->delete();
        $tx->restore();

        $this->assertDatabaseHas('accounts', [
            'id'      => $this->accountA->id,
            'balance' => $originalBalance - 50_000,
        ]);
    }

    /** @test [CONFIRMED] updating transaction amount adjusts balance correctly */
    public function updating_transaction_amount_adjusts_account_balance(): void
    {
        $tx = Transaction::create([
            'user_id'          => $this->user->id,
            'household_id'     => $this->user->household->id,
            'category_id'      => $this->categoryExpense->id,
            'account_id'       => $this->accountA->id,
            'type'             => Transaction::TYPE_EXPENSE,
            'amount'           => 50_000,
            'transaction_date' => now()->toDateString(),
            'source'           => Transaction::SOURCE_MANUAL,
        ]);

        $balanceAfterCreate = (float) $this->accountA->fresh()->balance;

        // Update amount from 50k to 75k (additional 25k deducted)
        $tx->update(['amount' => 75_000]);

        $this->assertDatabaseHas('accounts', [
            'id'      => $this->accountA->id,
            'balance' => $balanceAfterCreate - 25_000,
        ]);
    }

    /** @test [CONFIRMED] updating account_id moves balance correctly between accounts */
    public function updating_transaction_account_moves_balance_between_accounts(): void
    {
        $balanceA = (float) $this->accountA->balance;
        $balanceB = (float) $this->accountB->balance;

        $tx = Transaction::create([
            'user_id'          => $this->user->id,
            'household_id'     => $this->user->household->id,
            'category_id'      => $this->categoryExpense->id,
            'account_id'       => $this->accountA->id,
            'type'             => Transaction::TYPE_EXPENSE,
            'amount'           => 50_000,
            'transaction_date' => now()->toDateString(),
            'source'           => Transaction::SOURCE_MANUAL,
        ]);

        // Move the transaction from accountA to accountB
        $tx->update(['account_id' => $this->accountB->id]);

        // AccountA should be restored to original balance
        $this->assertDatabaseHas('accounts', [
            'id'      => $this->accountA->id,
            'balance' => $balanceA,
        ]);
        // AccountB should now be decremented
        $this->assertDatabaseHas('accounts', [
            'id'      => $this->accountB->id,
            'balance' => $balanceB - 50_000,
        ]);
    }
}
