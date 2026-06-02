<div class="flex h-full flex-col border-r border-[var(--bento-border)] bg-[var(--bento-card)] text-[var(--bento-text)]">
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

    <div class="flex h-16 items-center justify-between border-b border-[var(--bento-border)] px-4">
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-[var(--bento-purple)] font-extrabold text-white">K</div>
            <span x-show="sidebarOpen" x-transition class="truncate text-xl font-black text-[var(--bento-purple)]">KeySaku</span>
        </div>
        <button type="button" @click="sidebarOpen = ! sidebarOpen" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[var(--bento-text-muted)] transition hover:bg-indigo-50 hover:text-[var(--bento-text)]" aria-label="Toggle sidebar">
            <svg x-show="sidebarOpen" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <svg x-show="! sidebarOpen" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    <div class="border-b border-[var(--bento-border)] px-3 py-4">
        <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-2" :class="sidebarOpen ? '' : 'justify-center'">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[var(--bento-purple)] text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div x-show="sidebarOpen" x-transition class="min-w-0">
                <p class="text-xs leading-none text-[var(--bento-text-muted)]">{{ $greeting }} {{ $greetingEmoji }}</p>
                <p class="mt-1 truncate text-sm font-semibold leading-none text-[var(--bento-text)]">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>

    @php
        $items = [
            ['label' => 'Beranda', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['label' => 'Transaksi', 'route' => 'transactions.index', 'active' => 'transactions.*', 'icon' => 'M9 5h6m-6 4h6m-6 4h6m-8 8h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
            ['label' => 'Anggaran', 'route' => 'budgets.index', 'active' => 'budgets.index', 'icon' => 'M4 4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2h-8l-2-2H4z'],
            ['label' => 'Tabungan', 'route' => 'savings.index', 'active' => 'savings.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            ['label' => 'Laporan', 'route' => 'reports.index', 'active' => 'reports.index', 'icon' => 'M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0V7a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8'],
            ['label' => 'Kategori', 'route' => 'categories', 'active' => 'categories', 'icon' => 'M7 7h.01M7 3h5l7 7-8 8-7-7V6a3 3 0 013-3z'],
            ['label' => 'Pengaturan', 'route' => 'settings', 'active' => 'settings*', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0'],
        ];
    @endphp

    <nav class="flex-1 space-y-1.5 px-3 py-4">
        @foreach ($items as $item)
            @php $active = request()->routeIs($item['active']); @endphp
            <a href="{{ route($item['route']) }}" class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all {{ $active ? 'bg-[var(--bento-purple)] font-semibold text-white' : 'text-[var(--bento-text-muted)] hover:bg-indigo-50 hover:text-[var(--bento-text)]' }}" :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                <span x-show="sidebarOpen" x-transition class="truncate">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-[var(--bento-border)] px-3 py-4">
        <a href="{{ route('transactions.create') }}" class="mb-3 flex items-center justify-center gap-2 rounded-xl bg-[var(--bento-purple)] py-2.5 text-sm font-bold text-white shadow-[0_10px_28px_rgba(99,102,241,0.22)] transition hover:brightness-105" :class="sidebarOpen ? 'w-full px-3' : 'mx-auto h-10 w-10 px-0'">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span x-show="sidebarOpen" x-transition>Tambah Transaksi</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-[var(--bento-text-muted)] transition hover:bg-indigo-50 hover:text-[var(--bento-text)]" :class="sidebarOpen ? '' : 'mx-auto h-10 w-10 px-0'">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
                <span x-show="sidebarOpen" x-transition>Keluar</span>
            </button>
        </form>
    </div>
</div>
