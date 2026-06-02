<nav class="fixed bottom-0 left-0 right-0 z-40 mx-auto max-w-md lg:hidden">
    <div class="mx-3 mb-3 rounded-[24px] px-2 py-2 shadow-[0_12px_34px_rgba(0,0,0,0.28)]" style="background:rgba(19,32,68,0.95) !important;backdrop-filter:blur(12px) !important;box-shadow:0 4px 16px rgba(163,230,53,0.35) !important;border:1px solid rgba(255,255,255,0.08) !important;">
        <div class="flex items-center justify-around">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-[#A3E635]' : 'text-[var(--bento-text-muted)]' }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ request()->routeIs('dashboard') ? 'bg-[var(--bento-card-2)]' : '' }}">
                    <i data-lucide="house" class="h-5 w-5" aria-hidden="true"></i>
                </span>
                <span class="text-[10px] font-medium">Beranda</span>
            </a>

            <a href="{{ route('budgets.index') }}" class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('budgets.index') ? 'text-[#A3E635]' : 'text-[var(--bento-text-muted)]' }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ request()->routeIs('budgets.index') ? 'bg-[var(--bento-card-2)]' : '' }}">
                    <i data-lucide="chart-column-increasing" class="h-5 w-5" aria-hidden="true"></i>
                </span>
                <span class="text-[10px] font-medium">Anggaran</span>
            </a>

            <div class="flex flex-col items-center -mt-5">
                <a href="{{ route('transactions.create') }}" aria-label="Tambah transaksi" class="flex h-14 w-14 items-center justify-center rounded-full border-4 border-[var(--bento-bg)] bg-[var(--bento-teal)] text-black shadow-[0_8px_24px_rgba(74,222,128,0.22)] transition-transform active:scale-95" style="box-shadow:0 4px 16px rgba(163,230,53,0.35) !important;">
                    <i data-lucide="plus" class="h-6 w-6" aria-hidden="true"></i>
                </a>
                <span class="mt-1 text-[10px] font-medium text-[#A3E635]">Tambah</span>
            </div>

            <a href="{{ route('savings.index') }}" class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('savings.index') ? 'text-[#A3E635]' : 'text-[var(--bento-text-muted)]' }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ request()->routeIs('savings.index') ? 'bg-[var(--bento-card-2)]' : '' }}">
                    <i data-lucide="piggy-bank" class="h-5 w-5" aria-hidden="true"></i>
                </span>
                <span class="text-[10px] font-medium">Tabungan</span>
            </a>

            <a href="{{ route('settings') }}" class="flex flex-col items-center gap-1 rounded-2xl px-3 py-2 transition-all duration-200 {{ request()->routeIs('settings*') ? 'text-[#A3E635]' : 'text-[var(--bento-text-muted)]' }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl {{ request()->routeIs('settings*') ? 'bg-[var(--bento-card-2)]' : '' }}">
                    <i data-lucide="settings" class="h-5 w-5" aria-hidden="true"></i>
                </span>
                <span class="text-[10px] font-medium">Setelan</span>
            </a>
        </div>
    </div>
</nav>
