<?php

namespace App\Services;

class CategorySuggestion
{
    public function __construct(
        public readonly int $categoryId,
        public readonly float $confidence,
    ) {
    }
}
