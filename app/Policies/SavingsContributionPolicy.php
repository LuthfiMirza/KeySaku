<?php

namespace App\Policies;

use App\Models\SavingsContribution;
use App\Models\User;

class SavingsContributionPolicy
{
    public function view(User $user, SavingsContribution $savingsContribution): bool
    {
        return $user->id === $savingsContribution->user_id;
    }

    public function update(User $user, SavingsContribution $savingsContribution): bool
    {
        return $user->id === $savingsContribution->user_id;
    }

    public function delete(User $user, SavingsContribution $savingsContribution): bool
    {
        return $user->id === $savingsContribution->user_id;
    }
}
