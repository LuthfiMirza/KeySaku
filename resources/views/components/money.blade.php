@props([
    'value' => null,
    'amount' => null,
    'symbol' => true,
    'signed' => false,
    'prefix' => '',
])

@php
    // Accept both :value and :amount props; :amount takes precedence if both are given.
    $resolvedAmount = (float) ($amount ?? $value ?? 0);
    $formatted = $symbol
        ? \App\Support\Money::format($resolvedAmount)
        : \App\Support\Money::number($resolvedAmount);
@endphp

<span {{ $attributes->merge(['class' => 'tabular-nums']) }}>{{ $prefix }}{{ $signed && $resolvedAmount > 0 ? '+ ' : '' }}{{ $formatted }}</span>
