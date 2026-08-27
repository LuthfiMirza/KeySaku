<?php

namespace Tests\Feature\Transactions;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TimezoneAffectsDefaultDateTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        date_default_timezone_set(config('app.timezone'));

        parent::tearDown();
    }

    public function test_default_transaction_date_reflects_the_users_own_timezone_near_midnight(): void
    {
        // 23:30 in Jakarta (WIB, UTC+7) is already 01:30 the next day
        // in Jayapura (WIT, UTC+9) - two different calendar dates for
        // the exact same instant. A real HTTP request is required here
        // (not Livewire::test(), which bypasses the web middleware
        // group and never runs SetUserTimezone).
        Carbon::setTestNow(Carbon::parse('2026-03-15 16:30:00', 'UTC'));

        $wibUser = User::factory()->create(['timezone' => 'Asia/Jakarta']);
        $witUser = User::factory()->create(['timezone' => 'Asia/Jayapura']);

        $wibContent = $this->actingAs($wibUser)->get(route('transactions.create'))->getContent();
        $witContent = $this->actingAs($witUser)->get(route('transactions.create'))->getContent();

        $this->assertStringContainsString('2026-03-15', $wibContent);
        $this->assertStringContainsString('2026-03-16', $witContent);
    }
}
