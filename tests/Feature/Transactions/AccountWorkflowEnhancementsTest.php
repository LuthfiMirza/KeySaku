<?php

namespace Tests\Feature\Transactions;

use App\Livewire\Transactions\EditTransaction;
use App\Livewire\Transactions\TransactionList;
use App\Livewire\Transactions\VoiceTransaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccountWorkflowEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_newly_registered_user_automatically_has_default_cash_account(): void
    {
        $user = User::factory()->create();

        $accounts = $user->household->accounts()->get();

        $this->assertCount(1, $accounts);
        $account = $accounts->first();
        $this->assertEquals('Kas Tunai', $account->name);
        $this->assertEquals(Account::TYPE_CASH, $account->type);
        $this->assertTrue($account->is_active);
    }

    public function test_updating_initial_balance_in_settings_synchronizes_with_primary_account(): void
    {
        $user = User::factory()->create();
        $account = $user->household->accounts()->first();
        $this->assertEquals(0, $account->balance);

        $this->actingAs($user)
            ->patch(route('settings.initial-balance.update'), [
                'initial_balance' => '2.500.000',
            ])
            ->assertRedirect();

        $user->household->refresh();
        $account->refresh();

        $this->assertEquals(2500000, (float) $user->household->initial_balance);
        $this->assertEquals(2500000, (float) $account->balance);
    }

    public function test_voice_transaction_saves_with_selected_account_and_decrements_balance(): void
    {
        $user = User::factory()->create();
        $account = $user->household->accounts()->first();
        $account->update(['balance' => 500000]);

        $category = $user->categories()->where('name', 'Makanan')->where('type', Category::TYPE_EXPENSE)->firstOrFail();

        Livewire::actingAs($user)
            ->test(VoiceTransaction::class)
            ->set('transcript', 'beli nasi padang dua puluh lima ribu kategori makanan')
            ->call('parse')
            ->set('selectedAccountId', $account->id)
            ->call('save')
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => 25000.00,
            'source' => Transaction::SOURCE_VOICE,
        ]);

        $account->refresh();
        $this->assertEquals(475000, (float) $account->balance);
    }

    public function test_voice_transaction_auto_detects_account_mentioned_in_transcript(): void
    {
        $user = User::factory()->create();

        $gopay = Account::create([
            'household_id' => $user->household->id,
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => Account::TYPE_EWALLET,
            'balance' => 300000,
            'color' => '#06b6d4',
            'icon' => '📱',
            'is_active' => true,
        ]);

        $category = $user->categories()->where('name', 'Transportasi')->where('type', Category::TYPE_EXPENSE)->firstOrFail();

        $component = Livewire::actingAs($user)
            ->test(VoiceTransaction::class)
            ->set('transcript', 'beli bensin 50rb pakai gopay kategori transportasi')
            ->call('parse');

        $this->assertEquals($gopay->id, $component->get('selectedAccountId'));
    }

    public function test_edit_transaction_normalizes_amount_with_thousand_separator(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->where('type', Category::TYPE_EXPENSE)->firstOrFail();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $category->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 25000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        Livewire::actingAs($user)
            ->test(EditTransaction::class, ['transaction' => $transaction])
            ->set('amount', '75.000')
            ->call('save')
            ->assertHasNoErrors();

        $transaction->refresh();
        $this->assertEquals(75000, (float) $transaction->amount);
    }

    public function test_transaction_list_displays_account_badge_and_filters_by_account(): void
    {
        $user = User::factory()->create();
        $kas = $user->household->accounts()->first();
        $bca = Account::create([
            'household_id' => $user->household->id,
            'user_id' => $user->id,
            'name' => 'BCA',
            'type' => Account::TYPE_BANK,
            'balance' => 1000000,
            'is_active' => true,
        ]);

        $category = $user->categories()->where('type', Category::TYPE_EXPENSE)->firstOrFail();

        Transaction::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $category->id,
            'account_id' => $kas->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 20000,
            'note' => 'Belanja Tunai',
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $category->id,
            'account_id' => $bca->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 80000,
            'note' => 'Belanja BCA',
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        // Test see badges in list
        Livewire::actingAs($user)
            ->test(TransactionList::class)
            ->assertSee('Kas Tunai')
            ->assertSee('BCA')
            ->set('accountId', (string) $bca->id)
            ->assertSee('Belanja BCA')
            ->assertDontSee('Belanja Tunai');
    }
}
