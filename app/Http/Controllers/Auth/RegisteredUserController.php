<?php

namespace App\Http\Controllers\Auth;

use App\Actions\ProvisionHouseholdForUser;
use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'initial_balance' => ['nullable', 'string', 'max:20'],
            'invite_code' => ['nullable', 'string', 'max:12'],
        ]);

        $inviteCode = $request->filled('invite_code')
            ? strtoupper(trim((string) $request->input('invite_code')))
            : null;

        if ($inviteCode !== null && ! Household::where('invite_code', $inviteCode)->exists()) {
            throw ValidationException::withMessages([
                'invite_code' => 'Kode undangan tidak ditemukan.',
            ]);
        }

        $normalizedBalance = preg_replace('/\D+/', '', (string) $request->input('initial_balance', ''));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        (new ProvisionHouseholdForUser())->handle(
            $user,
            $inviteCode,
            $normalizedBalance !== '' ? (float) $normalizedBalance : 0,
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
