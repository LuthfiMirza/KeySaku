<?php

namespace App\Actions;

use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\User;

class ProvisionHouseholdForUser
{
    /**
     * Join an existing household by invite code, or create a new one.
     *
     * $initialBalance only applies when a new household is created —
     * joining an existing household never touches its balance.
     */
    public function handle(User $user, ?string $inviteCode, float $initialBalance = 0): Household
    {
        if ($inviteCode !== null) {
            $household = Household::where('invite_code', $inviteCode)->firstOrFail();

            HouseholdMember::create([
                'household_id' => $household->id,
                'user_id' => $user->id,
                'role' => HouseholdMember::ROLE_MEMBER,
            ]);

            return $household;
        }

        return $this->createNewHousehold($user, $initialBalance);
    }

    private function createNewHousehold(User $user, float $initialBalance): Household
    {
        $household = Household::create([
            'invite_code' => Household::generateInviteCode(),
            'initial_balance' => $initialBalance,
        ]);

        HouseholdMember::create([
            'household_id' => $household->id,
            'user_id' => $user->id,
            'role' => HouseholdMember::ROLE_OWNER,
        ]);

        $this->seedDefaultCategories($household, $user);

        return $household;
    }

    private function seedDefaultCategories(Household $household, User $user): void
    {
        $defaults = config('default-categories', []);

        if ($defaults === []) {
            return;
        }

        $now = now();

        $rows = array_map(fn (array $entry) => [
            'household_id' => $household->id,
            'user_id' => $user->id,
            'name' => $entry['name'],
            'type' => $entry['type'],
            'color' => $entry['color'] ?? null,
            'icon' => $entry['icon'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $defaults);

        $household->categories()->insert($rows);
    }
}
