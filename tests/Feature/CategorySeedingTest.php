<?php

namespace Tests\Feature;

use App\Livewire\CategoryManager;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategorySeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_created_via_factory_gets_default_categories(): void
    {
        $user = User::factory()->create();

        $this->assertSame(count(config('default-categories')), $user->categories()->count());
        $this->assertTrue($user->categories()->where('name', 'Makanan')->where('type', Category::TYPE_EXPENSE)->exists());
        $this->assertTrue($user->categories()->where('name', 'Gaji')->where('type', Category::TYPE_INCOME)->exists());
    }

    public function test_user_registered_via_http_gets_default_categories(): void
    {
        $response = $this->post('/register', [
            'name' => 'Baru',
            'email' => 'baru@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'baru@example.test')->firstOrFail();
        $this->assertSame(count(config('default-categories')), $user->categories()->count());
    }

    public function test_seeded_categories_belong_only_to_the_new_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $this->assertSame(count(config('default-categories')), $userA->categories()->count());
        $this->assertSame(count(config('default-categories')), $userB->categories()->count());
        $this->assertSame(0, Category::where('user_id', $userA->id)->where('name', 'Makanan')->count() - 1);
        $this->assertFalse($userA->categories()->whereIn('id', $userB->categories()->pluck('id'))->exists());
    }

    public function test_deleting_all_categories_stays_empty_when_reopening_page(): void
    {
        $user = User::factory()->create();
        $this->assertSame(count(config('default-categories')), $user->categories()->count());

        $user->categories()->delete();
        $this->assertSame(0, $user->fresh()->categories()->count());

        Livewire::actingAs($user)
            ->test(CategoryManager::class)
            ->assertOk();

        $this->assertSame(0, $user->fresh()->categories()->count());
    }
}
