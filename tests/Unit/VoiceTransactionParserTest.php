<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Services\VoiceTransactionParser;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class VoiceTransactionParserTest extends TestCase
{
    public function test_it_parses_simple_indonesian_word_amount_and_category(): void
    {
        $result = $this->parser()->parse(
            'Beli nasi goreng dua puluh lima ribu kategori makanan',
            $this->categories()
        );

        $this->assertSame('expense', $result['type']);
        $this->assertSame(25000.0, $result['amount']);
        $this->assertSame(1, $result['category_id']);
        $this->assertSame('Makanan', $result['category_name']);
        $this->assertSame('beli nasi goreng', $result['note']);
        $this->assertSame([], $result['warnings']);
    }

    public function test_it_parses_numeric_rupiah_amount(): void
    {
        $result = $this->parser()->parse(
            'Belanja dapur Rp150.000 kategori belanja dapur',
            $this->categories()
        );

        $this->assertSame(150000.0, $result['amount']);
        $this->assertSame(3, $result['category_id']);
        $this->assertSame('belanja dapur', $result['note']);
    }

    public function test_it_parses_short_ribu_amount(): void
    {
        $result = $this->parser()->parse(
            'Bayar parkir 25rb kategori transportasi',
            $this->categories()
        );

        $this->assertSame(25000.0, $result['amount']);
        $this->assertSame(2, $result['category_id']);
        $this->assertSame('bayar parkir', $result['note']);
    }

    public function test_it_parses_income_voice_transaction(): void
    {
        $result = $this->parser()->parse(
            'Terima gaji satu juta kategori gaji',
            $this->categories()
        );

        $this->assertSame('income', $result['type']);
        $this->assertSame(1000000.0, $result['amount']);
        $this->assertSame(5, $result['category_id']);
        $this->assertSame('Gaji', $result['category_name']);
        $this->assertSame('terima gaji', $result['note']);
    }

    public function test_it_parses_gajian_as_income(): void
    {
        $result = $this->parser()->parse(
            'gajian 5 juta',
            $this->categories()
        );

        $this->assertSame('income', $result['type']);
        $this->assertSame(5000000.0, $result['amount']);
        $this->assertSame('gajian', $result['note']);
    }

    public function test_it_parses_numeric_juta_amount(): void
    {
        $result = $this->parser()->parse(
            'Terima kasih 2 juta kategori gaji',
            $this->categories()
        );

        $this->assertSame('income', $result['type']);
        $this->assertSame(2000000.0, $result['amount']);
        $this->assertSame(5, $result['category_id']);
        $this->assertSame('terima kasih', $result['note']);
    }

    public function test_it_parses_category_followed_by_today(): void
    {
        $result = $this->parser()->parse(
            'dari gergaji 5 juta kategori gaji hari ini',
            $this->categories()
        );

        $this->assertSame('income', $result['type']);
        $this->assertSame(5000000.0, $result['amount']);
        $this->assertSame(5, $result['category_id']);
        $this->assertSame(now()->toDateString(), $result['transaction_date']);
        $this->assertSame('dari gergaji', $result['note']);
        $this->assertSame([], $result['warnings']);
    }

    public function test_it_parses_mixed_juta_and_thousand_amount(): void
    {
        $result = $this->parser()->parse(
            'terima gaji 2 juta 500 kategori gaji',
            $this->categories()
        );

        $this->assertSame('income', $result['type']);
        $this->assertSame(2500000.0, $result['amount']);
        $this->assertSame(5, $result['category_id']);
        $this->assertSame('terima gaji', $result['note']);
        $this->assertSame([], $result['warnings']);
    }

    public function test_it_parses_yesterday_date_phrase(): void
    {
        $result = $this->parser()->parse(
            'beli nasi 25 ribu kategori makanan kemarin',
            $this->categories()
        );

        $this->assertSame('expense', $result['type']);
        $this->assertSame(25000.0, $result['amount']);
        $this->assertSame(1, $result['category_id']);
        $this->assertSame(now()->subDay()->toDateString(), $result['transaction_date']);
        $this->assertSame('beli nasi', $result['note']);
    }

    public function test_explicit_expense_keyword_wins_and_partial_category_is_rejected(): void
    {
        $result = $this->parser()->parse(
            'beli gajih 20 ribu kategori gajih',
            $this->categories()
        );

        $this->assertSame('expense', $result['type']);
        $this->assertNull($result['category_id']);
        $this->assertNull($result['category_name']);
    }

    public function test_category_containing_needle_still_matches_when_needle_is_whole_word(): void
    {
        $result = $this->parser()->parse(
            'catat kategori makanan siang',
            $this->categories()
        );

        $this->assertSame(1, $result['category_id']);
        $this->assertSame('Makanan', $result['category_name']);
    }

    public function test_typo_within_levenshtein_threshold_still_matches(): void
    {
        $result = $this->parser()->parse(
            'catat 10 ribu kategori transportsi',
            $this->categories()
        );

        $this->assertSame(2, $result['category_id']);
        $this->assertSame('Transportasi', $result['category_name']);
    }

    public function test_exact_category_name_still_matches(): void
    {
        $result = $this->parser()->parse(
            'beli 5 ribu kategori makanan',
            $this->categories()
        );

        $this->assertSame(1, $result['category_id']);
        $this->assertSame('Makanan', $result['category_name']);
    }

    public function test_it_returns_warnings_when_amount_and_category_are_missing(): void
    {
        $result = $this->parser()->parse('Beli sesuatu', $this->categories());

        $this->assertNull($result['amount']);
        $this->assertNull($result['category_id']);
        $this->assertContains('Nominal belum terbaca. Tambahkan nominal seperti ‘dua puluh lima ribu’ atau ‘25.000’.', $result['warnings']);
        $this->assertContains('Kategori belum cocok. Sebutkan seperti ‘kategori makanan’.', $result['warnings']);
    }

    private function parser(): VoiceTransactionParser
    {
        return new VoiceTransactionParser;
    }

    private function categories(): Collection
    {
        return collect([
            $this->category(1, 'Makanan'),
            $this->category(2, 'Transportasi'),
            $this->category(3, 'Belanja Dapur'),
            $this->category(4, 'Kesehatan'),
            $this->category(5, 'Gaji', Category::TYPE_INCOME),
            $this->category(6, 'Bonus', Category::TYPE_INCOME),
        ]);
    }

    private function category(int $id, string $name, string $type = Category::TYPE_EXPENSE): Category
    {
        $category = new Category(['name' => $name, 'type' => $type]);
        $category->id = $id;

        return $category;
    }
}
