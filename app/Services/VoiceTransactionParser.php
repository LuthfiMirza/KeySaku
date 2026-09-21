<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VoiceTransactionParser
{
    public function __construct(private ?CategorySuggester $categorySuggester = null) {}

    /**
     * @param  Collection<int, Category>  $categories
     * @return array{type: string, amount: float|null, category_id: int|null, category_name: string|null, transaction_date: string, note: string, raw_voice_text: string, warnings: array<int, string>}
     */
    public function parse(string $transcript, Collection $categories): array
    {
        $rawTranscript = trim($transcript);
        $workingText = $this->normalizeText($rawTranscript);
        $warnings = [];

        [$transactionDate, $workingText] = $this->extractDate($workingText);
        [$type, $workingText] = $this->extractType($workingText);
        [$category, $workingText] = $this->extractCategory($workingText, $categories);
        [$amount, $workingText] = $this->extractAmount($workingText);

        $note = $this->cleanNote($workingText);

        if ($category === null && $this->categorySuggester !== null) {
            $suggestion = $this->categorySuggester->suggest(
                $note,
                $type ?? Transaction::TYPE_EXPENSE,
                $categories
            );

            if ($suggestion !== null && $suggestion->confidence >= CategorySuggester::MINIMUM_CONFIDENCE) {
                $category = $categories->firstWhere('id', $suggestion->categoryId);
            }
        }

        $type = $type ?? $category?->type ?? Transaction::TYPE_EXPENSE;

        if ($amount === null) {
            $warnings[] = 'Nominal belum terbaca. Tambahkan nominal seperti ‘dua puluh lima ribu’ atau ‘25.000’.';
        }

        if ($category === null) {
            $warnings[] = 'Kategori belum cocok. Sebutkan seperti ‘kategori makanan’.';
        }

        if ($note === '') {
            $warnings[] = 'Catatan belum terbaca. Tambahkan keterangan pengeluaran.';
        }

        return [
            'type' => $type,
            'amount' => $amount,
            'category_id' => $category?->id,
            'category_name' => $category?->name,
            'transaction_date' => $transactionDate,
            'note' => $note,
            'raw_voice_text' => $rawTranscript,
            'warnings' => $warnings,
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function extractDate(string $text): array
    {
        $date = now();

        $phrases = [
            'hari ini' => now(),
            'kemarin' => now()->subDay(),
            'kemaren' => now()->subDay(),
        ];

        foreach ($phrases as $phrase => $resolvedDate) {
            if (preg_match('/\b'.preg_quote($phrase, '/').'\b/u', $text)) {
                return [
                    $resolvedDate->toDateString(),
                    trim(preg_replace('/\b'.preg_quote($phrase, '/').'\b/u', '', $text) ?? $text),
                ];
            }
        }

        return [$date->toDateString(), $text];
    }

    /**
     * @return array{0: string|null, 1: string}
     */
    private function extractType(string $text): array
    {
        $incomeKeywords = [
            'pemasukan',
            'pendapatan',
            'masuk',
            'terima',
            'menerima',
            'dapat',
            'dapet',
            'gaji',
            'gajian',
            'salary',
            'upah',
            'honor',
            'bonus',
            'thr',
            'usaha',
            'jualan',
            'omzet',
            'omset',
            'untung',
            'cuan',
            'laba',
            'hadiah',
            'transferan',
            'kiriman',
            'sangu',
            'uang saku',
            'komisi',
            'cashback',
        ];

        $expenseKeywords = [
            'pengeluaran',
            'keluar',
            'beli',
            'belanja',
            'bayar',
            'makan',
            'parkir',
        ];

        foreach ($incomeKeywords as $keyword) {
            if (preg_match('/\b'.preg_quote($keyword, '/').'\b/u', $text)) {
                return [Transaction::TYPE_INCOME, $text];
            }
        }

        foreach ($expenseKeywords as $keyword) {
            if (preg_match('/\b'.preg_quote($keyword, '/').'\b/u', $text)) {
                return [Transaction::TYPE_EXPENSE, $text];
            }
        }

        return [null, $text];
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
        $normalizedCandidate = $this->normalizeCategoryText($candidate);
        $matchedCategory = $categories->first(function (Category $category) use ($candidate) {
            $categoryName = Str::lower($category->name);
            $candidateText = Str::lower($candidate);

            return $categoryName === $candidateText
                || $this->wordBoundaryContains($candidateText, $categoryName);
        });

        if (! $matchedCategory) {
            $matchedCategory = $categories->first(function (Category $category) use ($normalizedCandidate) {
                $categoryName = $this->normalizeCategoryText($category->name);

                return $categoryName === $normalizedCandidate
                    || $this->wordBoundaryContains($normalizedCandidate, $categoryName)
                    || $this->wordBoundaryContains($categoryName, $normalizedCandidate)
                    || $this->fuzzyMatches($categoryName, $normalizedCandidate);
            });
        }

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
        if (preg_match('/\b([0-9]+)\s*(?:juta|jt)\s+([0-9]{1,3})(?:\s*(?:ribu|rb))?\b/u', $text, $matches)) {
            $millions = (int) $matches[1] * 1000000;
            $thousands = (int) $matches[2] * 1000;
            $amountText = $matches[0];

            return [(float) ($millions + $thousands), trim(str_replace($amountText, '', $text))];
        }

        $patterns = [
            '/\brp\s*([0-9]+(?:\.[0-9]{3})+|[0-9]+)\b/u',
            '/\b([0-9]+(?:\.[0-9]{3})+)\b/u',
            '/\b([0-9]+(?:\.[0-9]+)?)\s*(?:juta|jt)\b/u',
            '/\b([0-9]+)\s*(?:ribu|rb)\b/u',
            '/\b([0-9]+)\b/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                if (str_contains($matches[0], 'juta') || str_contains($matches[0], 'jt')) {
                    $amount = (string) ((float) $matches[1] * 1000000);
                } else {
                    $amount = str_replace('.', '', $matches[1]);
                    if (str_contains($matches[0], 'ribu') || str_contains($matches[0], 'rb')) {
                        $amount = (string) ((int) $amount * 1000);
                    }
                }

                return [(float) $amount, trim(str_replace($matches[0], '', $text))];
            }
        }

        $wordAmounts = [
            'seratus lima puluh ribu' => 150000,
            'dua puluh lima ribu' => 25000,
            'tiga puluh ribu' => 30000,
            'dua puluh ribu' => 20000,
            'lima belas ribu' => 15000,
            'seratus ribu' => 100000,
            'sepuluh ribu' => 10000,
            'lima ribu' => 5000,
            'lima juta' => 5000000,
            'satu juta' => 1000000,
            'setengah juta' => 500000,
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
        $text = str_replace(['“', '”', '"'], ' ', $text);
        $text = str_replace(',', '.', $text); // Convert Indonesian decimal comma to dot
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function normalizeCategoryText(string $text): string
    {
        $text = $this->normalizeText($text);
        $text = preg_replace('/\b(dan|untuk|sebesar|senilai)\b.*$/u', '', $text) ?? $text;

        return trim($text);
    }

    private function wordBoundaryContains(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return false;
        }

        $pattern = '/\b'.preg_quote($needle, '/').'\b/u';

        return preg_match($pattern, $haystack) === 1;
    }

    private function fuzzyMatches(string $categoryName, string $candidate): bool
    {
        $minLength = min(strlen($categoryName), strlen($candidate));

        if ($minLength < 5) {
            return false;
        }

        $threshold = min(2, (int) floor($minLength / 4));

        return levenshtein($categoryName, $candidate) <= $threshold;
    }

    private function cleanNote(string $text): string
    {
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
