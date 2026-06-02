@props([
    'label' => '',
    'value' => '',
    'trend' => null,
    'trendUp' => true,
    'color' => 'indigo',
])

@php
    $accentMap = [
        'indigo' => 'text-[#1a1aff]',
        'emerald' => 'text-[#1d9e75]',
        'rose' => 'text-[#e24b4a]',
        'amber' => 'text-[#f5a623]',
    ];
    $accent = $accentMap[$color] ?? $accentMap['indigo'];
@endphp

<div {{ $attributes->merge(['class' => "bg-white rounded-2xl p-4 shadow-sm border border-[#dde3f5]"]) }}>
    <p class="text-xs font-medium text-[#8890b0] mb-1">{{ $label }}</p>
    <p class="text-xl font-black {{ $accent }} tabular-nums">{{ $value }}</p>
    @if($trend)
        <div class="flex items-center gap-1 mt-1.5">
            <span class="text-xs font-medium {{ $trendUp ? 'text-[#1d9e75]' : 'text-[#e24b4a]' }}">
                {{ $trendUp ? '↑' : '↓' }} {{ $trend }}
            </span>
            <span class="text-xs text-[#8890b0]">vs bulan lalu</span>
        </div>
    @endif
    {{ $slot }}
</div>
