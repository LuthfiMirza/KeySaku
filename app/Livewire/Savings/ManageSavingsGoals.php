<?php

namespace App\Livewire\Savings;

use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManageSavingsGoals extends Component
{
    public string $name = '';

    public string $target_amount = '';

    public string $deadline = '';

    public ?int $contributingGoalId = null;

    public string $contributionAmount = '';

    public string $contributionDate = '';

    public string $contributionNote = '';

    public ?int $editingGoalId = null;

    public string $editName = '';

    public string $editTargetAmount = '';

    public string $editDeadline = '';

    public function startEditGoal(int $goalId): void
    {
        $goal = auth()->user()
            ->savingsGoals()
            ->whereKey($goalId)
            ->first();

        abort_if(! $goal, 404);

        $this->cancelContribution();
        $this->editingGoalId = $goal->id;
        $this->editName = $goal->name;
        $this->editTargetAmount = (string) (float) $goal->target_amount;
        $this->editDeadline = $goal->deadline?->toDateString() ?? '';
        $this->resetValidation(['editName', 'editTargetAmount', 'editDeadline']);
    }

    public function cancelEditGoal(): void
    {
        $this->editingGoalId = null;
        $this->editName = '';
        $this->editTargetAmount = '';
        $this->editDeadline = '';
        $this->resetValidation(['editName', 'editTargetAmount', 'editDeadline']);
    }

    public function updateGoal(): void
    {
        $validated = $this->validate([
            'editingGoalId' => ['required', 'integer'],
            'editName' => ['required', 'string', 'max:100'],
            'editTargetAmount' => ['required', 'numeric', 'gt:0'],
            'editDeadline' => ['nullable', 'date', 'after_or_equal:today'],
        ], [
            'editName.required' => 'Nama target wajib diisi.',
            'editName.max' => 'Nama target maksimal 100 karakter.',
            'editTargetAmount.required' => 'Nominal target wajib diisi.',
            'editTargetAmount.numeric' => 'Nominal target harus berupa angka.',
            'editTargetAmount.gt' => 'Nominal target harus lebih dari 0.',
            'editDeadline.date' => 'Deadline harus berupa tanggal yang valid.',
            'editDeadline.after_or_equal' => 'Deadline tidak boleh sebelum hari ini.',
        ]);

        $goal = auth()->user()
            ->savingsGoals()
            ->whereKey($validated['editingGoalId'])
            ->first();

        abort_if(! $goal, 404);

        $goal->update([
            'name' => $validated['editName'],
            'target_amount' => $validated['editTargetAmount'],
            'deadline' => $validated['editDeadline'] ?: null,
        ]);

        $this->cancelEditGoal();

        session()->flash('savings_status', 'Target tabungan berhasil diperbarui.');
    }

    public function archiveGoal(int $goalId): void
    {
        $goal = auth()->user()
            ->savingsGoals()
            ->whereKey($goalId)
            ->first();

        abort_if(! $goal, 404);

        $goal->update([
            'status' => SavingsGoal::STATUS_ARCHIVED,
        ]);

        if ($this->editingGoalId === $goalId) {
            $this->cancelEditGoal();
        }

        if ($this->contributingGoalId === $goalId) {
            $this->cancelContribution();
        }

        session()->flash('savings_status', 'Target tabungan berhasil diarsipkan.');
    }

    public function startContribution(int $goalId): void
    {
        $goal = auth()->user()
            ->savingsGoals()
            ->whereKey($goalId)
            ->first();

        abort_if(! $goal, 404);

        $this->cancelEditGoal();
        $this->contributingGoalId = $goal->id;
        $this->contributionAmount = '';
        $this->contributionDate = now()->toDateString();
        $this->contributionNote = '';
        $this->resetValidation(['contributionAmount', 'contributionDate', 'contributionNote']);
    }

    public function cancelContribution(): void
    {
        $this->contributingGoalId = null;
        $this->contributionAmount = '';
        $this->contributionDate = '';
        $this->contributionNote = '';
        $this->resetValidation(['contributionAmount', 'contributionDate', 'contributionNote']);
    }

    public function addContribution(): void
    {
        $validated = $this->validate([
            'contributingGoalId' => ['required', 'integer'],
            'contributionAmount' => ['required', 'numeric', 'gt:0'],
            'contributionDate' => ['required', 'date'],
            'contributionNote' => ['nullable', 'string', 'max:1000'],
        ], [
            'contributionAmount.required' => 'Nominal dana wajib diisi.',
            'contributionAmount.numeric' => 'Nominal dana harus berupa angka.',
            'contributionAmount.gt' => 'Nominal dana harus lebih dari 0.',
            'contributionDate.required' => 'Tanggal wajib diisi.',
            'contributionDate.date' => 'Tanggal harus berupa tanggal yang valid.',
            'contributionNote.max' => 'Catatan maksimal 1000 karakter.',
        ]);

        $goal = auth()->user()
            ->savingsGoals()
            ->whereKey($validated['contributingGoalId'])
            ->first();

        abort_if(! $goal, 404);

        DB::transaction(function () use ($goal, $validated) {
            SavingsContribution::create([
                'savings_goal_id' => $goal->id,
                'amount' => $validated['contributionAmount'],
                'contribution_date' => $validated['contributionDate'],
                'note' => $validated['contributionNote'] ?: null,
            ]);

            $goal->update([
                'current_amount' => (float) $goal->current_amount + (float) $validated['contributionAmount'],
            ]);
        });

        $this->cancelContribution();

        session()->flash('savings_status', 'Dana berhasil ditambahkan.');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'target_amount' => ['required', 'numeric', 'gt:0'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ], [
            'name.required' => 'Nama target wajib diisi.',
            'name.max' => 'Nama target maksimal 100 karakter.',
            'target_amount.required' => 'Nominal target wajib diisi.',
            'target_amount.numeric' => 'Nominal target harus berupa angka.',
            'target_amount.gt' => 'Nominal target harus lebih dari 0.',
            'deadline.date' => 'Deadline harus berupa tanggal yang valid.',
            'deadline.after_or_equal' => 'Deadline tidak boleh sebelum hari ini.',
        ]);

        SavingsGoal::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => 0,
            'deadline' => $validated['deadline'] ?: null,
            'status' => SavingsGoal::STATUS_ACTIVE,
        ]);

        $this->reset(['name', 'target_amount', 'deadline']);
        $this->resetValidation();

        session()->flash('savings_status', 'Target tabungan berhasil dibuat.');
    }

    public function render(): View
    {
        return view('livewire.savings.manage-savings-goals', [
            'goals' => auth()->user()
                ->savingsGoals()
                ->where('status', SavingsGoal::STATUS_ACTIVE)
                ->latest('id')
                ->get(),
        ]);
    }
}
