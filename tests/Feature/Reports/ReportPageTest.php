<?php

namespace Tests\Feature\Reports;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_monthly_report_for_selected_month(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $incomeCategory = $this->category($user, 'Gaji', Category::TYPE_INCOME, '💼');
        $foodCategory = $this->category($user, 'Makanan', Category::TYPE_EXPENSE, '🍜');
        $transportCategory = $this->category($user, 'Transportasi', Category::TYPE_EXPENSE, '🚗');
        $otherCategory = $this->category($otherUser, 'Makanan', Category::TYPE_EXPENSE, '🍜');

        $this->transaction($user, $incomeCategory, Transaction::TYPE_INCOME, 5000000, '2026-05-03', 'gaji bulanan');
        $this->transaction($user, $foodCategory, Transaction::TYPE_EXPENSE, 25000, '2026-05-06', 'beli nasi goreng');
        $this->transaction($user, $transportCategory, Transaction::TYPE_EXPENSE, 5000, '2026-05-05', 'bayar parkir');
        $this->transaction($user, $foodCategory, Transaction::TYPE_EXPENSE, 100000, '2026-04-30', 'makan bulan lalu');
        $this->transaction($otherUser, $otherCategory, Transaction::TYPE_EXPENSE, 999000, '2026-05-06', 'transaksi user lain');

        $this->actingAs($user)
            ->get(route('reports.index', ['month' => '2026-05']))
            ->assertOk()
            ->assertSee('Laporan Bulanan')
            ->assertSee('Mei 2026')
            ->assertSee('Rp 5.000.000')
            ->assertSee('Rp 30.000')
            ->assertSee('Rp 4.970.000')
            ->assertSee('Makanan')
            ->assertSee('83% dari pengeluaran')
            ->assertSee('Transportasi')
            ->assertSee('17% dari pengeluaran')
            ->assertSee('beli nasi goreng')
            ->assertSee('bayar parkir')
            ->assertDontSee('makan bulan lalu')
            ->assertDontSee('transaksi user lain')
            ->assertDontSee('Rp 999.000');
    }

    public function test_invalid_month_falls_back_to_current_month(): void
    {
        $user = User::factory()->create();

        $this->travelTo(now()->setDate(2026, 5, 6));

        $this->actingAs($user)
            ->get(route('reports.index', ['month' => 'invalid-month']))
            ->assertOk()
            ->assertSee('Mei 2026')
            ->assertSee('Belum ada data bulan ini.')
            ->assertSee('Catat pemasukan atau pengeluaran untuk melihat laporan bulanan.')
            ->assertSee('Tambah Transaksi')
            ->assertSee('Input Suara');
    }

    public function test_guest_is_redirected_from_reports_page(): void
    {
        $this->get(route('reports.index'))
            ->assertRedirect(route('login'));
    }

    private function category(User $user, string $name, string $type, string $icon): Category
    {
        return Category::create([
            'user_id' => $user->id,
            'name' => $name,
            'type' => $type,
            'color' => '#6366f1',
            'icon' => $icon,
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
