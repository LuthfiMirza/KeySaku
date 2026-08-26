<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class KeywordCategorySuggester implements CategorySuggester
{
    public const CONFIDENCE = 0.75;

    public function suggest(string $text, string $type, Collection $categories): ?CategorySuggestion
    {
        $normalized = Str::lower($text);

        if (trim($normalized) === '') {
            return null;
        }

        foreach (config('category-keywords', []) as $categoryName => $keywords) {
            foreach ($keywords as $keyword) {
                if (! preg_match('/\b'.preg_quote(Str::lower($keyword), '/').'\b/u', $normalized)) {
                    continue;
                }

                $category = $categories->first(
                    fn (Category $candidate) => $candidate->type === $type
                        && Str::lower($candidate->name) === Str::lower($categoryName)
                );

                if ($category) {
                    return new CategorySuggestion($category->id, self::CONFIDENCE);
                }
            }
        }

        return null;
    }
}
