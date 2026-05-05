<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\View\View;

class PageController extends Controller
{
    public function budgets(): View
    {
        return view('pages.budgets');
    }

    public function goals(): View
    {
        return view('pages.goals');
    }

    public function settings(): View
    {
        return view('pages.settings');
    }

    public function categories(): View
    {
        return view('pages.categories');
    }

    public function transactions(): View
    {
        return view('pages.transactions.index');
    }

    public function createTransaction(): View
    {
        return view('pages.transactions.create');
    }

    public function voiceTransaction(): View
    {
        return view('pages.transactions.voice');
    }

    public function editTransaction(Transaction $transaction): View
    {
        abort_unless($transaction->user_id === auth()->id(), 404);

        return view('pages.transactions.edit', [
            'transaction' => $transaction,
        ]);
    }
}
