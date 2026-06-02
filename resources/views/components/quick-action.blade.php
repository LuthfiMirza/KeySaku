@props([
    'label' => '',
    'icon' => '',
    'color' => 'indigo',
    'href' => '#',
])

@php
    $colorMap = [
        'indigo' => 'bg-[#1a1aff]/10 text-[#1a1aff]',
        'emerald' => 'bg-[#1d9e75]/10 text-[#1d9e75]',
        'rose' => 'bg-[#e24b4a]/10 text-[#e24b4a]',
        'amber' => 'bg-[#f5a623]/10 text-[#7a4f00]',
        'purple' => 'bg-[#4d4dff]/10 text-[#4d4dff]',
    ];
    $colorClass = $colorMap[$color] ?? $colorMap['indigo'];
@endphp

<a href="{{ $href }}" class="flex flex-col items-center gap-2 group">
    <div class="w-14 h-14 rounded-2xl {{ $colorClass }} flex items-center justify-center text-2xl shadow-sm transition-transform group-active:scale-95">
        {{ $icon }}
    </div>
    <span class="text-xs font-medium text-[#0f0f5e] text-center leading-tight">{{ $label }}</span>
</a>
