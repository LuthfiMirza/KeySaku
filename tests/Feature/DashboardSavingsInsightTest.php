<?php

namespace Tests\Feature;

use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSavingsInsightTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_active_savings_goal_insight(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Dana Darurat',
            'target_amount' => 1000000,
            'current_amount' => 250000,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Liburan Keluarga',
            'target_amount' => 2000000,
            'current_amount' => 1000000,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Target Lama',
            'target_amount' => 5000000,
            'current_amount' => 5000000,
            'status' => SavingsGoal::STATUS_ARCHIVED,
        ]);

        SavingsGoal::create([
            'user_id' => $otherUser->id,
            'name' => 'Target User Lain',
            'target_amount' => 9000000,
            'current_amount' => 9000000,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Target Tabungan')
            ->assertSee('Lihat tabungan')
            ->assertSee('Terkumpul')
            ->assertSee('Total Target')
            ->assertSee('Progress')
            ->assertSee('Rp 1.250.000')
            ->assertSee('Rp 3.000.000')
            ->assertSee('42%')
            ->assertSee('Dana Darurat')
            ->assertSee('Liburan Keluarga')
            ->assertDontSee('Target Lama')
            ->assertDontSee('Target User Lain')
            ->assertDontSee('Belum ada target tabungan aktif.');
    }

    public function test_dashboard_shows_savings_empty_state_without_active_goals(): void
    {
        $user = User::factory()->create();

        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Target Diarsipkan',
            'target_amount' => 1000000,
            'current_amount' => 500000,
            'status' => SavingsGoal::STATUS_ARCHIVED,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada target tabungan aktif.')
            ->assertSee('Buat target untuk mulai memantau progres tabunganmu.')
            ->assertSee('Lihat tabungan')
            ->assertDontSee('Target Diarsipkan');
    }

    public function test_dashboard_caps_savings_progress_bar_for_overfunded_goals(): void
    {
        $user = User::factory()->create();

        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Laptop Baru',
            'target_amount' => 1000000,
            'current_amount' => 1500000,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('150%')
            ->assertSee('width: 100%', false);
    }
}
