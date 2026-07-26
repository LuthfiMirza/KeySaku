<?php

use App\Support\Money;

if (! function_exists('money')) {
    function money(int|float|string|null $amount, ?string $currency = null): string
    {
        return Money::format($amount, $currency);
    }
}
