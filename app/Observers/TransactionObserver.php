<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Adjust account balance when a new transaction is saved.
     * Income increases balance; expense decreases balance.
     */
    public function created(Transaction $transaction): void
    {
        if (! $transaction->account_id) {
            return;
        }

        $account = Account::find($transaction->account_id);
        if (! $account) {
            return;
        }

        if ($transaction->type === Transaction::TYPE_INCOME) {
            $account->increment('balance', $transaction->amount);
        } else {
            $account->decrement('balance', $transaction->amount);
        }
    }

    /**
     * Revert old amount and apply new amount when a transaction is updated.
     * This correctly handles changes in type, amount, or account.
     */
    public function updated(Transaction $transaction): void
    {
        $oldAccountId = $transaction->getOriginal('account_id');
        $newAccountId = $transaction->account_id;
        $oldType      = $transaction->getOriginal('type');
        $newType      = $transaction->type;
        $oldAmount    = (float) $transaction->getOriginal('amount');
        $newAmount    = (float) $transaction->amount;

        // Revert the old account effect
        if ($oldAccountId) {
            $oldAccount = Account::find($oldAccountId);
            if ($oldAccount) {
                if ($oldType === Transaction::TYPE_INCOME) {
                    $oldAccount->decrement('balance', $oldAmount);
                } else {
                    $oldAccount->increment('balance', $oldAmount);
                }
            }
        }

        // Apply the new account effect
        if ($newAccountId) {
            $newAccount = Account::find($newAccountId);
            if ($newAccount) {
                if ($newType === Transaction::TYPE_INCOME) {
                    $newAccount->increment('balance', $newAmount);
                } else {
                    $newAccount->decrement('balance', $newAmount);
                }
            }
        }
    }

    /**
     * Revert the balance effect when a transaction is soft-deleted.
     */
    public function deleted(Transaction $transaction): void
    {
        if (! $transaction->account_id) {
            return;
        }

        $account = Account::find($transaction->account_id);
        if (! $account) {
            return;
        }

        // Reverse the original effect
        if ($transaction->type === Transaction::TYPE_INCOME) {
            $account->decrement('balance', $transaction->amount);
        } else {
            $account->increment('balance', $transaction->amount);
        }
    }

    /**
     * Re-apply the balance effect when a soft-deleted transaction is restored.
     */
    public function restored(Transaction $transaction): void
    {
        $this->created($transaction);
    }
}
