<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('idrParityValues')]
    public function test_idr_output_is_byte_identical_to_legacy_format($value): void
    {
        $legacy = 'Rp '.number_format((float) $value, 0, ',', '.');
        $this->assertSame($legacy, Money::format($value, 'IDR'));
    }

    public static function idrParityValues(): array
    {
        return [
            [0],
            [1000],
            [25000],
            [5000000],
            [1234567.89],
        ];
    }

    public function test_usd_uses_two_decimals_and_correct_separators(): void
    {
        $this->assertSame('$1,234.57', Money::format(1234.567, 'USD'));
        $this->assertSame('$0.00', Money::format(0, 'USD'));
        $this->assertSame('$5,000,000.00', Money::format(5000000, 'USD'));
    }

    public function test_negative_values_have_minus_before_symbol(): void
    {
        $this->assertSame('-Rp 25.000', Money::format(-25000, 'IDR'));
        $this->assertSame('-$25.00', Money::format(-25, 'USD'));
    }

    public function test_null_is_treated_as_zero(): void
    {
        $this->assertSame('Rp 0', Money::format(null, 'IDR'));
        $this->assertSame('$0.00', Money::format(null, 'USD'));
    }

    public function test_unknown_currency_falls_back_to_default_without_error(): void
    {
        $this->assertSame(Money::format(1000, 'IDR'), Money::format(1000, 'XYZ'));
    }

    public function test_guest_user_falls_back_to_default(): void
    {
        $this->assertGuest();
        $this->assertSame('Rp 5.000.000', Money::format(5000000));
    }

    public function test_authenticated_user_preferred_currency_is_used(): void
    {
        $user = User::factory()->create(['preferred_currency' => 'USD']);
        $this->actingAs($user);
        $this->assertSame('$1,000.00', Money::format(1000));
    }

    public function test_user_with_unknown_currency_falls_back_to_default(): void
    {
        $user = User::factory()->create(['preferred_currency' => 'XYZ']);
        $this->actingAs($user);
        $this->assertSame('Rp 1.000', Money::format(1000));
    }

    public function test_config_returns_resolved_array(): void
    {
        $cfg = Money::config('IDR');
        $this->assertSame('IDR', $cfg['code']);
        $this->assertSame('Rp', $cfg['symbol']);
        $this->assertSame('before', $cfg['position']);
        $this->assertSame(0, $cfg['decimals']);
        $this->assertTrue($cfg['space']);
    }

    public function test_symbol_helper(): void
    {
        $this->assertSame('Rp', Money::symbol('IDR'));
        $this->assertSame('$', Money::symbol('USD'));
    }

    public function test_money_global_helper_delegates_to_format(): void
    {
        $this->assertSame(Money::format(1000, 'IDR'), money(1000, 'IDR'));
    }
}
