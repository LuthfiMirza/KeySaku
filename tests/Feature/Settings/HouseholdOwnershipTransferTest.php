<?php

namespace Tests\Feature\Settings;

use App\Livewire\Settings\ManageHousehold;
use App\Models\HouseholdMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HouseholdOwnershipTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_transfer_ownership_to_a_member(): void
    {
        [$owner, $member, $memberRow] = $this->householdWithMember();

        Livewire::actingAs($owner)
            ->test(ManageHousehold::class)
            ->call('transferOwnership', $memberRow->id)
            ->assertHasNoErrors();

        $this->assertSame(HouseholdMember::ROLE_OWNER, $memberRow->fresh()->role);
        $this->assertSame(HouseholdMember::ROLE_MEMBER, $owner->householdMembership->fresh()->role);
    }

    public function test_non_owner_cannot_transfer_ownership(): void
    {
        [$owner, $member, $memberRow] = $this->householdWithMember();

        Livewire::actingAs($member)
            ->test(ManageHousehold::class)
            ->call('transferOwnership', $owner->householdMembership->id)
            ->assertStatus(403);

        $this->assertSame(HouseholdMember::ROLE_OWNER, $owner->householdMembership->fresh()->role);
    }

    public function test_owner_cannot_transfer_ownership_to_themselves(): void
    {
        [$owner, $member, $memberRow] = $this->householdWithMember();

        Livewire::actingAs($owner)
            ->test(ManageHousehold::class)
            ->call('transferOwnership', $owner->householdMembership->id)
            ->assertHasErrors('member');

        $this->assertSame(HouseholdMember::ROLE_OWNER, $owner->householdMembership->fresh()->role);
    }

    public function test_former_owner_can_now_leave_even_though_other_members_remain(): void
    {
        [$owner, $member, $memberRow] = $this->householdWithMember();
        $household = $owner->household;

        Livewire::actingAs($owner)
            ->test(ManageHousehold::class)
            ->call('transferOwnership', $memberRow->id);

        Livewire::actingAs($owner->fresh())
            ->test(ManageHousehold::class)
            ->call('leaveHousehold')
            ->assertRedirect(route('settings'));

        $this->assertNotSame($household->id, $owner->fresh()->household->id);
        $this->assertSame(1, $household->fresh()->members()->count());
        $this->assertSame(HouseholdMember::ROLE_OWNER, $memberRow->fresh()->role);
    }

    public function test_new_owner_can_manage_the_former_owner_as_a_regular_member(): void
    {
        [$owner, $member, $memberRow] = $this->householdWithMember();
        $household = $owner->household;

        Livewire::actingAs($owner)
            ->test(ManageHousehold::class)
            ->call('transferOwnership', $memberRow->id);

        Livewire::actingAs($member->fresh())
            ->test(ManageHousehold::class)
            ->call('removeMember', $owner->householdMembership->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('household_members', ['id' => $owner->householdMembership->id]);
        $this->assertNotSame($household->id, $owner->fresh()->household->id);
    }

    /**
     * @return array{0: User, 1: User, 2: HouseholdMember}
     */
    private function householdWithMember(): array
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $household = $owner->household;

        $member->householdMembership->delete();
        $memberRow = HouseholdMember::create([
            'household_id' => $household->id,
            'user_id' => $member->id,
            'role' => HouseholdMember::ROLE_MEMBER,
        ]);

        return [$owner, $member->fresh(), $memberRow];
    }
}
