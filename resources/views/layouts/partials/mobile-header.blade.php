<header class="sticky top-0 z-30 px-5 pt-4 pb-2 bg-[#FCF8F8]/90 backdrop-blur-md">
    <div class="flex items-center justify-between">
        {{-- Left Hamburger Menu --}}
        <a href="{{ route('settings') }}"
           class="w-11 h-11 rounded-full bg-white flex items-center justify-center text-[#121316] shadow-[0_4px_16px_rgba(18,19,22,0.04)] border border-[#F0EBEB] transition active:scale-95 hover:border-slate-300"
           aria-label="Menu Setelan">
            <svg class="w-5 h-5 text-[#121316]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </a>

        {{-- Center Account Pill with icon •••• 6281 --}}
        <a href="{{ route('accounts') }}"
           class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-[#F0EBEB] shadow-[0_4px_16px_rgba(18,19,22,0.03)] hover:border-slate-300 transition active:scale-98">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-semibold text-[#121316] tracking-tight">•••• 6281</span>
            <svg class="w-3.5 h-3.5 text-[#8E8E93]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </a>

        {{-- Right Bell Notification --}}
        <a href="{{ route('settings.notifications') }}"
           class="relative w-11 h-11 rounded-full bg-white flex items-center justify-center text-[#121316] shadow-[0_4px_16px_rgba(18,19,22,0.04)] border border-[#F0EBEB] transition active:scale-95 hover:border-slate-300"
           aria-label="Notifikasi">
            <svg class="w-5 h-5 text-[#121316]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            <span class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-[#FF6B6B] ring-2 ring-white"></span>
        </a>
    </div>
</header>

