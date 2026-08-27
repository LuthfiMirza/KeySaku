<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTimezoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_default_to_the_app_timezone(): void
    {
        $user = User::factory()->create();

        $this->assertSame(config('app.timezone'), $user->fresh()->timezone);
    }

    public function test_authenticated_requests_use_the_users_timezone_for_now(): void
    {
        $user = User::factory()->create(['timezone' => 'America/New_York']);

        $this->actingAs($user)->get(route('dashboard'));

        $this->assertSame('America/New_York', date_default_timezone_get());
    }

    public function test_guest_requests_keep_the_app_default_timezone(): void
    {
        $this->get(route('login'));

        $this->assertSame(config('app.timezone'), date_default_timezone_get());
    }
}
