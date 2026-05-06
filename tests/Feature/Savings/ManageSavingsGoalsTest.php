<?php

namespace Tests\Feature\Savings;

use App\Livewire\Savings\ManageSavingsGoals;
use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageSavingsGoalsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_savings_goal(): void
    {
        $user = User::factory()->create();
        $deadline = now()->addMonth()->toDateString();

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->set('name', 'Dana Darurat')
            ->set('target_amount', '10000000')
            ->set('deadline', $deadline)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('name', '')
            ->assertSet('target_amount', '')
            ->assertSet('deadline', '')
            ->assertSee('Target tabungan berhasil dibuat.')
            ->assertSee('Dana Darurat')
            ->assertSee('Rp 10.000.000')
            ->assertSee('Rp 0')
            ->assertSee('Aktif')
            ->assertSee('0% terkumpul');

        $this->assertDatabaseHas('savings_goals', [
            'user_id' => $user->id,
            'name' => 'Dana Darurat',
            'target_amount' => 10000000.00,
            'current_amount' => 0.00,
            'deadline' => $deadline.' 00:00:00',
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);
    }

    public function test_savings_goal_form_validates_required_numeric_and_future_deadline(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->set('name', '')
            ->set('target_amount', '0')
            ->set('deadline', now()->subDay()->toDateString())
            ->call('save')
            ->assertHasErrors(['name', 'target_amount', 'deadline']);

        $this->assertDatabaseCount('savings_goals', 0);
    }

    public function test_savings_goal_list_shows_only_current_users_goals(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Dana Darurat',
            'target_amount' => 10000000,
            'current_amount' => 0,
            'deadline' => now()->addMonth()->toDateString(),
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        SavingsGoal::create([
            'user_id' => $otherUser->id,
            'name' => 'Liburan User Lain',
            'target_amount' => 5000000,
            'current_amount' => 0,
            'deadline' => now()->addMonth()->toDateString(),
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->assertSee('Dana Darurat')
            ->assertDontSee('Liburan User Lain');
    }

    public function test_savings_page_shows_create_form_and_empty_state(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('savings.index'))
            ->assertOk()
            ->assertSee('Buat Target')
            ->assertSee('Nama Target')
            ->assertSee('Nominal Target')
            ->assertSee('Deadline')
            ->assertSee('Simpan Target')
            ->assertSee('Daftar Target')
            ->assertSee('Belum ada target tabungan.')
            ->assertSee('Buat target untuk memantau progres dana darurat, liburan, atau kebutuhan keluarga.');
    }

    public function test_user_can_start_contribution_for_own_goal(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $goal->id)
            ->assertSet('contributingGoalId', $goal->id)
            ->assertSet('contributionDate', now()->toDateString())
            ->assertSee('Tambah Dana')
            ->assertSee('Nominal')
            ->assertSee('Tanggal')
            ->assertSee('Catatan')
            ->assertSee('Simpan Dana')
            ->assertSee('Batal');
    }

    public function test_user_can_add_contribution_to_own_goal(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);
        $date = now()->toDateString();

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $goal->id)
            ->set('contributionAmount', '250000')
            ->set('contributionDate', $date)
            ->set('contributionNote', 'Setoran pertama')
            ->call('addContribution')
            ->assertHasNoErrors()
            ->assertSet('contributingGoalId', null)
            ->assertSet('contributionAmount', '')
            ->assertSet('contributionDate', '')
            ->assertSet('contributionNote', '')
            ->assertSee('Dana berhasil ditambahkan.')
            ->assertSee('Rp 250.000')
            ->assertSee('Rp 1.000.000')
            ->assertSee('Rp 750.000')
            ->assertSee('25% terkumpul');

        $this->assertDatabaseHas('savings_contributions', [
            'savings_goal_id' => $goal->id,
            'amount' => 250000.00,
            'contribution_date' => $date.' 00:00:00',
            'note' => 'Setoran pertama',
        ]);

        $this->assertSame('250000.00', $goal->fresh()->current_amount);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_contribution_validation_rejects_invalid_data(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $goal->id)
            ->set('contributionAmount', '0')
            ->set('contributionDate', '')
            ->set('contributionNote', str_repeat('a', 1001))
            ->call('addContribution')
            ->assertHasErrors(['contributionAmount', 'contributionDate', 'contributionNote']);

        $this->assertDatabaseCount('savings_contributions', 0);
        $this->assertSame('0.00', $goal->fresh()->current_amount);
    }

    public function test_user_cannot_add_contribution_to_another_users_goal(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherGoal = $this->goal($otherUser, 'Dana Darurat User Lain', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->set('contributingGoalId', $otherGoal->id)
            ->set('contributionAmount', '250000')
            ->set('contributionDate', now()->toDateString())
            ->call('addContribution')
            ->assertNotFound();

        $this->assertDatabaseCount('savings_contributions', 0);
        $this->assertSame('0.00', $otherGoal->fresh()->current_amount);
    }

    public function test_contribution_can_exceed_target_without_completing_status(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 100000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $goal->id)
            ->set('contributionAmount', '150000')
            ->set('contributionDate', now()->toDateString())
            ->call('addContribution')
            ->assertSee('Rp 150.000')
            ->assertSee('150% terkumpul')
            ->assertSee('Target tercapai')
            ->assertSee('Tercapai');

        $goal->refresh();

        $this->assertSame('150000.00', $goal->current_amount);
        $this->assertSame(SavingsGoal::STATUS_ACTIVE, $goal->status);
    }

    public function test_user_can_start_editing_own_savings_goal(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startEditGoal', $goal->id)
            ->assertSet('editingGoalId', $goal->id)
            ->assertSet('editName', 'Dana Darurat')
            ->assertSet('editTargetAmount', '1000000')
            ->assertSet('editDeadline', $goal->deadline->toDateString())
            ->assertSee('Edit Target')
            ->assertSee('Nama Target')
            ->assertSee('Nominal Target')
            ->assertSee('Deadline')
            ->assertSee('Simpan')
            ->assertSee('Batal');
    }

    public function test_user_can_update_own_savings_goal_basic_details(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);
        $contributionDate = now()->toDateString();
        $newDeadline = now()->addMonths(2)->toDateString();

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $goal->id)
            ->set('contributionAmount', '250000')
            ->set('contributionDate', $contributionDate)
            ->call('addContribution')
            ->call('startEditGoal', $goal->id)
            ->set('editName', 'Dana Darurat Keluarga')
            ->set('editTargetAmount', '500000')
            ->set('editDeadline', $newDeadline)
            ->call('updateGoal')
            ->assertHasNoErrors()
            ->assertSet('editingGoalId', null)
            ->assertSet('editName', '')
            ->assertSet('editTargetAmount', '')
            ->assertSet('editDeadline', '')
            ->assertSee('Target tabungan berhasil diperbarui.')
            ->assertSee('Dana Darurat Keluarga')
            ->assertSee('Rp 500.000')
            ->assertSee('Rp 250.000')
            ->assertSee('50% terkumpul');

        $goal->refresh();

        $this->assertSame('Dana Darurat Keluarga', $goal->name);
        $this->assertSame('500000.00', $goal->target_amount);
        $this->assertSame($newDeadline, $goal->deadline->toDateString());
        $this->assertSame('250000.00', $goal->current_amount);
        $this->assertSame(SavingsGoal::STATUS_ACTIVE, $goal->status);
        $this->assertDatabaseHas('savings_contributions', [
            'savings_goal_id' => $goal->id,
            'amount' => 250000.00,
        ]);
    }

    public function test_savings_goal_edit_validates_fields(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startEditGoal', $goal->id)
            ->set('editName', '')
            ->set('editTargetAmount', '0')
            ->set('editDeadline', now()->subDay()->toDateString())
            ->call('updateGoal')
            ->assertHasErrors(['editName', 'editTargetAmount', 'editDeadline']);

        $goal->refresh();

        $this->assertSame('Dana Darurat', $goal->name);
        $this->assertSame('1000000.00', $goal->target_amount);
    }

    public function test_user_cannot_update_another_users_savings_goal(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherGoal = $this->goal($otherUser, 'Dana Darurat User Lain', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->set('editingGoalId', $otherGoal->id)
            ->set('editName', 'Diubah')
            ->set('editTargetAmount', '500000')
            ->set('editDeadline', now()->addMonth()->toDateString())
            ->call('updateGoal')
            ->assertNotFound();

        $otherGoal->refresh();

        $this->assertSame('Dana Darurat User Lain', $otherGoal->name);
        $this->assertSame('1000000.00', $otherGoal->target_amount);
    }

    public function test_editing_target_below_current_amount_shows_target_reached_without_completing_status(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $goal->id)
            ->set('contributionAmount', '750000')
            ->set('contributionDate', now()->toDateString())
            ->call('addContribution')
            ->call('startEditGoal', $goal->id)
            ->set('editTargetAmount', '500000')
            ->call('updateGoal')
            ->assertSee('150% terkumpul')
            ->assertSee('Target tercapai')
            ->assertSee('Tercapai');

        $goal->refresh();

        $this->assertSame('750000.00', $goal->current_amount);
        $this->assertSame('500000.00', $goal->target_amount);
        $this->assertSame(SavingsGoal::STATUS_ACTIVE, $goal->status);
    }

    public function test_user_can_archive_own_savings_goal_without_deleting_contributions(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $goal->id)
            ->set('contributionAmount', '250000')
            ->set('contributionDate', now()->toDateString())
            ->set('contributionNote', 'Setoran awal')
            ->call('addContribution')
            ->call('archiveGoal', $goal->id)
            ->assertSee('Target tabungan berhasil diarsipkan.')
            ->assertSee('Belum ada target tabungan.');

        $goal->refresh();

        $this->assertSame(SavingsGoal::STATUS_ARCHIVED, $goal->status);
        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'status' => SavingsGoal::STATUS_ARCHIVED,
        ]);
        $this->assertDatabaseHas('savings_contributions', [
            'savings_goal_id' => $goal->id,
            'amount' => 250000.00,
            'note' => 'Setoran awal',
        ]);
    }

    public function test_archived_goals_are_hidden_from_active_list(): void
    {
        $user = User::factory()->create();
        $activeGoal = $this->goal($user, 'Dana Darurat', 1000000);
        $archivedGoal = $this->goal($user, 'Liburan Lama', 5000000);
        $archivedGoal->update(['status' => SavingsGoal::STATUS_ARCHIVED]);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->assertSee('Dana Darurat')
            ->assertDontSee('Liburan Lama');

        $this->assertSame(SavingsGoal::STATUS_ACTIVE, $activeGoal->fresh()->status);
        $this->assertSame(SavingsGoal::STATUS_ARCHIVED, $archivedGoal->fresh()->status);
    }

    public function test_archiving_goal_clears_edit_state(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startEditGoal', $goal->id)
            ->call('archiveGoal', $goal->id)
            ->assertSet('editingGoalId', null)
            ->assertSet('editName', '')
            ->assertSet('editTargetAmount', '')
            ->assertSet('editDeadline', '')
            ->assertSee('Target tabungan berhasil diarsipkan.');

        $this->assertSame(SavingsGoal::STATUS_ARCHIVED, $goal->fresh()->status);
    }

    public function test_archiving_goal_clears_contribution_state(): void
    {
        $user = User::factory()->create();
        $goal = $this->goal($user, 'Dana Darurat', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('startContribution', $goal->id)
            ->set('contributionAmount', '250000')
            ->call('archiveGoal', $goal->id)
            ->assertSet('contributingGoalId', null)
            ->assertSet('contributionAmount', '')
            ->assertSet('contributionDate', '')
            ->assertSet('contributionNote', '')
            ->assertSee('Target tabungan berhasil diarsipkan.');

        $this->assertSame(SavingsGoal::STATUS_ARCHIVED, $goal->fresh()->status);
    }

    public function test_user_cannot_archive_another_users_savings_goal(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherGoal = $this->goal($otherUser, 'Dana Darurat User Lain', 1000000);

        Livewire::actingAs($user)
            ->test(ManageSavingsGoals::class)
            ->call('archiveGoal', $otherGoal->id)
            ->assertNotFound();

        $this->assertSame(SavingsGoal::STATUS_ACTIVE, $otherGoal->fresh()->status);
    }

    private function goal(User $user, string $name, int $targetAmount): SavingsGoal
    {
        return SavingsGoal::create([
            'user_id' => $user->id,
            'name' => $name,
            'target_amount' => $targetAmount,
            'current_amount' => 0,
            'deadline' => now()->addMonth()->toDateString(),
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);
    }
}
