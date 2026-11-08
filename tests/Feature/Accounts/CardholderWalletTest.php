<?php

namespace Tests\Feature\Accounts;

use App\Livewire\Accounts\ManageAccounts;
use App\Models\Account;
use App\Models\Category;
use App\Models\Household;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CardholderWalletTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Household $household;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->household = $this->user->household;
    }

    public function test_accounts_page_is_accessible_by_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('accounts'));

        $response->assertOk()
            ->assertSee('Dompet Saya')
            ->assertSee('Total Saldo Dompet');
    }

    public function test_manage_accounts_displays_stacked_cards_and_total_balance(): void
    {
        // Primary account auto-created is Kas Tunai
        $bca = $this->household->accounts()->create([
            'user_id' => $this->user->id,
            'name' => 'BCA Rekening',
            'type' => Account::TYPE_BANK,
            'balance' => 5000000,
            'color' => '#00529C',
            'is_active' => true,
        ]);

        $gopay = $this->household->accounts()->create([
            'user_id' => $this->user->id,
            'name' => 'GoPay',
            'type' => Account::TYPE_EWALLET,
            'balance' => 350000,
            'color' => '#00AED6',
            'is_active' => true,
        ]);

        $totalExpected = (float) $this->household->accounts()->where('is_active', true)->sum('balance');

        Livewire::actingAs($this->user)
            ->test(ManageAccounts::class)
            ->assertSee('BCA Rekening')
            ->assertSee('GoPay')
            ->assertSee('Total Saldo Dompet')
            ->assertSet('selectedAccountId', $this->household->accounts()->where('is_active', true)->first()->id);
    }

    public function test_user_can_select_card_and_view_its_specific_transactions(): void
    {
        $bca = $this->household->accounts()->create([
            'user_id' => $this->user->id,
            'name' => 'BCA',
            'type' => Account::TYPE_BANK,
            'balance' => 2000000,
            'color' => '#00529C',
            'is_active' => true,
        ]);

        $category = $this->household->categories()->create([
            'user_id' => $this->user->id,
            'name' => 'Gaji Bulanan',
            'type' => Category::TYPE_INCOME,
            'icon' => '💼',
        ]);

        $transaction = Transaction::create([
            'household_id' => $this->household->id,
            'user_id' => $this->user->id,
            'account_id' => $bca->id,
            'category_id' => $category->id,
            'type' => Transaction::TYPE_INCOME,
            'amount' => 1500000,
            'transaction_date' => now()->toDateString(),
            'note' => 'Bonus Project BCA',
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        Livewire::actingAs($this->user)
            ->test(ManageAccounts::class)
            ->call('selectAccount', $bca->id)
            ->assertSet('selectedAccountId', $bca->id)
            ->assertSee('Bonus Project BCA')
            ->assertSee('Mutasi BCA');
    }

    public function test_user_can_create_new_account(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManageAccounts::class)
            ->call('openCreate')
            ->set('name', 'Bank Jago')
            ->set('type', Account::TYPE_BANK)
            ->set('balance', '1250000')
            ->set('icon', '🏦')
            ->set('color', '#6B38FB')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Akun berhasil ditambahkan.');

        $this->assertDatabaseHas('accounts', [
            'household_id' => $this->household->id,
            'name' => 'Bank Jago',
            'balance' => 1250000,
        ]);
    }

    public function test_user_can_edit_existing_account(): void
    {
        $account = $this->household->accounts()->first();

        Livewire::actingAs($this->user)
            ->test(ManageAccounts::class)
            ->call('openEdit', $account->id)
            ->set('name', 'Dompet Kas Utama')
            ->set('balance', '750000')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Akun berhasil diperbarui.');

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Dompet Kas Utama',
            'balance' => 750000,
        ]);
    }

    public function test_user_can_toggle_active_and_delete_account(): void
    {
        $account = $this->household->accounts()->create([
            'user_id' => $this->user->id,
            'name' => 'Akun Lama',
            'type' => Account::TYPE_BANK,
            'balance' => 10000,
            'color' => '#888888',
            'is_active' => true,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(ManageAccounts::class)
            ->call('toggleActive', $account->id);

        $this->assertFalse($account->fresh()->is_active);

        $component->call('confirmDelete', $account->id)
            ->call('delete')
            ->assertSee('Akun berhasil dihapus.');

        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id,
        ]);
    }
}
