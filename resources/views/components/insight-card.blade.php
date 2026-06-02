@props([
    'title' => '',
    'description' => '',
    'icon' => '💡',
    'type' => 'info',
])

@php
    $typeMap = [
        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
        'success' => 'bg-[#1d9e75]/10 border-emerald-200 text-emerald-800',
        'danger' => 'bg-[#e24b4a]/10 border-rose-200 text-rose-800',
    ];
    $typeClass = $typeMap[$type] ?? $typeMap['info'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl border p-4 $typeClass"]) }}>
    <div class="flex items-start gap-3">
        <span class="text-2xl leading-none">{{ $icon }}</span>
        <div>
            <p class="text-sm font-semibold">{{ $title }}</p>
            <p class="text-xs mt-0.5 opacity-80">{{ $description }}</p>
        </div>
    </div>
</div>
