<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

interface CategorySuggester
{
    /**
     * Callers should never pre-select a suggestion below this confidence.
     */
    public const MINIMUM_CONFIDENCE = 0.5;

    /**
     * @param  Collection<int, Category>  $categories  Candidates to suggest from, already scoped to the household.
     */
    public function suggest(string $text, string $type, Collection $categories): ?CategorySuggestion;
}
