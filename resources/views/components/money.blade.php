@props([
    'value' => 0,
    'symbol' => true,
    'signed' => false,
    'prefix' => '',
])

@php
    $amount = (float) $value;
    $formatted = $symbol
        ? \App\Support\Money::format($amount)
        : \App\Support\Money::number($amount);
@endphp

<span {{ $attributes->merge(['class' => 'tabular-nums']) }}>{{ $prefix }}{{ $signed && $amount > 0 ? '+ ' : '' }}{{ $formatted }}</span>
