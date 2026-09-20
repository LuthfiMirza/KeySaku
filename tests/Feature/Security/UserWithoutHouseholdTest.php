<?php

namespace Tests\Feature\Security;

use App\Models\HouseholdMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * B2: User tanpa household.
 *
 * Investigasi:
 * - UserFactory::configure() selalu memanggil ProvisionHouseholdForUser → setiap user punya household.
 * - RegisteredUserController::store() selalu memanggil ProvisionHouseholdForUser.
 * - Tidak ada cara UI untuk keluar dari household tanpa transfer ownership terlebih dahulu.
 *
 * [CONFIRMED] Secara desain, setiap user yang dibuat via factory atau register
 * SELALU punya household. Kondisi "user tanpa household" tidak bisa terjadi
 * melalui jalur normal UI.
 *
 * Namun: kondisi ini BISA terjadi jika:
 * 1. Data dihapus langsung dari database (admin/migration)
 * 2. Bug masa depan menghapus HouseholdMember tanpa pemeriksaan
 *
 * Test ini membuktikan invarian register → household, dan mendokumentasikan
 * bahwa DashboardController AKAN crash (null->transactions()) jika household
 * null terjadi. Bug ini dicatat sebagai [SUSPECTED — Low Risk by Design].
 */
class UserWithoutHouseholdTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // Invarian: Register selalu membuat household
    // =========================================================================

    /** @test [CONFIRMED] UserFactory always provisions a household */
    public function user_factory_always_creates_household(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->household, 'UserFactory::configure() calls ProvisionHouseholdForUser');
        $this->assertNotNull($user->household->id);
    }

    /** @test [CONFIRMED] register endpoint always creates household for new user */
    public function register_endpoint_always_creates_household(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Test User',
            'email'                 => 'testuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(); // redirects to dashboard

        $user = User::where('email', 'testuser@example.com')->firstOrFail();
        $this->assertNotNull($user->household, 'Registered user must always have a household');
    }

    /** @test [CONFIRMED] register with valid invite_code joins existing household */
    public function register_with_invite_code_joins_existing_household(): void
    {
        // Create owner
        $owner = User::factory()->create();
        $inviteCode = $owner->household->invite_code;

        $this->post(route('register'), [
            'name'                  => 'New Member',
            'email'                 => 'newmember@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'invite_code'           => $inviteCode,
        ])->assertRedirect();

        $newMember = User::where('email', 'newmember@example.com')->firstOrFail();

        // Should be in the same household as owner
        $this->assertEquals(
            $owner->household->id,
            $newMember->household->id,
            'User who registers with invite code must join the existing household'
        );
    }

    // =========================================================================
    // [SUSPECTED — Low Risk] Dashboard crashes if household is null
    // Documented for future mitigation (middleware or null-safe accessor)
    // =========================================================================

    /** @test [SUSPECTED] user without household causes 500 on dashboard — BUG-B2-01 */
    public function user_without_household_causes_500_on_dashboard(): void
    {
        // Simulate the edge case: create user then remove household membership
        $user = User::factory()->create();
        $this->assertNotNull($user->household);

        // Force remove HouseholdMember record (simulates data anomaly / future feature bug)
        HouseholdMember::where('user_id', $user->id)->delete();
        $user->refresh();
        $this->assertNull($user->household, 'Setup: user should have no household now');

        // [BUG-B2-01] Fixed: EnsureHouseholdExists middleware now protects auth routes
        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect('/login')
                 ->assertSessionHasErrors('email');
    }
}
