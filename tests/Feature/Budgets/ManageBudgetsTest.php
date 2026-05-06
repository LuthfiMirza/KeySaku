<?php

namespace Tests\Feature\Budgets;

use App\Livewire\Budgets\ManageBudgets;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageBudgetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_monthly_budget_for_expense_category(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->set('category_id', $category->id)
            ->set('amount', '1500000')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Anggaran berhasil disimpan.')
            ->assertSee('Makanan')
            ->assertSee('Rp 1.500.000');

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 1500000.00,
        ]);
    }

    public function test_user_cannot_create_duplicate_budget_for_same_category_month_year(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 1000000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->set('category_id', $category->id)
            ->set('amount', '1500000')
            ->call('save')
            ->assertHasErrors(['category_id'])
            ->assertSee('Anggaran untuk kategori ini pada bulan tersebut sudah ada.');

        $this->assertDatabaseCount('budgets', 1);
    }

    public function test_user_cannot_budget_income_or_other_users_category(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $incomeCategory = $this->category($user, 'Gaji', Category::TYPE_INCOME);
        $otherCategory = $this->category($otherUser, 'Makanan', Category::TYPE_EXPENSE);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->set('category_id', $incomeCategory->id)
            ->set('amount', '1500000')
            ->call('save')
            ->assertHasErrors(['category_id'])
            ->assertSee('Kategori pengeluaran tidak ditemukan.');

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->set('category_id', $otherCategory->id)
            ->set('amount', '1500000')
            ->call('save')
            ->assertHasErrors(['category_id'])
            ->assertSee('Kategori pengeluaran tidak ditemukan.');

        $this->assertDatabaseCount('budgets', 0);
    }

    public function test_budget_form_validates_required_and_numeric_fields(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 13)
            ->set('year', now()->year + 6)
            ->set('category_id', null)
            ->set('amount', '0')
            ->call('save')
            ->assertHasErrors(['month', 'year', 'category_id', 'amount']);
    }

    public function test_budget_empty_state_guides_user_to_create_budget_for_selected_month(): void
    {
        $user = User::factory()->create();
        $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->assertSee('Pilih bulan dan tahun untuk melihat atau membuat anggaran.')
            ->assertSee('Belum ada anggaran untuk bulan ini.')
            ->assertSee('Buat anggaran per kategori agar KeySaku bisa memantau batas pengeluaranmu.')
            ->assertSee('Buat Anggaran')
            ->assertSee('Lihat Riwayat');
    }

    public function test_budget_progress_uses_selected_month_expenses_for_current_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);
        $incomeCategory = $this->category($user, 'Gaji', Category::TYPE_INCOME);
        $otherCategory = $this->category($otherUser, 'Makanan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        $this->transaction($user, $category, Transaction::TYPE_EXPENSE, 50000, '2026-05-06', 'makan');
        $this->transaction($user, $category, Transaction::TYPE_EXPENSE, 25000, '2026-05-07', 'kopi');
        $this->transaction($user, $category, Transaction::TYPE_EXPENSE, 999000, '2026-04-30', 'bulan lalu');
        $this->transaction($user, $incomeCategory, Transaction::TYPE_INCOME, 999000, '2026-05-06', 'gaji');
        $this->transaction($otherUser, $otherCategory, Transaction::TYPE_EXPENSE, 999000, '2026-05-06', 'user lain');
        $deleted = $this->transaction($user, $category, Transaction::TYPE_EXPENSE, 10000, '2026-05-08', 'hapus');
        $deleted->delete();

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->assertSee('Progress Anggaran')
            ->assertSee('Makanan')
            ->assertSee('Rp 100.000')
            ->assertSee('Rp 75.000')
            ->assertSee('Rp 25.000')
            ->assertSee('75% terpakai')
            ->assertSee('Aman')
            ->assertDontSee('Rp 999.000')
            ->assertDontSee('Rp 85.000');
    }

    public function test_budget_progress_shows_no_expense_helper_when_budgets_have_no_usage(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->assertSee('Belum ada pengeluaran untuk kategori anggaran bulan ini.')
            ->assertSee('Tambah Transaksi')
            ->assertSee('Rp 0')
            ->assertSee('0% terpakai');
    }

    public function test_budget_progress_status_changes_near_and_over_limit(): void
    {
        $user = User::factory()->create();
        $transportCategory = $this->category($user, 'Transportasi', Category::TYPE_EXPENSE);
        $healthCategory = $this->category($user, 'Kesehatan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $transportCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $healthCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        $this->transaction($user, $transportCategory, Transaction::TYPE_EXPENSE, 90000, '2026-05-06', 'bensin');
        $this->transaction($user, $healthCategory, Transaction::TYPE_EXPENSE, 125000, '2026-05-07', 'obat');

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->assertSee('90% terpakai')
            ->assertSee('Mendekati Batas')
            ->assertSee('125% terpakai')
            ->assertSee('Lewat Batas')
            ->assertSee('Lewat Rp 25.000');
    }

    public function test_budget_summary_uses_only_selected_month_budget_categories_for_current_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $foodCategory = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);
        $transportCategory = $this->category($user, 'Transportasi', Category::TYPE_EXPENSE);
        $unbudgetedCategory = $this->category($user, 'Hiburan', Category::TYPE_EXPENSE);
        $incomeCategory = $this->category($user, 'Gaji', Category::TYPE_INCOME);
        $otherCategory = $this->category($otherUser, 'Makanan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $foodCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $transportCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 200000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $unbudgetedCategory->id,
            'month' => 4,
            'year' => 2026,
            'amount' => 999000,
        ]);

        $this->transaction($user, $foodCategory, Transaction::TYPE_EXPENSE, 125000, '2026-05-06', 'makan');
        $this->transaction($user, $transportCategory, Transaction::TYPE_EXPENSE, 50000, '2026-05-07', 'bensin');
        $this->transaction($user, $unbudgetedCategory, Transaction::TYPE_EXPENSE, 999000, '2026-05-08', 'film');
        $this->transaction($user, $foodCategory, Transaction::TYPE_EXPENSE, 999000, '2026-04-30', 'bulan lalu');
        $this->transaction($user, $incomeCategory, Transaction::TYPE_INCOME, 999000, '2026-05-06', 'gaji');
        $this->transaction($otherUser, $otherCategory, Transaction::TYPE_EXPENSE, 999000, '2026-05-06', 'user lain');
        $deleted = $this->transaction($user, $transportCategory, Transaction::TYPE_EXPENSE, 25000, '2026-05-08', 'hapus');
        $deleted->delete();

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->assertSee('Ringkasan Anggaran')
            ->assertSee('Total Anggaran')
            ->assertSee('Terpakai')
            ->assertSee('Sisa')
            ->assertSee('Lewat Batas')
            ->assertSee('Rp 300.000')
            ->assertSee('Rp 175.000')
            ->assertSee('Rp 125.000')
            ->assertSee('1 kategori')
            ->assertDontSee('Rp 999.000')
            ->assertDontSee('Rp 1.174.000');
    }

    public function test_user_can_start_editing_own_budget(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->call('startEdit', $budget->id)
            ->assertSet('editingBudgetId', $budget->id)
            ->assertSet('editAmount', '100000')
            ->assertSee('Nominal Anggaran')
            ->assertSee('Simpan')
            ->assertSee('Batal');
    }

    public function test_user_can_update_own_budget_amount_and_refresh_progress_summary(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        $this->transaction($user, $category, Transaction::TYPE_EXPENSE, 75000, '2026-05-06', 'makan');

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->call('startEdit', $budget->id)
            ->set('editAmount', '150000')
            ->call('updateBudget')
            ->assertHasNoErrors()
            ->assertSet('editingBudgetId', null)
            ->assertSet('editAmount', '')
            ->assertSee('Anggaran berhasil diperbarui.')
            ->assertSee('Rp 150.000')
            ->assertSee('Rp 75.000')
            ->assertSee('50% terpakai')
            ->assertDontSee('75% terpakai');

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'user_id' => $user->id,
            'amount' => 150000.00,
        ]);
    }

    public function test_budget_edit_validates_amount(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->call('startEdit', $budget->id)
            ->set('editAmount', '0')
            ->call('updateBudget')
            ->assertHasErrors(['editAmount']);

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'amount' => 100000.00,
        ]);
    }

    public function test_user_cannot_update_another_users_budget(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherCategory = $this->category($otherUser, 'Makanan', Category::TYPE_EXPENSE);

        $otherBudget = Budget::create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('editingBudgetId', $otherBudget->id)
            ->set('editAmount', '150000')
            ->call('updateBudget')
            ->assertNotFound();

        $this->assertDatabaseHas('budgets', [
            'id' => $otherBudget->id,
            'user_id' => $otherUser->id,
            'amount' => 100000.00,
        ]);
    }

    public function test_user_can_delete_own_budget_without_deleting_transactions(): void
    {
        $user = User::factory()->create();
        $foodCategory = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);
        $transportCategory = $this->category($user, 'Transportasi', Category::TYPE_EXPENSE);

        $foodBudget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $foodCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $transportCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 200000,
        ]);

        $transaction = $this->transaction($user, $foodCategory, Transaction::TYPE_EXPENSE, 75000, '2026-05-06', 'makan');

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->call('deleteBudget', $foodBudget->id)
            ->assertSee('Anggaran berhasil dihapus.')
            ->assertSee('Transportasi')
            ->assertSee('Rp 200.000')
            ->assertDontSee('Rp 300.000')
            ->assertDontSee('Rp 75.000')
            ->assertDontSee('75% terpakai');

        $this->assertDatabaseMissing('budgets', [
            'id' => $foodBudget->id,
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'category_id' => $foodCategory->id,
            'amount' => 75000.00,
        ]);
    }

    public function test_deleting_edited_budget_clears_edit_state(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->call('startEdit', $budget->id)
            ->call('deleteBudget', $budget->id)
            ->assertSet('editingBudgetId', null)
            ->assertSet('editAmount', '')
            ->assertSee('Anggaran berhasil dihapus.');

        $this->assertDatabaseMissing('budgets', [
            'id' => $budget->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_budget(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherCategory = $this->category($otherUser, 'Makanan', Category::TYPE_EXPENSE);

        $otherBudget = Budget::create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->call('deleteBudget', $otherBudget->id)
            ->assertNotFound();

        $this->assertDatabaseHas('budgets', [
            'id' => $otherBudget->id,
            'user_id' => $otherUser->id,
            'amount' => 100000.00,
        ]);
    }

    public function test_user_can_copy_previous_month_budgets_to_selected_month(): void
    {
        $user = User::factory()->create();
        $foodCategory = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);
        $transportCategory = $this->category($user, 'Transportasi', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $foodCategory->id,
            'month' => 4,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $transportCategory->id,
            'month' => 4,
            'year' => 2026,
            'amount' => 200000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->call('copyPreviousMonthBudgets')
            ->assertSee('2 anggaran berhasil disalin dari bulan lalu.')
            ->assertSee('Makanan')
            ->assertSee('Transportasi')
            ->assertSee('Rp 300.000')
            ->assertSee('Rp 100.000')
            ->assertSee('Rp 200.000');

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $foodCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 100000.00,
        ]);

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $transportCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 200000.00,
        ]);
    }

    public function test_copy_previous_month_uses_previous_year_for_january(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 12,
            'year' => 2025,
            'amount' => 100000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 1)
            ->set('year', 2026)
            ->call('copyPreviousMonthBudgets')
            ->assertSee('1 anggaran berhasil disalin dari bulan lalu.')
            ->assertSee('Januari 2026')
            ->assertSee('Rp 100.000');

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 1,
            'year' => 2026,
            'amount' => 100000.00,
        ]);
    }

    public function test_copy_previous_month_skips_existing_target_budgets_without_overwriting(): void
    {
        $user = User::factory()->create();
        $foodCategory = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);
        $transportCategory = $this->category($user, 'Transportasi', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $foodCategory->id,
            'month' => 4,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $transportCategory->id,
            'month' => 4,
            'year' => 2026,
            'amount' => 200000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $foodCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 500000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->call('copyPreviousMonthBudgets')
            ->assertSee('1 anggaran berhasil disalin dari bulan lalu.')
            ->assertSee('Rp 700.000')
            ->assertSee('Rp 500.000')
            ->assertSee('Rp 200.000')
            ->assertDontSee('Rp 100.000');

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $foodCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 500000.00,
        ]);

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $transportCategory->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 200000.00,
        ]);

        $this->assertDatabaseCount('budgets', 4);
    }

    public function test_copy_previous_month_does_not_copy_other_users_budgets(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherCategory = $this->category($otherUser, 'Makanan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
            'month' => 4,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->call('copyPreviousMonthBudgets')
            ->assertSee('Tidak ada anggaran bulan lalu untuk disalin.');

        $this->assertDatabaseCount('budgets', 1);
        $this->assertDatabaseMissing('budgets', [
            'user_id' => $user->id,
            'month' => 5,
            'year' => 2026,
        ]);
    }

    public function test_copy_previous_month_shows_message_when_source_is_empty(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->call('copyPreviousMonthBudgets')
            ->assertSee('Tidak ada anggaran bulan lalu untuk disalin.');

        $this->assertDatabaseCount('budgets', 0);
    }

    public function test_copy_previous_month_shows_message_when_all_categories_already_exist(): void
    {
        $user = User::factory()->create();
        $category = $this->category($user, 'Makanan', Category::TYPE_EXPENSE);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 4,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 5,
            'year' => 2026,
            'amount' => 500000,
        ]);

        Livewire::actingAs($user)
            ->test(ManageBudgets::class)
            ->set('month', 5)
            ->set('year', 2026)
            ->call('copyPreviousMonthBudgets')
            ->assertSee('Semua kategori dari bulan lalu sudah punya anggaran bulan ini.')
            ->assertSee('Rp 500.000')
            ->assertDontSee('Rp 100.000');

        $this->assertDatabaseCount('budgets', 2);
    }

    private function category(User $user, string $name, string $type): Category
    {
        return Category::create([
            'user_id' => $user->id,
            'name' => $name,
            'type' => $type,
            'color' => '#6366f1',
            'icon' => '💸',
        ]);
    }

    private function transaction(
        User $user,
        Category $category,
        string $type,
        int $amount,
        string $date,
        string $note
    ): Transaction {
        return Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => $type,
            'amount' => $amount,
            'transaction_date' => $date,
            'note' => $note,
            'source' => Transaction::SOURCE_MANUAL,
            'raw_voice_text' => null,
        ]);
    }
}
