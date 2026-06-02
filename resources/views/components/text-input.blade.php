@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20', 'style' => 'background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;']) }}>
