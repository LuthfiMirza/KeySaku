<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimezoneSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_timezone_settings_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings.timezone'))
            ->assertOk()
            ->assertSee('Zona Waktu')
            ->assertSee('Asia/Jakarta');
    }

    public function test_user_can_update_timezone(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('settings.timezone.update'), [
                'timezone' => 'Asia/Jayapura',
            ])
            ->assertRedirect();

        $this->assertSame('Asia/Jayapura', $user->fresh()->timezone);
    }

    public function test_invalid_timezone_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('settings.timezone.update'), [
                'timezone' => 'America/New_York',
            ])
            ->assertSessionHasErrors('timezone');

        $this->assertSame('Asia/Jakarta', $user->fresh()->timezone);
    }
}
