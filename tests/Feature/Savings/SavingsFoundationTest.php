<?php

namespace Tests\Feature\Savings;

use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SavingsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_savings_tables_have_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('savings_goals', [
            'id',
            'user_id',
            'name',
            'target_amount',
            'current_amount',
            'deadline',
            'status',
            'created_at',
            'updated_at',
        ]));

        $this->assertTrue(Schema::hasColumns('savings_contributions', [
            'id',
            'savings_goal_id',
            'amount',
            'contribution_date',
            'note',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_savings_relationships_are_available(): void
    {
        $user = User::factory()->create();

        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Dana Darurat',
            'target_amount' => 10000000,
            'current_amount' => 2500000,
            'deadline' => '2026-12-31',
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        $contribution = SavingsContribution::create([
            'savings_goal_id' => $goal->id,
            'amount' => 500000,
            'contribution_date' => '2026-05-06',
            'note' => 'Setoran awal',
        ]);

        $this->assertTrue($user->savingsGoals->contains($goal));
        $this->assertTrue($goal->user->is($user));
        $this->assertTrue($goal->contributions->contains($contribution));
        $this->assertTrue($contribution->savingsGoal->is($goal));
        $this->assertSame('10000000.00', $goal->target_amount);
        $this->assertSame('2500000.00', $goal->current_amount);
        $this->assertSame('2026-12-31', $goal->deadline->toDateString());
        $this->assertSame('500000.00', $contribution->amount);
        $this->assertSame('2026-05-06', $contribution->contribution_date->toDateString());
    }

    public function test_savings_page_is_authenticated_and_shows_skeleton(): void
    {
        $this->get(route('savings.index'))
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('savings.index'))
            ->assertOk()
            ->assertSee('Target Tabungan')
            ->assertSee('Rencanakan dana untuk tujuan penting.')
            ->assertSee('Belum ada target tabungan.')
            ->assertSee('Buat target untuk memantau progres dana darurat, liburan, atau kebutuhan keluarga.')
            ->assertSee('Buat Target');
    }

    public function test_savings_route_uses_tabungan_path(): void
    {
        $this->assertSame(url('/tabungan'), route('savings.index'));
    }
}
