<?php

namespace Tests\Feature\Transactions;

use App\Livewire\Transactions\CreateTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTransactionCategorySuggestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_typing_a_note_with_a_matching_keyword_selects_the_category(): void
    {
        $user = User::factory()->create();
        $transportasi = $user->household->categories()->where('name', 'Transportasi')->firstOrFail();

        Livewire::actingAs($user)
            ->test(CreateTransaction::class)
            ->set('note', 'isi bensin motor')
            ->assertSet('category_id', $transportasi->id);
    }

    public function test_a_note_with_no_matching_keyword_does_not_change_the_category(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(CreateTransaction::class);

        $initialCategoryId = $component->get('category_id');

        $component
            ->set('note', 'sesuatu yang tidak dikenali sama sekali')
            ->assertSet('category_id', $initialCategoryId);
    }
}
