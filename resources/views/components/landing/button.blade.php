@props([
    'href' => null,
    'variant' => 'primary',
])

@php
    $base = 'keysaku-focus inline-flex items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-extrabold transition duration-200 active:scale-[0.98]';
    $variants = [
        'primary' => 'bg-[#201a2e] text-white shadow-[0_16px_34px_rgba(32,26,46,0.24)] hover:-translate-y-0.5 hover:bg-[#2d2540]',
        'secondary' => 'border-2 border-[#201a2e] bg-white/80 text-[#201a2e] hover:-translate-y-0.5 hover:bg-white',
        'light' => 'bg-white text-[#201a2e] shadow-[0_16px_34px_rgba(32,26,46,0.18)] hover:-translate-y-0.5',
    ];
    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
