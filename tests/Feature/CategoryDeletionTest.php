<?php

namespace Tests\Feature;

use App\Livewire\CategoryManager;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_with_transactions_is_not_deleted(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->where('name', 'Makanan')->firstOrFail();

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 25000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        Livewire::actingAs($user)
            ->test(CategoryManager::class)
            ->call('delete', $category->id)
            ->assertHasNoErrors();

        $this->assertNotNull(Category::find($category->id), 'Kategori yang masih dipakai tidak boleh terhapus.');
        $this->assertSame(1, Transaction::where('category_id', $category->id)->count());
    }

    public function test_category_without_transactions_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->where('name', 'Hiburan')->firstOrFail();

        Livewire::actingAs($user)
            ->test(CategoryManager::class)
            ->call('delete', $category->id)
            ->assertHasNoErrors();

        $this->assertNull(Category::find($category->id));
    }

    public function test_delete_never_raises_query_exception_for_in_use_category(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->where('name', 'Makanan')->firstOrFail();

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 10000,
            'transaction_date' => now()->toDateString(),
            'source' => Transaction::SOURCE_MANUAL,
        ]);

        // If the guard were missing, the delete() below would raise
        // Illuminate\Database\QueryException (SQLSTATE 23000). We assert
        // that Livewire call returns cleanly instead.
        Livewire::actingAs($user)
            ->test(CategoryManager::class)
            ->call('delete', $category->id)
            ->assertHasNoErrors();

        $this->assertTrue(true);
    }
}
