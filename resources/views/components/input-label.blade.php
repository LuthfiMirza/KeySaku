@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#64748B]']) }}>
    {{ $value ?? $slot }}
</label>
