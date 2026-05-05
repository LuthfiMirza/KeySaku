@props([
    'label' => '',
    'icon' => '',
    'color' => 'indigo',
    'href' => '#',
])

@php
    $colorMap = [
        'indigo' => 'bg-indigo-100 text-indigo-600',
        'emerald' => 'bg-emerald-100 text-emerald-600',
        'rose' => 'bg-rose-100 text-rose-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'purple' => 'bg-purple-100 text-purple-600',
    ];
    $colorClass = $colorMap[$color] ?? $colorMap['indigo'];
@endphp

<a href="{{ $href }}" class="flex flex-col items-center gap-2 group">
    <div class="w-14 h-14 rounded-2xl {{ $colorClass }} flex items-center justify-center text-2xl shadow-sm transition-transform group-active:scale-95">
        {{ $icon }}
    </div>
    <span class="text-xs font-medium text-slate-600 text-center leading-tight">{{ $label }}</span>
</a>
