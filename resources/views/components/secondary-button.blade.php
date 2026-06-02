<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-[#A3E635]/20 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150', 'style' => 'background:#1A2B52 !important;border-color:rgba(255,255,255,0.1) !important;color:#FFFFFF !important;']) }}>
    {{ $slot }}
</button>
