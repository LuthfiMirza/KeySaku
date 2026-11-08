<div class="space-y-6" x-data="{ showAccountListModal: false }">

    {{-- Status message alert --}}
    @if ($statusMessage)
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>✅</span>
                <span>{{ $statusMessage }}</span>
            </div>
            <button wire:click="$set('statusMessage', null)" class="text-emerald-500 hover:text-emerald-800 text-xs">✕</button>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- 1. LUXURY SKEUOMORPHIC CARDHOLDER POCKET (Dompet & Kartu Fisik)           --}}
    {{-- ========================================================================= --}}
    <div class="relative max-w-sm mx-auto select-none pt-2">

        {{-- Top Cardholder Controls --}}
        <div class="flex items-center justify-between px-2 mb-3">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-white text-xs font-bold shadow-sm">
                    💳
                </span>
                <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-500">
                    Kartu & Dompet
                </h3>
            </div>
            <button wire:click="openCreate"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm border border-slate-200/80 hover:bg-slate-50 active:scale-95 transition-all">
                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah
            </button>
        </div>

        @if ($accounts->isEmpty())
            {{-- Empty Wallet State --}}
            <div class="rounded-3xl bg-white border border-slate-200/70 p-8 text-center shadow-sm">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-3xl mb-3 shadow-inner">
                    👛
                </div>
                <h4 class="text-base font-extrabold text-slate-800">Dompet Masih Kosong</h4>
                <p class="mt-1 text-xs text-slate-500 max-w-xs mx-auto">
                    Tambahkan rekening bank, e-wallet (GoPay, DANA), atau kas tunai untuk mulai mengatur pos keuangan.
                </p>
                <button wire:click="openCreate"
                        type="button"
                        class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-500/25 active:scale-95 transition-transform">
                    + Buat Akun Pertama
                </button>
            </div>
        @else
            {{-- ================================================================= --}}
            {{-- THE SLEEVE: Stacked Physical Cards Peeking From Leather Pocket    --}}
            {{-- ================================================================= --}}
            <div class="relative overflow-visible">

                {{-- STACKED PEEKING CARDS --}}
                <div class="space-y-0 relative z-0 px-2 pb-2">
                    @foreach ($accounts as $index => $acc)
                        @php
                            $isSelected = ($selectedAccountId === $acc->id);
                            $lowerName = strtolower($acc->name);

                            // Dynamic brand gradient resolution
                            $cardGradient = match (true) {
                                str_contains($lowerName, 'bca') => 'linear-gradient(135deg, #00529C 0%, #003366 50%, #001D3D 100%)',
                                str_contains($lowerName, 'mandiri') => 'linear-gradient(135deg, #002B5B 0%, #0D407A 50%, #001938 100%)',
                                str_contains($lowerName, 'bri') => 'linear-gradient(135deg, #00529C 0%, #003B70 50%, #002447 100%)',
                                str_contains($lowerName, 'bni') => 'linear-gradient(135deg, #E65100 0%, #005E6A 60%, #00363D 100%)',
                                str_contains($lowerName, 'jago') => 'linear-gradient(135deg, #6B38FB 0%, #5218E6 50%, #3508A8 100%)',
                                str_contains($lowerName, 'gopay') => 'linear-gradient(135deg, #00AED6 0%, #0087A8 50%, #005F75 100%)',
                                str_contains($lowerName, 'dana') => 'linear-gradient(135deg, #118EEA 0%, #0D6DB5 50%, #094B7E 100%)',
                                str_contains($lowerName, 'ovo') => 'linear-gradient(135deg, #4C3494 0%, #362175 50%, #20114F 100%)',
                                str_contains($lowerName, 'shopee') => 'linear-gradient(135deg, #EE4D2D 0%, #D83C1C 50%, #9C2208 100%)',
                                str_contains($lowerName, 'kas') || str_contains($lowerName, 'tunai') || $acc->type === 'cash' => 'linear-gradient(135deg, #059669 0%, #047857 50%, #064E3B 100%)',
                                default => 'linear-gradient(135deg, ' . ($acc->color ?: '#4F46E5') . ' 0%, #1E1B4B 100%)',
                            };

                            // Realistic brand logo badge
                            $brandBadge = match (true) {
                                str_contains($lowerName, 'bca') => 'BCA',
                                str_contains($lowerName, 'mandiri') => 'MANDIRI',
                                str_contains($lowerName, 'bri') => 'BRI',
                                str_contains($lowerName, 'bni') => 'BNI',
                                str_contains($lowerName, 'jago') => 'JAGO',
                                str_contains($lowerName, 'gopay') => 'GoPay',
                                str_contains($lowerName, 'dana') => 'DANA',
                                str_contains($lowerName, 'ovo') => 'OVO',
                                str_contains($lowerName, 'shopee') => 'ShopeePay',
                                default => strtoupper(\App\Models\Account::typeLabel($acc->type)),
                            };

                            $maskedDigits = str_pad((string) (($acc->id * 179 + 4360) % 10000), 4, '0', STR_PAD_LEFT);
                            $stackMargin = $index === 0 ? '' : '-mt-12';
                        @endphp

                        <div wire:click="selectAccount({{ $acc->id }})"
                             class="group cursor-pointer transition-all duration-300 {{ $stackMargin }} {{ $isSelected ? 'relative z-20 -translate-y-3' : 'relative z-0 hover:-translate-y-2' }}"
                             style="touch-action: manipulation;">
                            
                            {{-- Physical Card --}}
                            <div class="w-full h-24 rounded-[22px] card-emboss p-3.5 flex flex-col justify-between text-white relative overflow-hidden transition-transform duration-300 {{ $isSelected ? 'ring-2 ring-white/90 scale-[1.01]' : 'opacity-90 group-hover:opacity-100' }}"
                                 style="background: {{ $cardGradient }};">

                                {{-- Card Glass Shimmer Effect --}}
                                <div class="absolute inset-0 bg-gradient-to-tr from-white/10 via-transparent to-white/5 pointer-events-none"></div>
                                <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-white/5 blur-xl pointer-events-none"></div>

                                {{-- Top Header of Card (Visible even when peeked) --}}
                                <div class="flex items-center justify-between relative z-10">
                                    <div class="flex items-center gap-2 min-w-0">
                                        {{-- Simulated EMV Chip or Icon --}}
                                        <div class="w-6 h-4.5 rounded-[4px] bg-amber-300/80 border border-amber-400 flex items-center justify-center text-[8px] font-black text-amber-900 shadow-sm shrink-0">
                                            <span>░░</span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-xs font-black tracking-wide truncate uppercase drop-shadow-sm">
                                                {{ $acc->name }}
                                            </span>
                                            <span class="block text-[10px] font-semibold text-white/70 tracking-wider">
                                                {{ $brandBadge }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Balance on Peeking Header --}}
                                    <div class="text-right shrink-0">
                                        <div class="text-xs font-black tracking-tight tabular-nums drop-shadow-sm">
                                            <x-money :amount="$acc->balance" />
                                        </div>
                                        @if (! $acc->is_active)
                                            <span class="text-[9px] font-bold text-amber-200 bg-black/30 px-1.5 py-0.5 rounded-full">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Bottom Details of Card (Peeks subtly or shows when selected) --}}
                                <div class="flex items-center justify-between text-[11px] font-medium text-white/70 relative z-10 pt-1">
                                    <span class="tracking-widest font-mono text-[10px]">•••• •••• •••• {{ $maskedDigits }}</span>
                                    @if ($isSelected)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-white bg-white/20 backdrop-blur-md px-2 py-0.5 rounded-full shadow-sm">
                                            ✓ Terpilih
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ============================================================= --}}
                {{-- FRONT LEATHER FLAP WITH TOP NOTCH CUTOUT                      --}}
                {{-- ============================================================= --}}
                <div class="relative z-30 -mt-10">

                    {{-- Top Notch Cutout SVG (Curved dip in the leather flap) --}}
                    <div class="w-full h-6 relative overflow-hidden -mb-[1px]">
                        <svg viewBox="0 0 360 24" class="w-full h-full block text-[#18191E]" fill="currentColor" preserveAspectRatio="none">
                            <path d="M 0 24 L 0 0 L 105 0 C 122 0 132 16 150 16 L 210 16 C 228 16 238 0 255 0 L 360 0 L 360 24 Z" />
                        </svg>
                        {{-- Specular Highlight Line on Notch Rim --}}
                        <svg viewBox="0 0 360 24" class="w-full h-full absolute inset-0 block text-white/20 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1" preserveAspectRatio="none">
                            <path d="M 0 0 L 105 0 C 122 0 132 16 150 16 L 210 16 C 228 16 238 0 255 0 L 360 0" />
                        </svg>
                    </div>

                    {{-- Leather Pocket Body Container --}}
                    <div class="leather-pocket rounded-b-[28px] px-6 pt-2 pb-6 relative overflow-hidden">
                        {{-- Ambient Leather Grain & Inner Highlight --}}
                        <div class="absolute inset-0 bg-radial-gradient from-white/5 to-transparent pointer-events-none opacity-40"></div>

                        {{-- Stitched Perimeter Frame --}}
                        <div class="leather-stitch rounded-2xl p-4 relative z-10">
                            
                            {{-- Wallet Total Header --}}
                            <div class="flex items-center justify-between text-slate-400 text-[11px] font-bold tracking-widest uppercase">
                                <span>Total Saldo Dompet</span>
                                <span class="inline-flex items-center gap-1.5 text-emerald-400 text-[10px] font-semibold bg-emerald-950/60 border border-emerald-500/20 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    {{ $activeCount }} Akun Aktif
                                </span>
                            </div>

                            {{-- Grand Total Balance Amount --}}
                            <div class="mt-1 flex items-baseline justify-between">
                                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight tabular-nums drop-shadow-md">
                                    <x-money :amount="$totalBalance" />
                                </h2>
                            </div>

                            <p class="mt-1 text-[11px] text-slate-400/90 font-medium truncate">
                                Tap kartu di atas untuk melihat detail & mutasinya
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ============================================================= --}}
                {{-- QUICK ACTION BUTTONS UNDER THE LEATHER FLAP                   --}}
                {{-- (Matching "+ Deposit", "↗", "Withdraw" in Reference)          --}}
                {{-- ============================================================= --}}
                <div class="relative z-30 -mt-2 px-3 flex items-center justify-center gap-2.5">
                    {{-- 1. Deposit / Masuk --}}
                    <a href="{{ route('transactions.create', ['type' => 'income', 'account_id' => $selectedAccountId]) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-full bg-[#121316] hover:bg-black text-white px-4 py-3 text-xs font-bold shadow-lg shadow-black/15 border border-white/10 active:scale-95 transition-all">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>+ Masuk</span>
                    </a>

                    {{-- 2. Voice AI Quick Trigger (Center Circle) --}}
                    <button type="button"
                            x-on:click="$dispatch('open-voice-modal')"
                            class="w-12 h-12 shrink-0 rounded-full bg-gradient-to-br from-[#FF6B6B] to-[#FF8E53] text-white flex items-center justify-center shadow-lg shadow-orange-500/30 ring-4 ring-[#FCF8F8] active:scale-90 transition-transform group"
                            title="Input Suara AI">
                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3Z" />
                        </svg>
                    </button>

                    {{-- 3. Withdraw / Keluar --}}
                    <a href="{{ route('transactions.create', ['type' => 'expense', 'account_id' => $selectedAccountId]) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-full bg-[#121316] hover:bg-black text-white px-4 py-3 text-xs font-bold shadow-lg shadow-black/15 border border-white/10 active:scale-95 transition-all">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                        </svg>
                        <span>− Keluar</span>
                    </a>
                </div>

            </div>
        @endif
    </div>

    {{-- ========================================================================= --}}
    {{-- 2. SELECTED CARD DETAIL & MUTASI (Detail Kartu Terpilih & Card History)   --}}
    {{-- ========================================================================= --}}
    @if ($selectedAccount)
        <div class="space-y-4 pt-1">

            {{-- Selected Card Header & Management --}}
            <div class="rounded-3xl bg-white border border-slate-200/80 p-5 shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-xl shadow-sm"
                              style="background-color: {{ $selectedAccount->color ?: '#6366f1' }}20; color: {{ $selectedAccount->color ?: '#6366f1' }}; border: 1px solid {{ $selectedAccount->color ?: '#6366f1' }}40;">
                            {{ $selectedAccount->icon ?: ($selectedAccount->type === 'cash' ? '💵' : ($selectedAccount->type === 'bank' ? '🏦' : '📱')) }}
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-extrabold text-slate-800 truncate">
                                    {{ $selectedAccount->name }}
                                </h4>
                                @if (! $selectedAccount->is_active)
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-400">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400">
                                {{ \App\Models\Account::typeLabel($selectedAccount->type) }} · Rekening #{{ $selectedAccount->id }}
                            </p>
                        </div>
                    </div>

                    {{-- Current Balance on Card --}}
                    <div class="text-right shrink-0">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Saldo Akun</p>
                        <p class="text-base font-black text-slate-900 tabular-nums">
                            <x-money :amount="$selectedAccount->balance" />
                        </p>
                    </div>
                </div>

                {{-- Monthly Cashflow Insight Chips for this Card --}}
                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <div class="rounded-2xl bg-emerald-50/70 border border-emerald-100/80 p-3">
                        <div class="flex items-center gap-1.5 text-emerald-600 text-[11px] font-bold">
                            <span>↓</span>
                            <span>Pemasukan Bulan Ini</span>
                        </div>
                        <p class="mt-1 text-xs font-black text-emerald-700 tabular-nums">
                            <x-money :amount="$selectedMonthlyIncome" />
                        </p>
                    </div>

                    <div class="rounded-2xl bg-rose-50/70 border border-rose-100/80 p-3">
                        <div class="flex items-center gap-1.5 text-rose-600 text-[11px] font-bold">
                            <span>↑</span>
                            <span>Pengeluaran Bulan Ini</span>
                        </div>
                        <p class="mt-1 text-xs font-black text-rose-700 tabular-nums">
                            <x-money :amount="$selectedMonthlyExpense" />
                        </p>
                    </div>
                </div>

                {{-- Card Management Action Buttons --}}
                <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                    <button wire:click="openEdit({{ $selectedAccount->id }})"
                            type="button"
                            class="flex-1 rounded-xl bg-slate-100 hover:bg-slate-200 px-3 py-2 text-xs font-bold text-slate-700 active:scale-95 transition-all text-center">
                        ✏️ Edit Akun
                    </button>
                    <button wire:click="toggleActive({{ $selectedAccount->id }})"
                            type="button"
                            class="flex-1 rounded-xl {{ $selectedAccount->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-700' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }} px-3 py-2 text-xs font-bold active:scale-95 transition-all text-center">
                        {{ $selectedAccount->is_active ? '⏸ Nonaktifkan' : '▶️ Aktifkan' }}
                    </button>
                    <button wire:click="confirmDelete({{ $selectedAccount->id }})"
                            type="button"
                            class="rounded-xl bg-rose-50 hover:bg-rose-100 px-3 py-2 text-xs font-bold text-rose-600 active:scale-95 transition-all">
                        🗑️ Hapus
                    </button>
                </div>
            </div>

            {{-- Card History / Mutasi Transaksi Kartu --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        Mutasi {{ $selectedAccount->name }}
                    </h3>
                    <a href="{{ route('transactions.index', ['account_id' => $selectedAccount->id]) }}"
                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Lihat Semua →
                    </a>
                </div>

                @if ($selectedTransactions->isEmpty())
                    <div class="rounded-3xl bg-white border border-slate-200/70 p-6 text-center shadow-sm">
                        <p class="text-2xl mb-1">📑</p>
                        <p class="text-xs font-bold text-slate-700">Belum Ada Transaksi</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">
                            Transaksi yang dicatat memakai akun {{ $selectedAccount->name }} akan tampil di sini.
                        </p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach ($selectedTransactions as $tx)
                            <div class="rounded-2xl bg-white border border-slate-200/80 p-3.5 flex items-center justify-between gap-3 shadow-sm hover:border-slate-300 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    {{-- Category Icon / Direction Badge --}}
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base shrink-0 {{ $tx->type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        {{ $tx->category?->icon ?: ($tx->type === 'income' ? '💰' : '🛒') }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">
                                            {{ $tx->note ?: ($tx->category?->name ?: 'Transaksi') }}
                                        </p>
                                        <p class="text-[11px] text-slate-400">
                                            {{ $tx->transaction_date->translatedFormat('d M Y') }}
                                            @if ($tx->category)
                                                · <span class="text-slate-500">{{ $tx->category->name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <p class="text-xs font-extrabold tabular-nums {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-slate-900' }}">
                                        {{ $tx->type === 'income' ? '+' : '-' }}<x-money :amount="$tx->amount" />
                                    </p>
                                    <span class="text-[9px] font-bold text-slate-400">
                                        {{ $tx->type === 'income' ? '↙ Masuk' : '↗ Keluar' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- 3. MODAL / DRAWER FORM: CREATE / EDIT ACCOUNT                             --}}
    {{-- ========================================================================= --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             x-transition>
            <div class="w-full max-w-md rounded-3xl bg-white border border-slate-200 p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto"
                 @click.outside="$wire.cancelForm()">

                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-800">
                        {{ $editingId ? 'Edit Akun Keuangan' : 'Tambah Akun / Dompet Baru' }}
                    </h3>
                    <button wire:click="cancelForm" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                {{-- Fast Presets for Indonesian Payment Methods --}}
                @if (! $editingId)
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Pilih Cepat Metode Pembayaran
                        </label>
                        <div class="grid grid-cols-4 gap-2">
                            <button type="button"
                                    wire:click="$set('name', 'BCA'); $set('type', 'bank'); $set('color', '#00529C'); $set('icon', '🏦')"
                                    class="rounded-xl border border-slate-200 p-2 text-center hover:bg-slate-50 active:scale-95 transition-transform">
                                <span class="text-sm">🏦</span>
                                <span class="block text-[10px] font-bold text-slate-700 mt-0.5">BCA</span>
                            </button>
                            <button type="button"
                                    wire:click="$set('name', 'Bank Mandiri'); $set('type', 'bank'); $set('color', '#002B5B'); $set('icon', '🏦')"
                                    class="rounded-xl border border-slate-200 p-2 text-center hover:bg-slate-50 active:scale-95 transition-transform">
                                <span class="text-sm">🏦</span>
                                <span class="block text-[10px] font-bold text-slate-700 mt-0.5">Mandiri</span>
                            </button>
                            <button type="button"
                                    wire:click="$set('name', 'GoPay'); $set('type', 'ewallet'); $set('color', '#00AED6'); $set('icon', '📱')"
                                    class="rounded-xl border border-slate-200 p-2 text-center hover:bg-slate-50 active:scale-95 transition-transform">
                                <span class="text-sm">📱</span>
                                <span class="block text-[10px] font-bold text-slate-700 mt-0.5">GoPay</span>
                            </button>
                            <button type="button"
                                    wire:click="$set('name', 'DANA'); $set('type', 'ewallet'); $set('color', '#118EEA'); $set('icon', '📱')"
                                    class="rounded-xl border border-slate-200 p-2 text-center hover:bg-slate-50 active:scale-95 transition-transform">
                                <span class="text-sm">📱</span>
                                <span class="block text-[10px] font-bold text-slate-700 mt-0.5">DANA</span>
                            </button>
                            <button type="button"
                                    wire:click="$set('name', 'Kas Dompet'); $set('type', 'cash'); $set('color', '#059669'); $set('icon', '💵')"
                                    class="rounded-xl border border-slate-200 p-2 text-center hover:bg-slate-50 active:scale-95 transition-transform">
                                <span class="text-sm">💵</span>
                                <span class="block text-[10px] font-bold text-slate-700 mt-0.5">Tunai</span>
                            </button>
                            <button type="button"
                                    wire:click="$set('name', 'Bank Jago'); $set('type', 'bank'); $set('color', '#6B38FB'); $set('icon', '🏦')"
                                    class="rounded-xl border border-slate-200 p-2 text-center hover:bg-slate-50 active:scale-95 transition-transform">
                                <span class="text-sm">🏦</span>
                                <span class="block text-[10px] font-bold text-slate-700 mt-0.5">Jago</span>
                            </button>
                            <button type="button"
                                    wire:click="$set('name', 'ShopeePay'); $set('type', 'ewallet'); $set('color', '#EE4D2D'); $set('icon', '📱')"
                                    class="rounded-xl border border-slate-200 p-2 text-center hover:bg-slate-50 active:scale-95 transition-transform">
                                <span class="text-sm">📱</span>
                                <span class="block text-[10px] font-bold text-slate-700 mt-0.5">Shopee</span>
                            </button>
                            <button type="button"
                                    wire:click="$set('name', 'OVO'); $set('type', 'ewallet'); $set('color', '#4C3494'); $set('icon', '📱')"
                                    class="rounded-xl border border-slate-200 p-2 text-center hover:bg-slate-50 active:scale-95 transition-transform">
                                <span class="text-sm">📱</span>
                                <span class="block text-[10px] font-bold text-slate-700 mt-0.5">OVO</span>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Name --}}
                <div>
                    <label for="acc-name" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                        Nama Akun / Dompet
                    </label>
                    <input id="acc-name" wire:model="name" type="text" placeholder="Contoh: BCA, GoPay, Dompet Tunai"
                           class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                    @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label for="acc-type" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                        Jenis Akun
                    </label>
                    <select id="acc-type" wire:model="type"
                            class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                        @foreach ($typeLabels as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                {{-- Balance --}}
                <div>
                    <label for="acc-balance" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                        Saldo Saat Ini (Rp)
                    </label>
                    <input id="acc-balance" wire:model="balance" type="number" min="0" step="1000" placeholder="0"
                           class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                    @error('balance') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                {{-- Color & Icon Picker --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="acc-icon" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                            Ikon Emoji
                        </label>
                        <input id="acc-icon" wire:model="icon" type="text" placeholder="💰"
                               class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-center text-lg focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="acc-color" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">
                            Warna Kartu
                        </label>
                        <div class="flex items-center gap-2">
                            <input id="acc-color" wire:model="color" type="color"
                                   class="h-11 w-full cursor-pointer rounded-2xl border border-slate-200 p-1 bg-white">
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="grid grid-cols-2 gap-3 pt-3">
                    <button wire:click="cancelForm"
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 active:scale-[0.98] transition-transform">
                        Batal
                    </button>
                    <button wire:click="save"
                            type="button"
                            class="rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-md shadow-indigo-500/25 active:scale-[0.98] transition-transform">
                        Simpan Akun
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- 4. MODAL DELETE CONFIRMATION                                              --}}
    {{-- ========================================================================= --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="w-full max-w-sm rounded-3xl bg-white border border-rose-200 p-6 shadow-2xl space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl mx-auto">
                    ⚠️
                </div>
                <h4 class="text-center text-sm font-bold text-slate-800">Hapus Akun Keuangan Ini?</h4>
                <p class="text-center text-xs text-slate-500">
                    Tindakan ini tidak dapat dibatalkan. Riwayat mutasi akun ini akan tetap tersimpan.
                </p>
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button wire:click="cancelDelete"
                            type="button"
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs font-bold text-slate-600">
                        Batal
                    </button>
                    <button wire:click="delete"
                            type="button"
                            class="rounded-2xl bg-rose-600 px-4 py-3 text-xs font-bold text-white shadow-md shadow-rose-600/25">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
