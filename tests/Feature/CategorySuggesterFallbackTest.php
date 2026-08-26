<?php

namespace Tests\Feature;

use App\Livewire\Transactions\VoiceTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategorySuggesterFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_voice_parser_suggests_category_from_keyword_without_explicit_kategori_phrase(): void
    {
        $user = User::factory()->create();

        $draft = Livewire::actingAs($user)
            ->test(VoiceTransaction::class)
            ->set('transcript', 'beli bensin 50 ribu')
            ->call('parse')
            ->get('parsedDraft');

        $this->assertSame('expense', $draft['type']);
        $this->assertSame('Transportasi', $draft['category_name']);
        $this->assertSame(50000.0, $draft['amount']);
    }
}
