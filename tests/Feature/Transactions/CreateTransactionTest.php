<?php

namespace Tests\Feature\Transactions;

use App\Livewire\Transactions\CreateTransaction;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_amount_with_thousand_separator_is_accepted(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Makanan',
            'type' => Category::TYPE_EXPENSE,
            'color' => '#f97316',
            'icon' => '🍜',
        ]);

        Livewire::actingAs($user)
            ->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_EXPENSE)
            ->set('category_id', $category->id)
            ->set('amount', '50.000')
            ->set('transaction_date', now()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 50000,
        ]);
    }

    public function test_large_amount_with_multiple_separators_is_accepted(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Gaji',
            'type' => Category::TYPE_INCOME,
            'color' => '#10b981',
            'icon' => '💼',
        ]);

        Livewire::actingAs($user)
            ->test(CreateTransaction::class)
            ->set('type', Transaction::TYPE_INCOME)
            ->set('category_id', $category->id)
            ->set('amount', '1.500.000')
            ->set('transaction_date', now()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 1500000,
        ]);
    }
}
