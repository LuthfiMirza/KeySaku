<x-app-layout>
    <x-slot:title>Beranda</x-slot:title>

    @php
        $firstExpense = $monthlyExpenseByCategory->first();
        $secondExpense = $monthlyExpenseByCategory->skip(1)->first();
        $topCategoryName = $firstExpense['category']?->name ?? 'Dining & Delivery';
        $topCategoryAmount = $firstExpense ? \App\Support\Money::format($firstExpense['total']) : 'Rp 831.000';
        $topCategoryBadge = $firstExpense ? $firstExpense['percentage'].'% dari total' : '-12% vs last month';

        $favCategoryName = $secondExpense['category']?->name ?? 'Transport & Fuel';
        $favCategoryAmount = $secondExpense ? \App\Support\Money::format($secondExpense['total']) : 'Rp 215.000';
        $favCategoryBadge = $secondExpense ? 'x'.max(1, count($latestTransactions)).' transaksi' : 'x9 transactions';
    @endphp

    <div class="px-5 pt-3 space-y-6 pb-6">

        {{-- Hero Balance Card (Sunset warm gradient backdrop) --}}
        <div class="relative overflow-hidden rounded-[32px] p-6 text-white shadow-[0_16px_36px_-6px_rgba(255,107,107,0.35)]"
             style="background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 50%, #FFA07A 100%);">
            <!-- Ambient soft blurred circles -->
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/20 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-black/10 blur-xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-black/60 font-medium tracking-wider uppercase">Total balance</p>
                        <p class="text-[11px] text-white/80 font-medium">Total Saldo</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-md text-[11px] font-semibold text-white border border-white/30">
                        {{ auth()->user()->preferred_currency ?? 'IDR' }}
                    </span>
                </div>

                {{-- Huge display amount with high contrast --}}
                <h2 class="text-4xl font-extrabold tracking-tight mt-2 tabular-nums text-[#121316]">
                    {{ \App\Support\Money::format($netBalance) }}
                </h2>

                <div class="mt-3 flex items-center justify-between rounded-2xl bg-white/20 backdrop-blur-md px-3.5 py-2 border border-white/30">
                    <span class="text-xs font-medium text-white/90">Saldo Tersedia</span>
                    <span class="text-sm font-bold tabular-nums text-white">{{ \App\Support\Money::format($availableBalance) }}</span>
                </div>

                @if ($shouldPromptInitialBalance)
                    <a href="{{ route('settings.initial-balance') }}"
                       class="mt-3 flex items-center justify-between gap-3 rounded-2xl bg-black/15 px-3.5 py-2.5 transition active:scale-[0.99] border border-white/20">
                        <span class="text-left">
                            <span class="block text-xs font-bold text-white">Saldo awal belum diatur</span>
                            <span class="block text-[11px] text-white/70">Atur saldo awal agar perhitungan total saldo akurat.</span>
                        </span>
                        <span class="shrink-0 text-xs font-bold text-white">Atur →</span>
                    </a>
                @endif

                {{-- Income & Expense Quick Stats --}}
                <div class="grid grid-cols-2 gap-2.5 mt-3">
                    <div class="rounded-2xl bg-white/20 backdrop-blur-md px-3.5 py-2.5 border border-white/30">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-black/60 block">Pemasukan</span>
                        <p class="text-xs font-black tabular-nums text-[#121316] mt-0.5">{{ \App\Support\Money::format($thisMonthIncome) }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/20 backdrop-blur-md px-3.5 py-2.5 border border-white/30">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-black/60 block">Pengeluaran</span>
                        <p class="text-xs font-black tabular-nums text-[#121316] mt-0.5">{{ \App\Support\Money::format($thisMonthExpense) }}</p>
                    </div>
                </div>

                {{-- 3 Circular Action Buttons with labels below --}}
                <div class="mt-5 pt-4 border-t border-white/25 grid grid-cols-3 gap-2">
                    {{-- Send Button --}}
                    <div class="flex flex-col items-center">
                        <a href="{{ route('transactions.create') }}"
                           class="w-13 h-13 rounded-full bg-[#121316] text-white flex items-center justify-center shadow-lg shadow-black/20 hover:scale-105 active:scale-95 transition"
                           aria-label="Send / Kirim Pengeluaran">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>
                        </a>
                        <span class="text-xs font-semibold text-[#121316] mt-2">Send</span>
                    </div>

                    {{-- Request Button --}}
                    <div class="flex flex-col items-center">
                        <a href="{{ route('transactions.create') }}"
                           class="w-13 h-13 rounded-full bg-[#121316] text-white flex items-center justify-center shadow-lg shadow-black/20 hover:scale-105 active:scale-95 transition"
                           aria-label="Request / Terima Pemasukan">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                            </svg>
                        </a>
                        <span class="text-xs font-semibold text-[#121316] mt-2">Request</span>
                    </div>

                    {{-- Add Button --}}
                    <div class="flex flex-col items-center">
                        <button type="button"
                                x-on:click="showVoiceModal = true"
                                class="w-13 h-13 rounded-full bg-[#121316] text-white flex items-center justify-center shadow-lg shadow-black/20 hover:scale-105 active:scale-95 transition"
                                aria-label="Add / Tambah Transaksi">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                        <span class="text-xs font-semibold text-[#121316] mt-2">Add</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metrics Grid (2 Columns) --}}
        <div class="grid grid-cols-2 gap-3.5">
            {{-- Card 1: Highest expense --}}
            <div class="card-porcelain rounded-[28px] p-4 relative overflow-hidden transition hover:shadow-md">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm shadow-sm">
                        ⚡
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 text-[10px] font-bold tracking-tight">
                        {{ $topCategoryBadge }}
                    </span>
                </div>
                <p class="text-[11px] font-medium text-[#8E8E93] uppercase tracking-wider">Highest expense</p>
                <h3 class="text-sm font-bold text-[#121316] mt-0.5 truncate">{{ $topCategoryName }}</h3>
                <p class="text-lg font-extrabold text-[#121316] mt-1.5 tabular-nums">{{ $topCategoryAmount }}</p>
            </div>

            {{-- Card 2: Favorite spending --}}
            <div class="card-porcelain rounded-[28px] p-4 relative overflow-hidden transition hover:shadow-md">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center text-sm shadow-sm">
                        ❤️
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[#121316] text-[10px] font-bold tracking-tight">
                        {{ $favCategoryBadge }}
                    </span>
                </div>
                <p class="text-[11px] font-medium text-[#8E8E93] uppercase tracking-wider">Favorite spending</p>
                <h3 class="text-sm font-bold text-[#121316] mt-0.5 truncate">{{ $favCategoryName }}</h3>
                <p class="text-lg font-extrabold text-[#121316] mt-1.5 tabular-nums">{{ $favCategoryAmount }}</p>
            </div>
        </div>

        {{-- Highlight Banner --}}
        <a href="{{ route('reports.index') }}"
           class="flex items-center justify-between p-4 rounded-[26px] bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-amber-500/5 border border-amber-200/50 shadow-[0_4px_16px_rgba(245,158,11,0.06)] transition hover:shadow-md active:scale-[0.99] group">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-11 h-11 rounded-2xl bg-amber-100/90 flex items-center justify-center text-xl shrink-0 shadow-sm group-hover:rotate-6 transition">
                    🪙
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[#121316] leading-tight">
                        Let's take a look at your financial overview for {{ now()->translatedFormat('F') }}!
                    </p>
                    <p class="text-[11px] text-[#8E8E93] mt-0.5 truncate">
                        Yuk lihat ikhtisar keuangan & analisis pengeluaran bulan ini
                    </p>
                </div>
            </div>
            <div class="w-7 h-7 rounded-full bg-white flex items-center justify-center text-[#121316] shrink-0 ml-2 shadow-sm group-hover:translate-x-1 transition">
                <svg class="w-4 h-4 text-[#121316]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </div>
        </a>

        {{-- Ringkasan Anggaran (Budget Insight) --}}
        <section>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-bold text-[#121316] tracking-tight">Ringkasan Anggaran</h2>
                <a href="{{ route('budgets.index') }}" aria-label="Lihat halaman anggaran" class="text-xs font-semibold text-[#FF6B6B] hover:text-[#FF8E53] transition inline-flex items-center gap-1">
                    <span>Lihat anggaran</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>

            <div class="card-porcelain rounded-[28px] p-5">
                @if (! $currentMonthBudgetInsight['hasBudgets'])
                    <div class="text-center py-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-xl mb-2">
                            📊
                        </div>
                        <p class="text-sm font-bold text-[#121316]">Belum ada anggaran bulan ini.</p>
                        <p class="mt-1 text-xs text-[#8E8E93]">Buat anggaran bulan ini untuk mulai memantau batas pengeluaran.</p>
                        <a href="{{ route('budgets.index') }}" aria-label="Buat anggaran bulan ini"
                           class="mt-4 inline-flex min-h-11 items-center rounded-2xl bg-[#121316] px-5 py-2.5 text-xs font-bold text-white shadow-md active:scale-95 transition">
                            Buat Anggaran
                        </a>
                    </div>
                @else
                    @php
                        $budgetRemainingLabel = $currentMonthBudgetInsight['totalRemaining'] >= 0
                            ? \App\Support\Money::format($currentMonthBudgetInsight['totalRemaining'])
                            : 'Lewat '.\App\Support\Money::format(abs($currentMonthBudgetInsight['totalRemaining']));
                    @endphp

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="rounded-2xl bg-[#FCF8F8] p-3.5 border border-[#F0EBEB]/60">
                            <p class="text-[#8E8E93] font-medium text-[11px]">Total Anggaran</p>
                            <p class="mt-1 text-sm font-extrabold text-[#121316] tabular-nums">{{ \App\Support\Money::format($currentMonthBudgetInsight['totalBudget']) }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#FCF8F8] p-3.5 border border-[#F0EBEB]/60">
                            <p class="text-[#8E8E93] font-medium text-[11px]">Terpakai</p>
                            <p class="mt-1 text-sm font-extrabold text-rose-500 tabular-nums">{{ \App\Support\Money::format($currentMonthBudgetInsight['totalUsed']) }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#FCF8F8] p-3.5 border border-[#F0EBEB]/60">
                            <p class="text-[#8E8E93] font-medium text-[11px]">Sisa</p>
                            <p class="mt-1 text-sm font-extrabold tabular-nums {{ $currentMonthBudgetInsight['totalRemaining'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">{{ $budgetRemainingLabel }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#FCF8F8] p-3.5 border border-[#F0EBEB]/60">
                            <p class="text-[#8E8E93] font-medium text-[11px]">Lewat Batas</p>
                            <p class="mt-1 text-sm font-extrabold text-[#121316]">{{ $currentMonthBudgetInsight['overLimitCount'] }} kategori</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Target Tabungan (Savings Goals Insight) --}}
        <section>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-bold text-[#121316] tracking-tight">Target Tabungan</h2>
                <a href="{{ route('savings.index') }}" aria-label="Lihat halaman tabungan" class="text-xs font-semibold text-[#FF6B6B] hover:text-[#FF8E53] transition inline-flex items-center gap-1">
                    <span>Lihat tabungan</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>

            <div class="card-porcelain rounded-[28px] p-5">
                @if (! $savingsGoalInsight['hasGoals'])
                    <div class="text-center py-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-xl mb-2">
                            🎯
                        </div>
                        <p class="text-sm font-bold text-[#121316]">Belum ada target tabungan aktif.</p>
                        <p class="mt-1 text-xs text-[#8E8E93]">Buat target untuk mulai memantau progres tabunganmu.</p>
                        <a href="{{ route('savings.index') }}" aria-label="Buat target tabungan"
                           class="mt-4 inline-flex min-h-11 items-center rounded-2xl bg-[#121316] px-5 py-2.5 text-xs font-bold text-white shadow-md active:scale-95 transition">
                            Lihat tabungan
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-3 gap-2.5 text-xs">
                        <div class="rounded-2xl bg-[#FCF8F8] p-3 border border-[#F0EBEB]/60">
                            <p class="text-[#8E8E93] text-[11px] font-medium">Terkumpul</p>
                            <p class="mt-1 text-xs sm:text-sm font-extrabold text-emerald-500 tabular-nums">{{ \App\Support\Money::format($savingsGoalInsight['totalSavingsCurrent']) }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#FCF8F8] p-3 border border-[#F0EBEB]/60">
                            <p class="text-[#8E8E93] text-[11px] font-medium">Total Target</p>
                            <p class="mt-1 text-xs sm:text-sm font-extrabold text-[#121316] tabular-nums">{{ \App\Support\Money::format($savingsGoalInsight['totalSavingsTarget']) }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#FCF8F8] p-3 border border-[#F0EBEB]/60">
                            <p class="text-[#8E8E93] text-[11px] font-medium">Progress</p>
                            <p class="mt-1 text-xs sm:text-sm font-extrabold text-sunset-gradient font-bold">{{ $savingsGoalInsight['savingsProgressPercentage'] }}%</p>
                        </div>
                    </div>

                    <div class="mt-4 h-2.5 rounded-full bg-slate-100 overflow-hidden" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $savingsGoalInsight['barPercentage'] }}" aria-label="Progress total target tabungan">
                        <div class="h-2.5 rounded-full bg-gradient-to-r from-[#FF6B6B] to-[#FF8E53] transition-all duration-500" style="width: {{ $savingsGoalInsight['barPercentage'] }}%"></div>
                    </div>

                    <div class="mt-4 space-y-2.5">
                        @foreach ($savingsGoalInsight['topSavingsGoals'] as $item)
                            <div class="rounded-2xl border border-[#F0EBEB] bg-[#FCF8F8] p-3.5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-[#121316]">{{ $item['goal']->name }}</p>
                                        <p class="mt-0.5 text-xs text-[#8E8E93] tabular-nums">{{ \App\Support\Money::format($item['goal']->current_amount) }} dari {{ \App\Support\Money::format($item['goal']->target_amount) }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-[#FF6B6B]">{{ $item['progressPercentage'] }}%</span>
                                </div>
                                <div class="mt-2.5 h-1.5 rounded-full bg-slate-200/70 overflow-hidden" aria-hidden="true">
                                    <div class="h-1.5 rounded-full bg-gradient-to-r from-[#FF6B6B] to-[#FFA07A]" style="width: {{ $item['barPercentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Transaksi Terakhir (Recent Transactions) --}}
        <section>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-bold text-[#121316] tracking-tight">Transaksi Terakhir</h2>
                <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-[#FF6B6B] hover:text-[#FF8E53] transition inline-flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>

            @if ($latestTransactions->isEmpty())
                <div class="card-porcelain rounded-[28px] p-6 text-center py-8">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 text-[#8E8E93] flex items-center justify-center mx-auto text-xl mb-2">
                        📋
                    </div>
                    <p class="text-sm font-bold text-[#121316]">Belum ada transaksi</p>
                    <p class="text-xs text-[#8E8E93] mt-1">Tambah transaksi pertamamu sekarang</p>
                    <a href="{{ route('transactions.create') }}" class="mt-4 inline-flex rounded-2xl bg-[#121316] px-5 py-3 text-xs font-bold text-white shadow-md active:scale-95 transition">
                        + Tambah Transaksi
                    </a>
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach ($latestTransactions as $transaction)
                        @php
                            $isIncome = $transaction->type === \App\Models\Transaction::TYPE_INCOME;
                            $amountPrefix = $isIncome ? '+' : '-';
                            $amountClass = $isIncome ? 'text-[#10B981]' : 'text-[#EF4444]';
                            $badgeClass = $isIncome ? 'bg-emerald-50 text-[#10B981]' : 'bg-rose-50 text-[#EF4444]';
                            $typeLabel = $isIncome ? 'Pemasukan' : 'Pengeluaran';
                        @endphp

                        <a href="{{ route('transactions.edit', $transaction) }}"
                           class="block card-porcelain rounded-[24px] p-3.5 transition hover:shadow-md active:scale-[0.99]">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    {{-- Circular category icon container --}}
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-lg shadow-sm"
                                          style="background-color: {{ $transaction->category?->color ?? '#F0EBEB' }}22; color: {{ $transaction->category?->color ?? '#121316' }};">
                                        {{ $transaction->category?->icon ?: '💸' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-[#121316]">{{ $transaction->category?->name ?? 'Tanpa kategori' }}</p>
                                        <p class="truncate text-xs text-[#8E8E93] mt-0.5">{{ $transaction->note ?: $transaction->transaction_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-extrabold tabular-nums {{ $amountClass }}">
                                        {{ $amountPrefix }} {{ \App\Support\Money::format($transaction->amount) }}
                                    </p>
                                    <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold {{ $badgeClass }}">{{ $typeLabel }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

    </div>
</x-app-layout>

