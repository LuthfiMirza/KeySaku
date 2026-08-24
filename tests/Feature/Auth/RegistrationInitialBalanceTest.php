<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationInitialBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_without_initial_balance_defaults_to_zero(): void
    {
        $this->post('/register', [
            'name' => 'Tanpa Saldo',
            'email' => 'tanpa-saldo@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'tanpa-saldo@example.com')->firstOrFail();

        $this->assertSame('0.00', $user->initial_balance);
    }

    public function test_registration_accepts_thousand_separated_initial_balance(): void
    {
        $this->post('/register', [
            'name' => 'Punya Saldo',
            'email' => 'punya-saldo@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'initial_balance' => '12.000.000',
        ]);

        $user = User::where('email', 'punya-saldo@example.com')->firstOrFail();

        $this->assertSame('12000000.00', $user->initial_balance);
    }
}
