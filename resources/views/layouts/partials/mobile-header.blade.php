<header class="border-b border-[var(--bento-border)] text-white lg:hidden" style="position:fixed;top:0;left:0;right:0;z-index:50;background:#0A1628;padding:44px 20px 12px 20px;min-height:100px;">
    @php
        $hour = now()->hour;
        if ($hour >= 5 && $hour < 12) {
            $greeting = 'Selamat pagi';
            $greetingEmoji = '☀️';
        } elseif ($hour >= 12 && $hour < 15) {
            $greeting = 'Selamat siang';
            $greetingEmoji = '🌤️';
        } elseif ($hour >= 15 && $hour < 19) {
            $greeting = 'Selamat sore';
            $greetingEmoji = '🌅';
        } else {
            $greeting = 'Selamat malam';
            $greetingEmoji = '🌙';
        }

    @endphp

    <div class="flex items-center justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[0.85rem] font-medium leading-none text-[var(--bento-text-dim)]">
                {{ $greeting }}
            </p>
            <p class="mt-1.5 truncate text-xl font-bold leading-tight tracking-[-0.02em] text-white">
                {{ auth()->user()->name }}
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <button type="button" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-[var(--bento-border)] bg-[var(--bento-card)] text-[var(--bento-text-muted)] transition active:scale-95" aria-label="Notifikasi">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 20a2 2 0 004 0" />
                </svg>
            </button>
            <a href="{{ route('profile.edit') }}" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-[var(--bento-teal)] bg-[var(--bento-card)] text-xs font-bold text-white transition active:scale-95" aria-label="Profil">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </a>
        </div>
    </div>
</header>
