<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackfillCategoriesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_backfill_seeds_categories_only_for_users_with_none(): void
    {
        $userWithout = User::factory()->createQuietly(); // observer skipped
        $userWith = User::factory()->create();            // observer seeds 13

        $this->assertSame(0, $userWithout->categories()->count());
        $this->assertSame(13, $userWith->categories()->count());

        $this->artisan('categories:backfill')->assertSuccessful();

        $this->assertSame(13, $userWithout->fresh()->categories()->count());
        $this->assertSame(13, $userWith->fresh()->categories()->count());
    }

    public function test_backfill_is_idempotent(): void
    {
        $user = User::factory()->createQuietly();
        $this->assertSame(0, $user->categories()->count());

        $this->artisan('categories:backfill')->assertSuccessful();
        $this->artisan('categories:backfill')->assertSuccessful();

        $this->assertSame(13, $user->fresh()->categories()->count());
    }

    public function test_dry_run_reports_but_does_not_change_data(): void
    {
        $user = User::factory()->createQuietly();

        $this->artisan('categories:backfill', ['--dry-run' => true])
            ->expectsOutputToContain('[dry-run]')
            ->expectsOutputToContain('Would process 1 user(s)')
            ->assertSuccessful();

        $this->assertSame(0, $user->fresh()->categories()->count());
    }
}
