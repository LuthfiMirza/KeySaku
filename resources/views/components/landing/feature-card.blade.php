@props([
    'icon' => null,
    'title',
    'description',
    'tone' => 'lime',
])

@php
    $tones = [
        'lime' => 'from-lime-100 to-white',
        'purple' => 'from-purple-100 to-white',
        'teal' => 'from-teal-100 to-white',
        'pink' => 'from-pink-100 to-white',
    ];
@endphp

<article {{ $attributes->merge(['class' => 'keysaku-card bg-gradient-to-br '.($tones[$tone] ?? $tones['lime']).' p-5']) }}>
    @if($icon)
        <img src="{{ $icon }}" alt="" aria-hidden="true" class="mb-4 h-14 w-14 rounded-2xl">
    @endif
    <h3 class="text-lg font-black tracking-[-0.03em] text-[#201a2e]">{{ $title }}</h3>
    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $description }}</p>
</article>
