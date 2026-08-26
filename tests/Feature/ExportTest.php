<?php

namespace Tests\Feature;

use App\Exports\MonthlyReportExport;
use App\Exports\TransactionsExport;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions_csv_export_is_triggered_with_household_scoped_data(): void
    {
        Excel::fake();

        [$user, $category] = $this->userWithCategory();
        $this->transaction($user, $category, 'Kopi pagi');

        $this->actingAs($user)
            ->get(route('transactions.export.csv'))
            ->assertOk();

        Excel::assertDownloaded('transaksi-'.now()->format('Y-m-d').'.csv', function (TransactionsExport $export) {
            return $export->collection()->pluck('note')->contains('Kopi pagi');
        });
    }

    public function test_transactions_export_excludes_other_households_data(): void
    {
        Excel::fake();

        [$user, $category] = $this->userWithCategory();
        $this->transaction($user, $category, 'Punya saya');

        [$otherUser, $otherCategory] = $this->userWithCategory();
        $this->transaction($otherUser, $otherCategory, 'Punya household lain');

        $this->actingAs($user)->get(route('transactions.export.csv'));

        Excel::assertDownloaded('transaksi-'.now()->format('Y-m-d').'.csv', function (TransactionsExport $export) {
            $notes = $export->collection()->pluck('note');

            return $notes->contains('Punya saya') && ! $notes->contains('Punya household lain');
        });
    }

    public function test_monthly_report_xlsx_export_only_includes_selected_month(): void
    {
        Excel::fake();

        [$user, $category] = $this->userWithCategory();
        $this->transaction($user, $category, 'Bulan ini', now()->toDateString());
        $this->transaction($user, $category, 'Bulan lalu', now()->subMonth()->toDateString());

        $month = now()->format('Y-m');

        $this->actingAs($user)
            ->get(route('reports.export.xlsx', ['month' => $month]))
            ->assertOk();

        Excel::assertDownloaded('laporan-'.$month.'.xlsx', function (MonthlyReportExport $export) {
            $notes = $export->collection()->pluck('note');

            return $notes->contains('Bulan ini') && ! $notes->contains('Bulan lalu');
        });
    }

    public function test_monthly_report_pdf_can_be_downloaded(): void
    {
        [$user, $category] = $this->userWithCategory();
        $this->transaction($user, $category, 'Transaksi PDF');

        $response = $this->actingAs($user)->get(route('reports.export.pdf'));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_transaction_export_amount_follows_preferred_currency(): void
    {
        Excel::fake();

        [$user, $category] = $this->userWithCategory();
        $user->update(['preferred_currency' => 'USD']);
        $this->transaction($user, $category, 'Beli kopi');

        $this->actingAs($user)->get(route('transactions.export.csv'));

        Excel::assertDownloaded('transaksi-'.now()->format('Y-m-d').'.csv', function (TransactionsExport $export) {
            $row = $export->collection()->firstWhere('note', 'Beli kopi');

            return str_starts_with($export->map($row)[3], '$');
        });
    }

    public function test_guest_cannot_access_export_routes(): void
    {
        $this->get(route('transactions.export.csv'))->assertRedirect(route('login'));
        $this->get(route('reports.export.pdf'))->assertRedirect(route('login'));
    }

    private function userWithCategory(): array
    {
        $user = User::factory()->create();
        $category = $user->household->categories()->where('type', Category::TYPE_EXPENSE)->first();

        return [$user, $category];
    }

    private function transaction(User $user, Category $category, string $note, ?string $date = null): Transaction
    {
        return Transaction::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'category_id' => $category->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 15000,
            'transaction_date' => $date ?? now()->toDateString(),
            'note' => $note,
            'source' => Transaction::SOURCE_MANUAL,
        ]);
    }
}
