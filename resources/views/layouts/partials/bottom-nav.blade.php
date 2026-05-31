<nav class="fixed bottom-0 left-0 right-0 z-40 mx-auto max-w-md lg:hidden">
    <div class="mx-3 mb-3 rounded-[24px] px-2 py-2 shadow-[0_12px_34px_rgba(0,0,0,0.28)]" style="background:rgba(19,32,68,0.95) !important;backdrop-filter:blur(12px) !important;box-shadow:0 4px 16px rgba(163,230,53,0.35) !important;border-top:1px solid rgba(255,255,255,0.08) !important;border-left:1px solid rgba(255,255,255,0.08) !important;border-right:1px solid rgba(255,255,255,0.08) !important;border-bottom:1px solid rgba(255,255,255,0.08) !important;">
        <div class="flex items-center justify-around">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-[var(--bento-purple)]' : 'text-[var(--bento-text-muted)]' }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ request()->routeIs('dashboard') ? 'bg-[var(--bento-card-2)]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.8 12 3l9 7.8" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10v10h5v-6h4v6h5V10" />
                    </svg>
                </span>
                <span class="text-[10px] font-medium">Beranda</span>
            </a>

            <a href="{{ route('budgets.index') }}" class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('budgets.index') ? 'text-[var(--bento-purple)]' : 'text-[var(--bento-text-muted)]' }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ request()->routeIs('budgets.index') ? 'bg-[var(--bento-card-2)]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 17v-6m4 6V7m4 10v-4m4 6H4" />
                    </svg>
                </span>
                <span class="text-[10px] font-medium">Anggaran</span>
            </a>

            <div class="flex flex-col items-center -mt-5">
                <a href="{{ route('transactions.create') }}" aria-label="Tambah transaksi" class="flex h-14 w-14 items-center justify-center rounded-full border-4 border-[var(--bento-bg)] bg-[var(--bento-teal)] text-black shadow-[0_8px_24px_rgba(74,222,128,0.22)] transition-transform active:scale-95" style="box-shadow:0 4px 16px rgba(163,230,53,0.35) !important;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                    </svg>
                </a>
                <span class="mt-1 text-[10px] font-medium text-[var(--bento-purple)]">Tambah</span>
            </div>

            <a href="{{ route('savings.index') }}" class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('savings.index') ? 'text-[var(--bento-purple)]' : 'text-[var(--bento-text-muted)]' }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ request()->routeIs('savings.index') ? 'bg-[var(--bento-card-2)]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 11.5c0-3 2.3-5.5 5.2-5.5H16a3 3 0 0 1 3 3v5.5a4.5 4.5 0 0 1-4.5 4.5H9.5A4.5 4.5 0 0 1 5 14.5v-3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h5m5 1h2m-7 8v2m-4-2v2" />
                    </svg>
                </span>
                <span class="text-[10px] font-medium">Tabungan</span>
            </a>

            <a href="{{ route('settings') }}" class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('settings') ? 'text-[var(--bento-purple)]' : 'text-[var(--bento-text-muted)]' }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ request()->routeIs('settings') ? 'bg-[var(--bento-card-2)]' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.05.05a2 2 0 0 1-2.83 2.83l-.05-.05A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 1.55V21a2 2 0 0 1-4 0v-.05A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.88.34l-.05.05a2 2 0 0 1-2.83-2.83l.05-.05A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 0 1 0-4h.05A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.88l-.05-.05a2 2 0 0 1 2.83-2.83l.05.05A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.55V3a2 2 0 0 1 4 0v.05A1.7 1.7 0 0 0 15 4.6a1.7 1.7 0 0 0 1.88-.34l.05-.05a2 2 0 0 1 2.83 2.83l-.05.05A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.55 1H21a2 2 0 0 1 0 4h-.05A1.7 1.7 0 0 0 19.4 15Z" />
                    </svg>
                </span>
                <span class="text-[10px] font-medium">Setelan</span>
            </a>
        </div>
    </div>
</nav>
