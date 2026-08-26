<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->household?->id === $transaction->household_id;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->household?->id === $transaction->household_id;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->household?->id === $transaction->household_id;
    }
}
