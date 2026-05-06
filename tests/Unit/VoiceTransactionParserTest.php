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
        return new VoiceTransactionParser();
    }

    private function categories(): Collection
    {
        return collect([
            $this->category(1, 'Makanan'),
            $this->category(2, 'Transportasi'),
            $this->category(3, 'Belanja Dapur'),
            $this->category(4, 'Kesehatan'),
        ]);
    }

    private function category(int $id, string $name): Category
    {
        $category = new Category(['name' => $name, 'type' => Category::TYPE_EXPENSE]);
        $category->id = $id;

        return $category;
    }
}
