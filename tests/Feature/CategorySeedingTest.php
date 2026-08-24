<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_created_via_factory_gets_default_categories(): void
    {
        $user = User::factory()->create();

        $this->assertSame(13, $user->categories()->count());
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
        $this->assertSame(13, $user->categories()->count());
    }

    public function test_seeded_categories_belong_only_to_the_new_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $this->assertSame(13, $userA->categories()->count());
        $this->assertSame(13, $userB->categories()->count());
        $this->assertSame(0, Category::where('user_id', $userA->id)->where('name', 'Makanan')->count() - 1);
        $this->assertFalse($userA->categories()->whereIn('id', $userB->categories()->pluck('id'))->exists());
    }
    public function test_deleting_all_categories_stays_empty_when_reopening_page(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->assertSame(13, $user->categories()->count());

        $user->categories()->delete();
        $this->assertSame(0, $user->fresh()->categories()->count());

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\CategoryManager::class)
            ->assertOk();

        $this->assertSame(0, $user->fresh()->categories()->count());
    }
}
