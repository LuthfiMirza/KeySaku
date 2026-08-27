<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoneyComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_idr_by_default(): void
    {
        $rendered = $this->blade('<x-money :value="1500000" />');

        $this->assertStringContainsString('Rp 1.500.000', $rendered);
    }

    public function test_it_follows_the_authenticated_users_preferred_currency(): void
    {
        $user = User::factory()->create(['preferred_currency' => 'USD']);
        $this->actingAs($user);

        $rendered = $this->blade('<x-money :value="1500000" />');

        $this->assertStringContainsString('$', $rendered);
        $this->assertStringNotContainsString('Rp', $rendered);
    }

    public function test_it_can_omit_the_symbol(): void
    {
        $rendered = $this->blade('<x-money :value="1500000" :symbol="false" />');

        $this->assertStringContainsString('1.500.000', $rendered);
        $this->assertStringNotContainsString('Rp', $rendered);
    }

    public function test_it_prefixes_positive_values_when_signed(): void
    {
        $rendered = $this->blade('<x-money :value="50000" signed />');

        $this->assertStringContainsString('+ Rp 50.000', $rendered);
    }

    public function test_negative_values_keep_their_minus_sign(): void
    {
        $rendered = $this->blade('<x-money :value="-25000" />');

        $this->assertStringContainsString('-Rp 25.000', $rendered);
    }
}
