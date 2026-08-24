<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitialBalanceSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_initial_balance_settings_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings.initial-balance'))
            ->assertOk()
            ->assertSee('Saldo Awal');
    }

    public function test_user_can_update_initial_balance_with_thousand_separator(): void
    {
        $user = User::factory()->create(['initial_balance' => 0]);

        $this->actingAs($user)
            ->patch(route('settings.initial-balance.update'), [
                'initial_balance' => '5.500.000',
            ])
            ->assertRedirect();

        $this->assertSame('5500000.00', $user->fresh()->initial_balance);
    }

    public function test_empty_initial_balance_is_rejected(): void
    {
        $user = User::factory()->create(['initial_balance' => 1000]);

        $this->actingAs($user)
            ->patch(route('settings.initial-balance.update'), [
                'initial_balance' => '',
            ])
            ->assertSessionHasErrors('initial_balance');

        $this->assertSame('1000.00', $user->fresh()->initial_balance);
    }
}
