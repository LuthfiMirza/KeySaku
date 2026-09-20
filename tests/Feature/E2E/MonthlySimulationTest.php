<?php

namespace Tests\Feature\E2E;

use App\Livewire\Accounts\ManageAccounts;
use App\Livewire\Budgets\ManageBudgets;
use App\Livewire\Savings\ManageSavingsGoals;
use App\Livewire\Transactions\CreateTransaction;
use App\Livewire\Transactions\EditTransaction;
use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Household;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use App\Services\VoiceTransactionParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class MonthlySimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_simulation_e2e()
    {
        // ---------------------------------------------------------
        // Langkah 1: Onboarding
        // ---------------------------------------------------------
        $registerData = [
            'name' => 'User E2E',
            'email' => 'e2e@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $registerData);
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'e2e@example.com')->first();
        $this->assertNotNull($user->household);
        $this->assertNotNull($user->household->id);

        $household = $user->household;
        $this->assertTrue($household->categories()->count() > 0, 'Default categories should exist');

        $this->actingAs($user)
            ->get('/')
            ->assertStatus(200)
            ->assertSee('Belum ada transaksi');

        // ---------------------------------------------------------
        // Langkah 2: Akun
        // ---------------------------------------------------------
        Livewire::actingAs($user)
            ->test(ManageAccounts::class)
            ->call('openCreate')
            ->set('name', 'Kas')
            ->set('type', Account::TYPE_CASH)
            ->set('balance', '500000')
            ->set('color', '#10b981')
            ->call('save')
            ->assertHasNoErrors();

        Livewire::actingAs($user)
            ->test(ManageAccounts::class)
            ->call('openCreate')
            ->set('name', 'Bank')
            ->set('type', Account::TYPE_BANK)
            ->set('balance', '2000000')
            ->set('color', '#3b82f6')
            ->call('save')
            ->assertHasNoErrors();

        $kas = $household->accounts()->where('name', 'Kas')->first();
        $bank = $household->accounts()->where('name', 'Bank')->first();

        $this->assertEquals(500000, $kas->balance);
        $this->assertEquals(2000000, $bank->balance);
        
        $totalBalance = $kas->balance + $bank->balance;
        $this->assertEquals(2500000, $totalBalance);

        // ---------------------------------------------------------
        // Langkah 3: Transaksi (bulan berjalan)
        // ---------------------------------------------------------
        $catGaji = $household->categories()->where('name', 'Gaji')->first();
        $catMakan = $household->categories()->where('name', 'Makanan')->first();
        $catTransport = $household->categories()->where('name', 'Transportasi')->first();
        $catBelanja = $household->categories()->where('name', 'Belanja Dapur')->first();

        $this->assertNotNull($catGaji);
        $this->assertNotNull($catMakan);

        $date = now()->toDateString();

        // Pemasukan: Gaji 5.000.000 ke Bank
        Livewire::actingAs($user)
            ->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_INCOME)
            ->set('category_id', $catGaji->id)
            ->set('account_id', $bank->id)
            ->set('amount', '5000000')
            ->set('transaction_date', $date)
            ->call('save')
            ->assertHasNoErrors();

        // Pengeluaran: Makan 50.000 (Kas)
        Livewire::actingAs($user)
            ->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)
            ->set('category_id', $catMakan->id)
            ->set('account_id', $kas->id)
            ->set('amount', '500000') // wait, 50k, let me correct this later, typing 50000
            ->set('amount', '50000')
            ->set('transaction_date', $date)
            ->call('save');

        // Makan 150.000 (Bank)
        Livewire::actingAs($user)
            ->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)
            ->set('category_id', $catMakan->id)
            ->set('account_id', $bank->id)
            ->set('amount', '150000')
            ->set('transaction_date', $date)
            ->call('save');

        // Transport 100.000 (Kas)
        Livewire::actingAs($user)
            ->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)
            ->set('category_id', $catTransport->id)
            ->set('account_id', $kas->id)
            ->set('amount', '100000')
            ->set('transaction_date', $date)
            ->call('save');

        // Belanja 300.000 (Bank)
        Livewire::actingAs($user)
            ->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)
            ->set('category_id', $catBelanja->id)
            ->set('account_id', $bank->id)
            ->set('amount', '300000')
            ->set('transaction_date', $date)
            ->call('save');

        // Assert per akun dan total
        $kas->refresh();
        $bank->refresh();

        $expectedKas = 500000 - 50000 - 100000;
        $expectedBank = 2000000 + 5000000 - 150000 - 300000;

        $this->assertEquals($expectedKas, $kas->balance); // 350.000
        $this->assertEquals($expectedBank, $bank->balance); // 6.550.000

        // Assert dashboard
        $this->actingAs($user)
            ->get('/')
            ->assertSee(number_format(5000000, 0, ',', '.')) // Income
            ->assertSee(number_format(600000, 0, ',', '.')) // Expense
            ->assertSee(number_format(4400000, 0, ',', '.')) // Total Balance
            ->assertSee('Makanan')
            ->assertSee('Belanja Dapur');

        // ---------------------------------------------------------
        // Langkah 4: Anggaran
        // ---------------------------------------------------------
        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('category_id', $catMakan->id)
            ->set('amount', '400000')
            ->call('save')
            ->assertHasNoErrors();

        $budgetMakan = $household->budgets()->where('category_id', $catMakan->id)->first();
        $this->assertNotNull($budgetMakan);

        $livewire = Livewire::actingAs($user)
            ->test(ManageBudgets::class);
        
        // Expense makan saat ini 200k / 400k (50%) -> Aman
        $livewire->assertSee('Aman');

        // Tambah transaksi makan 120.000 -> 320.000 (80%) -> Mendekati Batas
        Livewire::actingAs($user)->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)->set('category_id', $catMakan->id)
            ->set('account_id', $kas->id)->set('amount', '120000')->set('transaction_date', $date)
            ->call('save');
        
        $livewire = Livewire::actingAs($user)->test(ManageBudgets::class);
        $livewire->assertSee('Mendekati Batas');

        // Tambah transaksi makan 80.000 -> 400.000 (100%) -> Mendekati Batas (>=80%)
        Livewire::actingAs($user)->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)->set('category_id', $catMakan->id)
            ->set('account_id', $kas->id)->set('amount', '80000')->set('transaction_date', $date)
            ->call('save');
        
        $livewire = Livewire::actingAs($user)->test(ManageBudgets::class);
        $livewire->assertSee('Mendekati Batas'); // Since > 100 is Lewat Batas, 100% is still Mendekati

        // Tambah transaksi makan 10.000 -> 410.000 (102.5%) -> Lewat Batas (>100%)
        Livewire::actingAs($user)->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)->set('category_id', $catMakan->id)
            ->set('account_id', $kas->id)->set('amount', '10000')->set('transaction_date', $date)
            ->call('save');

        $livewire = Livewire::actingAs($user)->test(ManageBudgets::class);
        $livewire->assertSee('Lewat Batas');

        // Duplikat
        Livewire::actingAs($user)->test(ManageBudgets::class)
            ->set('category_id', $catMakan->id)
            ->set('amount', '500000')
            ->call('save')
            ->assertHasErrors(['category_id']);

        // Hapus
        $livewire->call('deleteBudget', $budgetMakan->id);
        $this->assertNull(Budget::find($budgetMakan->id));
        $this->assertTrue(Transaction::where('category_id', $catMakan->id)->exists());

        // Salin bulan depan
        Carbon::setTestNow(now()->addMonth());
        $livewire = Livewire::actingAs($user)->test(ManageBudgets::class);
        $livewire->call('copyPreviousMonthBudgets')->assertSee('Tidak ada anggaran bulan lalu untuk disalin.');
        $this->assertEquals(0, Budget::where('month', now()->month)->where('year', now()->year)->count()); // 0 because we deleted it!

        // Let's create one in previous month so we can copy
        Carbon::setTestNow(now()->subMonth());
        Livewire::actingAs($user)->test(ManageBudgets::class)
            ->set('category_id', $catBelanja->id)->set('amount', '100000')->call('save');
        Carbon::setTestNow(now()->addMonth()); // Back to original +1
        Livewire::actingAs($user)->test(ManageBudgets::class)
            ->call('copyPreviousMonthBudgets');
        $count = Budget::where('month', now()->month)->where('year', now()->year)->count();
        $this->assertEquals(1, $count);
        
        // Copy again -> no duplicates
        Livewire::actingAs($user)->test(ManageBudgets::class)
            ->call('copyPreviousMonthBudgets');
        $count2 = Budget::where('month', now()->month)->where('year', now()->year)->count();
        $this->assertEquals(1, $count2);

        Carbon::setTestNow(); // Reset time

        // ---------------------------------------------------------
        // Langkah 5: Tabungan
        // ---------------------------------------------------------
        Livewire::actingAs($user)->test(ManageSavingsGoals::class)
            ->set('name', 'Liburan')
            ->set('target_amount', '3000000')
            ->set('deadline', now()->addYear()->toDateString())
            ->call('save');

        $goal = $household->savingsGoals()->where('name', 'Liburan')->first();

        $livewire = Livewire::actingAs($user)->test(ManageSavingsGoals::class);
        $livewire->call('startContribution', $goal->id)
            ->set('contributionAmount', '500000')
            ->set('contributionDate', $date)
            ->call('addContribution')
            ->assertHasNoErrors();

        $livewire->call('startContribution', $goal->id)
            ->set('contributionAmount', '1000000')
            ->set('contributionDate', $date)
            ->call('addContribution')
            ->assertHasNoErrors();

        $goal->refresh();
        $this->assertEquals(1500000, $goal->current_amount);

        // Uji validasi
        $livewire->call('startContribution', $goal->id)
            ->set('contributionAmount', '0')->call('addContribution')->assertHasErrors('contributionAmount');
        $livewire->set('contributionAmount', '-100')->call('addContribution')->assertHasErrors('contributionAmount');
        $livewire->set('contributionAmount', 'abc')->call('addContribution')->assertHasErrors('contributionAmount');

        // Uji melewati target
        $livewire->call('startContribution', $goal->id)
            ->set('contributionAmount', '2000000') // total will be 3.5m > 3m
            ->set('contributionDate', $date)
            ->call('addContribution')
            ->assertHasNoErrors();
        $goal->refresh();
        $this->assertEquals(3500000, $goal->current_amount);
        // It allows crossing target.

        // Cek saldo akun
        $kas->refresh();
        $bank->refresh();
        $expectedKasAfterSavings = $expectedKas - 120000 - 80000 - 10000;
        $this->assertEquals($expectedKasAfterSavings, $kas->balance, "Savings contributions MUST NOT affect account balance per current design.");
        $this->assertEquals($expectedBank, $bank->balance);

        // Arsipkan
        $livewire->call('archiveGoal', $goal->id);
        $goal->refresh();
        $this->assertEquals(SavingsGoal::STATUS_ARCHIVED, $goal->status);

        // ---------------------------------------------------------
        // Langkah 6: Koreksi
        // ---------------------------------------------------------
        // Find expense belanja: 300.000 in bank
        $trx = $household->transactions()->where('category_id', $catBelanja->id)->first();
        $this->assertNotNull($trx);

        // Edit
        Livewire::actingAs($user)->test(EditTransaction::class, ['transaction' => $trx])
            ->set('amount', '350000')
            ->set('account_id', $kas->id) // Move to Kas
            ->call('save')
            ->assertHasNoErrors();

        $kas->refresh();
        $bank->refresh();
        // Bank got +300000 back
        $this->assertEquals($expectedBank + 300000, $bank->balance);
        // Kas pays -350000
        $this->assertEquals($expectedKasAfterSavings - 350000, $kas->balance);

        // Soft delete
        // Let's use TransactionList to delete
        Livewire::actingAs($user)->test(\App\Livewire\Transactions\EditTransaction::class, ['transaction' => $trx])
            ->call('delete');

        $kas->refresh();
        $this->assertEquals($expectedKasAfterSavings, $kas->balance); // Back to before payment

        // Restore -> we don't have UI restore, maybe call it programmatically? Or skip if not supported?
        // Let's manually soft delete and restore via model Observer if UI doesn't have it.
        $trxTrashed = Transaction::withTrashed()->find($trx->id);
        $trxTrashed->restore(); // this triggers observer
        $kas->refresh();
        $this->assertEquals($expectedKasAfterSavings - 350000, $kas->balance); // Deducted again

        // ---------------------------------------------------------
        // Langkah 7: Suara
        // ---------------------------------------------------------
        $parser = new VoiceTransactionParser();
        $cases = [
            "beli makan siang dua puluh ribu" => ['amount' => 20000],
            "bayar transport lima belas ribu" => ['amount' => 15000],
            "gaji masuk lima juta" => ['amount' => 5000000, 'type' => 'income'],
            "seratus lima puluh ribu belanja" => ['amount' => 150000],
            "setengah juta" => ['amount' => 500000],
            "25rb" => ['amount' => 25000],
            "Rp 25.000" => ['amount' => 25000],
            "1,5 juta" => ['amount' => 1500000],
            "hanya text tanpa angka" => ['amount' => null],
            "BelI MakAn" => ['amount' => null],
        ];

        $allCategories = $household->categories()->get();
        foreach($cases as $input => $expected) {
            $parsed = $parser->parse($input, $allCategories);
            $this->assertEquals($expected['amount'], $parsed['amount'], "Failed parsing: $input");
        }
        
        // Simpan hasil
        Livewire::actingAs($user)->test(\App\Livewire\Transactions\VoiceTransaction::class)
            ->set('transcript', 'beli makan siang dua puluh ribu kategori makanan') // beli makan siang = expense, 20000 = amount, kategori makanan = category
            ->call('parse')
            ->call('save')
            ->assertRedirect(route('transactions.index'));

        // 
            
        $voiceTrx = $household->transactions()->where('raw_voice_text', 'beli makan siang dua puluh ribu kategori makanan')->first();
        $this->assertNotNull($voiceTrx);
        $this->assertEquals(Transaction::SOURCE_VOICE, $voiceTrx->source);
        // Langkah 8: Laporan
        // ---------------------------------------------------------
        $this->actingAs($user)->get('/laporan')
            ->assertStatus(200);

        $this->actingAs($user)->get('/laporan?month=abc')
            ->assertStatus(200); // Handled gracefully

        $this->actingAs($user)->get('/laporan?month=2099-13')
            ->assertStatus(200); // Graceful

        // ---------------------------------------------------------
        // Langkah 9: Isolasi
        // ---------------------------------------------------------
        $user2 = User::factory()->create();
        $this->actingAs($user2)->get('/')
            ->assertStatus(200)
            ->assertDontSee(number_format($expectedKasAfterSavings - 350000, 0, ',', '.'));

        // ---------------------------------------------------------
        // Langkah 10: Batas nilai
        // ---------------------------------------------------------
        // 0, negatif, desimal, sangat besar
        $livewireCreate = Livewire::actingAs($user)->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)
            ->set('category_id', $catMakan->id)
            ->set('account_id', $kas->id)
            ->set('transaction_date', $date);
            
        $livewireCreate->set('amount', '0')->call('save')->assertHasErrors(['amount']);
        $livewireCreate->set('amount', '999999999999999')->call('save')->assertHasNoErrors(); // Should work if BIGINT/VARCHAR handles it
    }
}
