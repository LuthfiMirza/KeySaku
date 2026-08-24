<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HouseholdFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_household_tables_have_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('households', [
            'id',
            'name',
            'invite_code',
            'created_at',
            'updated_at',
        ]));

        $this->assertTrue(Schema::hasColumns('household_members', [
            'id',
            'household_id',
            'user_id',
            'role',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_household_relationships_are_available(): void
    {
        $user = User::factory()->create();
        $household = Household::create([
            'name' => 'Keluarga Test',
            'invite_code' => Household::generateInviteCode(),
        ]);

        $membership = HouseholdMember::create([
            'household_id' => $household->id,
            'user_id' => $user->id,
            'role' => HouseholdMember::ROLE_OWNER,
        ]);

        $this->assertTrue($household->members->contains($membership));
        $this->assertTrue($membership->household->is($household));
        $this->assertTrue($membership->user->is($user));
        $this->assertTrue($user->household->is($household));
    }

    public function test_generate_invite_code_produces_unique_codes(): void
    {
        $first = Household::generateInviteCode();
        Household::create(['invite_code' => $first]);

        $second = Household::generateInviteCode();

        $this->assertNotSame($first, $second);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{4}-[A-Z0-9]{4}$/', $first);
    }

    public function test_a_user_cannot_belong_to_two_households(): void
    {
        $user = User::factory()->create();
        $householdA = Household::create(['invite_code' => Household::generateInviteCode()]);
        $householdB = Household::create(['invite_code' => Household::generateInviteCode()]);

        HouseholdMember::create([
            'household_id' => $householdA->id,
            'user_id' => $user->id,
            'role' => HouseholdMember::ROLE_OWNER,
        ]);

        $this->expectException(QueryException::class);

        HouseholdMember::create([
            'household_id' => $householdB->id,
            'user_id' => $user->id,
            'role' => HouseholdMember::ROLE_MEMBER,
        ]);
    }
}
