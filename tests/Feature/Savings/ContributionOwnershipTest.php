<?php

namespace Tests\Feature\Savings;

use App\Livewire\Savings\ManageSavingsGoals;
use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContributionOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_added_contribution_is_stamped_with_the_goal_owners_user_id(): void
    {
        $user = User::factory()->create();
        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'household_id' => $user->household->id,
            'name' => 'Dana Darurat',
            'target_amount' => 1000000,
            'current_amount' => 0,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $goal->id)
            ->set('contributionAmount', '50000')
            ->set('contributionDate', now()->toDateString())
            ->call('addContribution')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('savings_contributions', [
            'savings_goal_id' => $goal->id,
            'user_id' => $user->id,
            'amount' => 50000,
        ]);
    }
}
