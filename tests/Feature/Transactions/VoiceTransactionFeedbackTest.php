<?php

namespace Tests\Feature\Transactions;

use App\Livewire\Transactions\VoiceTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VoiceTransactionFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_shows_pemasukan_for_an_income_draft(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(VoiceTransaction::class)
            ->set('transcript', 'terima gaji satu juta kategori gaji')
            ->call('parse')
            ->assertSee('Pemasukan')
            ->assertDontSee('Pengeluaran');
    }

    public function test_save_error_names_the_specific_missing_fields(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(VoiceTransaction::class)
            ->set('transcript', 'sesuatu tanpa nominal dan tanpa kategori')
            ->call('parse')
            ->call('save')
            ->assertHasErrors('parsedDraft')
            ->assertSee('Belum terbaca: nominal, kategori');
    }

    public function test_voice_page_warns_when_the_household_has_no_categories(): void
    {
        $user = User::factory()->create();
        $user->household->categories()->delete();

        Livewire::actingAs($user)
            ->test(VoiceTransaction::class)
            ->assertSee('Belum ada kategori.')
            ->assertSee('Buat Kategori');
    }

    public function test_voice_page_hides_the_category_warning_when_categories_exist(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(VoiceTransaction::class)
            ->assertDontSee('Belum ada kategori.');
    }
}
