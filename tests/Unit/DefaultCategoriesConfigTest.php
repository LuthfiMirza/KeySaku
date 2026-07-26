<?php

namespace Tests\Unit;

use App\Models\Category;
use PHPUnit\Framework\TestCase;

class DefaultCategoriesConfigTest extends TestCase
{
    public function test_config_contains_thirteen_entries_with_expected_structure(): void
    {
        $defaults = require __DIR__.'/../../config/default-categories.php';

        $this->assertIsArray($defaults);
        $this->assertCount(13, $defaults);

        $validTypes = [Category::TYPE_EXPENSE, Category::TYPE_INCOME];

        foreach ($defaults as $entry) {
            $this->assertIsArray($entry);
            $this->assertArrayHasKey('name', $entry);
            $this->assertArrayHasKey('type', $entry);
            $this->assertArrayHasKey('color', $entry);
            $this->assertArrayHasKey('icon', $entry);
            $this->assertIsString($entry['name']);
            $this->assertContains($entry['type'], $validTypes);
        }

        $expenseCount = count(array_filter($defaults, fn ($e) => $e['type'] === Category::TYPE_EXPENSE));
        $incomeCount = count(array_filter($defaults, fn ($e) => $e['type'] === Category::TYPE_INCOME));

        $this->assertSame(8, $expenseCount);
        $this->assertSame(5, $incomeCount);
    }
}
