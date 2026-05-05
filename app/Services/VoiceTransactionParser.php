<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VoiceTransactionParser
{
    /**
     * @param  Collection<int, Category>  $categories
     * @return array{type: string, amount: float|null, category_id: int|null, category_name: string|null, transaction_date: string, note: string, raw_voice_text: string, warnings: array<int, string>}
     */
    public function parse(string $transcript, Collection $categories): array
    {
        $rawTranscript = trim($transcript);
        $workingText = $this->normalizeText($rawTranscript);
        $warnings = [];

        [$category, $workingText] = $this->extractCategory($workingText, $categories);
        [$amount, $workingText] = $this->extractAmount($workingText);

        $note = $this->cleanNote($workingText);

        if ($amount === null) {
            $warnings[] = 'Nominal belum bisa dikenali.';
        }

        if ($category === null) {
            $warnings[] = 'Kategori belum ditemukan.';
        }

        if ($note === '') {
            $warnings[] = 'Catatan transaksi masih kosong.';
        }

        return [
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => $amount,
            'category_id' => $category?->id,
            'category_name' => $category?->name,
            'transaction_date' => now()->toDateString(),
            'note' => $note,
            'raw_voice_text' => $rawTranscript,
            'warnings' => $warnings,
        ];
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @return array{0: Category|null, 1: string}
     */
    private function extractCategory(string $text, Collection $categories): array
    {
        if (! preg_match('/\bkategori\s+(.+)$/u', $text, $matches)) {
            return [null, $text];
        }

        $candidate = trim($matches[1]);
        $matchedCategory = $categories->first(function (Category $category) use ($candidate) {
            return Str::lower($category->name) === Str::lower($candidate);
        });

        if (! $matchedCategory) {
            return [null, preg_replace('/\bkategori\s+.+$/u', '', $text) ?? $text];
        }

        return [$matchedCategory, preg_replace('/\bkategori\s+.+$/u', '', $text) ?? $text];
    }

    /**
     * @return array{0: float|null, 1: string}
     */
    private function extractAmount(string $text): array
    {
        $patterns = [
            '/\brp\s*([0-9]+(?:\.[0-9]{3})+|[0-9]+)\b/u',
            '/\b([0-9]+(?:\.[0-9]{3})+)\b/u',
            '/\b([0-9]+)\s*(?:ribu|rb)\b/u',
            '/\b([0-9]+)\b/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $amount = str_replace('.', '', $matches[1]);

                if (str_contains($matches[0], 'ribu') || str_contains($matches[0], 'rb')) {
                    $amount = (string) ((int) $amount * 1000);
                }

                return [(float) $amount, trim(str_replace($matches[0], '', $text))];
            }
        }

        $wordAmounts = [
            'dua puluh lima ribu' => 25000,
            'tiga puluh ribu' => 30000,
            'dua puluh ribu' => 20000,
            'seratus ribu' => 100000,
            'sepuluh ribu' => 10000,
            'lima ribu' => 5000,
            'satu juta' => 1000000,
        ];

        foreach ($wordAmounts as $phrase => $amount) {
            if (str_contains($text, $phrase)) {
                return [(float) $amount, trim(str_replace($phrase, '', $text))];
            }
        }

        return [null, $text];
    }

    private function normalizeText(string $text): string
    {
        $text = Str::lower($text);
        $text = str_replace(['“', '”', '"', ','], ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function cleanNote(string $text): string
    {
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
