@props([
    'label' => '',
    'value' => '',
    'trend' => null,
    'trendUp' => true,
    'color' => 'indigo',
])

@php
    $colorMap = [
        'indigo' => 'from-indigo-500 to-indigo-600',
        'emerald' => 'from-emerald-400 to-emerald-600',
        'rose' => 'from-rose-400 to-rose-600',
        'amber' => 'from-amber-400 to-amber-600',
    ];
    $gradient = $colorMap[$color] ?? $colorMap['indigo'];
@endphp

<div {{ $attributes->merge(['class' => "bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60"]) }}>
    <p class="text-xs font-medium text-slate-400 mb-1">{{ $label }}</p>
    <p class="text-xl font-bold text-slate-800 tabular-nums">{{ $value }}</p>
    @if($trend)
        <div class="flex items-center gap-1 mt-1.5">
            <span class="text-xs font-medium {{ $trendUp ? 'text-emerald-500' : 'text-rose-500' }}">
                {{ $trendUp ? '↑' : '↓' }} {{ $trend }}
            </span>
            <span class="text-xs text-slate-400">vs bulan lalu</span>
        </div>
    @endif
    {{ $slot }}
</div>
