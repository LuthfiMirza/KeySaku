<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

class BudgetPolicy
{
    public function view(User $user, Budget $budget): bool
    {
        return $user->household?->id === $budget->household_id;
    }

    public function update(User $user, Budget $budget): bool
    {
        return $user->household?->id === $budget->household_id;
    }

    public function delete(User $user, Budget $budget): bool
    {
        return $user->household?->id === $budget->household_id;
    }
}
