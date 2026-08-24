<?php

namespace Tests\Feature\Auth;

use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationHouseholdTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_without_invite_code_creates_a_new_household_as_owner(): void
    {
        $this->post('/register', [
            'name' => 'Pemilik Baru',
            'email' => 'pemilik-baru@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'pemilik-baru@example.com')->firstOrFail();

        $this->assertNotNull($user->household);
        $this->assertSame(HouseholdMember::ROLE_OWNER, $user->householdMembership->role);
        $this->assertSame(13, $user->household->categories()->count());
    }

    public function test_registration_with_valid_invite_code_joins_existing_household_as_member(): void
    {
        $owner = User::factory()->create();
        $household = $owner->household;
        $categoryCountBefore = $household->categories()->count();

        $this->post('/register', [
            'name' => 'Anggota Baru',
            'email' => 'anggota-baru@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invite_code' => $household->invite_code,
        ]);

        $member = User::where('email', 'anggota-baru@example.com')->firstOrFail();

        $this->assertTrue($member->household->is($household));
        $this->assertSame(HouseholdMember::ROLE_MEMBER, $member->householdMembership->role);
        $this->assertSame($categoryCountBefore, $household->categories()->count());
    }

    public function test_registration_with_invalid_invite_code_is_rejected(): void
    {
        $this->post('/register', [
            'name' => 'Salah Kode',
            'email' => 'salah-kode@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invite_code' => 'TIDAK-ADA',
        ])->assertSessionHasErrors('invite_code');

        $this->assertNull(User::where('email', 'salah-kode@example.com')->first());
    }

    public function test_joining_via_invite_code_ignores_the_initial_balance_field(): void
    {
        $owner = User::factory()->create();
        $household = $owner->household;
        $household->update(['initial_balance' => 5000000]);

        $this->post('/register', [
            'name' => 'Anggota Lain',
            'email' => 'anggota-lain@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'invite_code' => $household->invite_code,
            'initial_balance' => '999.000.000',
        ]);

        $this->assertSame('5000000.00', $household->fresh()->initial_balance);
    }
}
