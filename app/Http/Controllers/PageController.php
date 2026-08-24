<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function notificationSettings(): View
    {
        return view('pages.settings.notifications');
    }

    public function currencySettings(): View
    {
        return view('pages.settings.currency');
    }

    public function updateCurrency(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'preferred_currency' => ['required', 'in:IDR,USD'],
        ]);

        $request->user()->update([
            'preferred_currency' => $validated['preferred_currency'],
        ]);

        return back()->with('settings_status', 'Mata uang berhasil diperbarui.');
    }

    public function languageSettings(): View
    {
        return view('pages.settings.language');
    }

    public function updateLanguage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'language' => ['required', 'in:id,en'],
        ]);

        $request->user()->update([
            'language' => $validated['language'],
        ]);

        return back()->with('settings_status', 'Bahasa berhasil diperbarui.');
    }

    public function securitySettings(): View
    {
        return view('pages.settings.security');
    }

    public function installSettings(): View
    {
        return view('pages.settings.install');
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
        abort_unless(auth()->user()->can('view', $transaction), 404);

        return view('pages.transactions.edit', [
            'transaction' => $transaction,
        ]);
    }
}
