<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BelongsToUserTraitTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_id_is_auto_filled_from_the_authenticated_user_when_missing(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create([
            'name' => 'Tanpa user_id eksplisit',
            'type' => Category::TYPE_EXPENSE,
        ]);

        $this->assertSame($user->id, $category->user_id);
    }

    public function test_explicit_user_id_is_not_overridden_by_the_authenticated_user(): void
    {
        $owner = User::factory()->create();
        $actor = User::factory()->create();
        $this->actingAs($actor);

        $category = Category::create([
            'user_id' => $owner->id,
            'name' => 'user_id eksplisit',
            'type' => Category::TYPE_EXPENSE,
        ]);

        $this->assertSame($owner->id, $category->user_id);
    }
}
