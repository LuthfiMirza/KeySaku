<?php

namespace Tests\Feature\Services;

use App\Models\Category;
use App\Models\User;
use App\Services\CategorySuggester;
use App\Services\KeywordCategorySuggester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeywordCategorySuggesterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_suggests_a_category_from_a_matching_keyword(): void
    {
        $user = User::factory()->create();
        $categories = $user->household->categories()->get();
        $transportasi = $categories->firstWhere('name', 'Transportasi');

        $suggestion = (new KeywordCategorySuggester())->suggest(
            'beli bensin buat motor',
            Category::TYPE_EXPENSE,
            $categories
        );

        $this->assertNotNull($suggestion);
        $this->assertSame($transportasi->id, $suggestion->categoryId);
        $this->assertGreaterThanOrEqual(CategorySuggester::MINIMUM_CONFIDENCE, $suggestion->confidence);
    }

    public function test_it_returns_null_when_no_keyword_matches(): void
    {
        $user = User::factory()->create();
        $categories = $user->household->categories()->get();

        $suggestion = (new KeywordCategorySuggester())->suggest(
            'xyzxyz random text',
            Category::TYPE_EXPENSE,
            $categories
        );

        $this->assertNull($suggestion);
    }

    public function test_it_does_not_suggest_a_category_of_the_wrong_type(): void
    {
        $user = User::factory()->create();
        $categories = $user->household->categories()->get();

        // "gaji" is an income keyword; asking for expense should yield nothing.
        $suggestion = (new KeywordCategorySuggester())->suggest(
            'terima gaji bulan ini',
            Category::TYPE_EXPENSE,
            $categories
        );

        $this->assertNull($suggestion);
    }

    public function test_it_returns_null_for_empty_text(): void
    {
        $user = User::factory()->create();
        $categories = $user->household->categories()->get();

        $suggestion = (new KeywordCategorySuggester())->suggest('   ', Category::TYPE_EXPENSE, $categories);

        $this->assertNull($suggestion);
    }
}
