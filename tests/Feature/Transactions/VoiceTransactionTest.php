<?php

namespace Tests\Feature\Transactions;

use App\Livewire\Transactions\VoiceTransaction;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VoiceTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_parsed_voice_transaction(): void
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
            ->test(VoiceTransaction::class)
            ->set('transcript', 'Beli nasi goreng dua puluh lima ribu kategori makanan')
            ->call('parse')
            ->call('save')
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => Transaction::TYPE_EXPENSE,
            'amount' => 25000.00,
            'note' => 'beli nasi goreng',
            'source' => Transaction::SOURCE_VOICE,
            'raw_voice_text' => 'Beli nasi goreng dua puluh lima ribu kategori makanan',
        ]);
    }

    public function test_user_cannot_save_incomplete_voice_draft(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(VoiceTransaction::class)
            ->set('transcript', 'Beli sesuatu')
            ->call('parse')
            ->call('save')
            ->assertHasErrors(['parsedDraft']);

        $this->assertDatabaseCount('transactions', 0);
    }
}
