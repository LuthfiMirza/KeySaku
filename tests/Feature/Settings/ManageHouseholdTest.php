<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\ManageHousehold;
use App\Models\HouseholdMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageHouseholdTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_open_the_household_settings_page(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->get(route('settings.household'))
            ->assertOk()
            ->assertSee('Anggota Keluarga')
            ->assertSee($owner->household->invite_code);
    }

    public function test_owner_can_remove_a_member(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $household = $owner->household;

        $member->householdMembership->delete();
        HouseholdMember::create([
            'household_id' => $household->id,
            'user_id' => $member->id,
            'role' => HouseholdMember::ROLE_MEMBER,
        ]);

        $memberRow = $household->members()->where('user_id', $member->id)->firstOrFail();

        Livewire::actingAs($owner)
            ->test(ManageHousehold::class)
            ->call('removeMember', $memberRow->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('household_members', ['id' => $memberRow->id]);
        $this->assertNotNull($member->fresh()->household);
        $this->assertNotSame($household->id, $member->fresh()->household->id);
    }

    public function test_owner_cannot_remove_themselves(): void
    {
        $owner = User::factory()->create();
        $ownMembership = $owner->householdMembership;

        Livewire::actingAs($owner)
            ->test(ManageHousehold::class)
            ->call('removeMember', $ownMembership->id)
            ->assertHasErrors('member');

        $this->assertDatabaseHas('household_members', ['id' => $ownMembership->id]);
    }

    public function test_member_can_leave_and_receives_a_fresh_household(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $household = $owner->household;

        $member->householdMembership->delete();
        HouseholdMember::create([
            'household_id' => $household->id,
            'user_id' => $member->id,
            'role' => HouseholdMember::ROLE_MEMBER,
        ]);
        $member = $member->fresh();

        Livewire::actingAs($member)
            ->test(ManageHousehold::class)
            ->call('leaveHousehold')
            ->assertRedirect(route('settings'));

        $this->assertNotSame($household->id, $member->fresh()->household->id);
        $this->assertSame(1, $household->fresh()->members()->count());
    }

    public function test_owner_cannot_leave_while_other_members_remain(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $household = $owner->household;

        $member->householdMembership->delete();
        HouseholdMember::create([
            'household_id' => $household->id,
            'user_id' => $member->id,
            'role' => HouseholdMember::ROLE_MEMBER,
        ]);

        Livewire::actingAs($owner)
            ->test(ManageHousehold::class)
            ->call('leaveHousehold')
            ->assertHasErrors('leave');

        $this->assertSame($household->id, $owner->fresh()->household->id);
    }

    public function test_sole_owner_leaving_dissolves_the_household(): void
    {
        $owner = User::factory()->create();
        $oldHouseholdId = $owner->household->id;

        Livewire::actingAs($owner)
            ->test(ManageHousehold::class)
            ->call('leaveHousehold')
            ->assertRedirect(route('settings'));

        $this->assertNotSame($oldHouseholdId, $owner->fresh()->household->id);
        $this->assertDatabaseMissing('households', ['id' => $oldHouseholdId]);
    }
}
