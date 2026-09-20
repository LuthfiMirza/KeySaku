@props([
    'value' => null,
    'amount' => null,
    'symbol' => true,
    'signed' => false,
    'prefix' => '',
])

@php
    // Fallback logic to support both :value and :amount
    $moneyValue = $amount !== null ? $amount : ($value ?? 0);
    $parsedAmount = (float) $moneyValue;
    $formatted = $symbol
        ? \App\Support\Money::format($parsedAmount)
        : \App\Support\Money::number($parsedAmount);
@endphp

<span {{ $attributes->merge(['class' => 'tabular-nums']) }}>{{ $prefix }}{{ $signed && $parsedAmount > 0 ? '+ ' : '' }}{{ $formatted }}</span>
