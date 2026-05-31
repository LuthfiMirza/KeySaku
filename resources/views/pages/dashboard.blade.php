<x-app-layout>
    @php
        $formatRupiah = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
        $budgetPct = $currentMonthBudgetInsight['totalBudget'] > 0
            ? min(100, round(($currentMonthBudgetInsight['totalUsed'] / $currentMonthBudgetInsight['totalBudget']) * 100))
            : 0;
        $budgetFillClass = $budgetPct >= 90 ? 'bg-[var(--bento-rose)]' : ($budgetPct >= 70 ? 'bg-[var(--bento-amber)]' : 'bg-[var(--bento-teal)]');
        $balanceRaw = (int) ($netBalance ?? 0);
        $pointsTotal = data_get($userPoints ?? null, 'total_points', $userPoints ?? 0);
        $streakTotal = data_get($userPoints ?? null, 'streak_days', $streakDays ?? 0);
        $levelTotal = data_get($userPoints ?? null, 'level', 1);
        $topSavings = collect($savingsGoalInsight['topSavingsGoals'] ?? []);
        $primarySaving = $topSavings->first();
        $savingProgress = $primarySaving['progressPercentage'] ?? ($savingsGoalInsight['savingsProgressPercentage'] ?? 0);
        $savingOffset = 125.6 - (125.6 * min(100, max(0, (float) $savingProgress)) / 100);
        $sevenDayExpenseTotal = 0;
    @endphp
    <x-slot:title>Beranda</x-slot:title>

    <div class="block lg:hidden px-5" style="padding-bottom:100px;overflow:visible;">
        <section style="display:flex;gap:8px;padding:0;overflow-x:auto;scrollbar-width:none;margin-top:8px;margin-bottom:12px;">
            <div style="display:flex;align-items:center;gap:6px;background:#132044;border:1px solid rgba(255,255,255,0.1);border-radius:999px;padding:6px 14px;white-space:nowrap;flex-shrink:0;">
                <span>🪙</span>
                <span style="color:#94A3B8;font-size:0.75rem;">Poin</span>
                <span style="color:#fff;font-weight:700;font-size:0.85rem;">{{ $pointsTotal }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;background:#132044;border:1px solid rgba(255,255,255,0.1);border-radius:999px;padding:6px 14px;white-space:nowrap;flex-shrink:0;">
                <span>🔥</span>
                <span style="color:#94A3B8;font-size:0.75rem;">Streak</span>
                <span style="color:#fff;font-weight:700;font-size:0.85rem;">{{ $streakTotal }} hari</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;background:#132044;border:1px solid rgba(255,255,255,0.1);border-radius:999px;padding:6px 14px;white-space:nowrap;flex-shrink:0;">
                <span style="background:#A3E635;color:#0A1628;font-weight:800;font-size:0.7rem;padding:2px 6px;border-radius:999px;">LV</span>
                <span style="color:#fff;font-weight:700;font-size:0.85rem;">{{ $levelTotal }}</span>
            </div>
        </section>

        <section style="padding:16px 20px 20px 20px;">
            <p style="color:#64748B;font-size:0.85rem;margin:0 0 4px 0;font-weight:400;">Siap kelola keuangan</p>
            <h2 style="color:#FFFFFF;font-size:1.5rem;font-weight:800;margin:0;line-height:1.3;letter-spacing:-0.01em;">
                Hari ini, <span style="color:#A3E635;">{{ auth()->user()->name }}</span>?
            </h2>
        </section>

        <section class="relative overflow-hidden shadow-[0_24px_54px_rgba(79,70,229,0.28)]" style="background:linear-gradient(135deg,#1A2B52,#132044) !important;border:1px solid rgba(163,230,53,0.15) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 8px 32px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;color:#FFFFFF !important;">
            <div class="pointer-events-none absolute -right-16 -top-20 h-40 w-40 rounded-full bg-[var(--bento-card)]/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-20 bottom-0 h-36 w-36 rounded-full bg-[var(--bento-card)]/10 blur-3xl"></div>

            <div class="relative">
                <p class="font-semibold uppercase" style="color:#94A3B8 !important;font-size:10px;letter-spacing:0.06em;">Saldo Saat Ini</p>
                <div x-data="{ displayed: 0, target: {{ $balanceRaw }}, init() { if (this.target === 0) return; const duration = 800; const start = performance.now(); const update = (now) => { const elapsed = now - start; const progress = Math.min(elapsed / duration, 1); const ease = 1 - Math.pow(1 - progress, 3); this.displayed = Math.round(this.target * ease); if (progress < 1) requestAnimationFrame(update); }; requestAnimationFrame(update); } }" x-init="init()">
                    <p class="font-display mt-3 leading-[0.95] tracking-[-0.055em] tabular-nums" style="color: #FFFFFF !important; font-weight: 900; font-size: 2.5rem; letter-spacing:-0.02em; line-height:1.1; white-space:nowrap;">
                        Rp <span x-text="displayed.toLocaleString('id-ID')">{{ number_format($balanceRaw, 0, ',', '.') }}</span>
                    </p>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:20px;">
                    <div style="background:#A3E635;border-radius:16px;padding:14px;">
                        <p style="color:#0A1628;font-size:10px;font-weight:600;margin:0 0 4px 0;opacity:0.7;letter-spacing:0.06em;">PEMASUKAN</p>
                        <p style="color:#0A1628;font-size:1rem;font-weight:800;margin:0;">{{ $formatRupiah($thisMonthIncome) }}</p>
                    </div>
                    <div style="background:#1E3060 !important;border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:14px;">
                        <p style="color:#94A3B8;font-size:10px;font-weight:600;margin:0 0 4px 0;letter-spacing:0.06em;">PENGELUARAN</p>
                        <p style="color:#F87171;font-size:1rem;font-weight:800;margin:0;">{{ $formatRupiah($thisMonthExpense) }}</p>
                    </div>
                </div>

            </div>
        </section>

        <section class="rounded-[28px] border border-[var(--bento-border)] bg-[var(--bento-card)] p-5 shadow-[0_14px_34px_rgba(99,102,241,0.08)]" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 4px 24px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--bento-text-muted)]" style="letter-spacing:0.06em;font-size:10px;">Target Tabungan</p>
                    <h2 class="mt-1 text-base font-semibold text-[var(--bento-text-muted)]">Progress Simpanan</h2>
                </div>
                <a href="{{ route('savings.index') }}" class="text-xs font-semibold text-[var(--bento-purple)]">Detail</a>
            </div>
            @if (! $savingsGoalInsight['hasGoals'])
                <div class="rounded-3xl bg-[var(--bento-card)]/70 p-5 text-center">
                    <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--bento-card)]/20 text-[var(--bento-purple)]"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3m10-10h-3M5 12H2"/></svg></div>
                    <p class="mt-2 text-sm font-semibold text-[var(--bento-text)]">Belum ada target tabungan aktif.</p>
                    <p class="mt-1 text-xs text-[var(--bento-text-muted)]">Buat target untuk mulai memantau progres tabunganmu.</p>
                    <a href="{{ route('savings.index') }}" class="mt-4 inline-flex rounded-full bg-[var(--bento-purple)] px-4 py-2 text-xs font-bold text-white">Lihat tabungan</a>
                </div>
            @else
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    @foreach ($savingsGoalInsight['topSavingsGoals'] as $item)
                        <div style="background:#1A2B52;border:1px solid rgba(163,230,53,0.1) !important;border-radius:20px;padding:16px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px;">
                                <span style="background:rgba(163,230,53,0.15);color:#A3E635;border-radius:999px;padding:3px 10px;font-size:10px;font-weight:700;letter-spacing:0.06em;">TARGET</span>
                            </div>
                            <p style="color:#fff;font-weight:700;font-size:0.9rem;margin:0 0 4px 0;line-height:1.25;">{{ $item['goal']->name }}</p>
                            <p style="color:#94A3B8;font-size:0.7rem;margin:0 0 10px 0;line-height:1.35;">{{ $formatRupiah($item['goal']->current_amount) }} terkumpul</p>
                            <p style="color:#A3E635;font-size:1.3rem;font-weight:800;margin:0 0 8px 0;">{{ $item['progressPercentage'] }}%</p>
                            <div style="background:rgba(255,255,255,0.1);border-radius:999px;height:5px !important;overflow:hidden;" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $item['barPercentage'] }}" aria-label="Progress {{ $item['goal']->name }}">
                                <div style="background:linear-gradient(90deg,#A3E635,#4ADE80) !important;border-radius:999px !important;height:5px !important;width:{{ $item['barPercentage'] }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-[28px] border border-[var(--bento-border)] bg-[var(--bento-card)] p-5 shadow-[0_14px_34px_rgba(99,102,241,0.08)]" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 4px 24px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--bento-text-muted)]" style="letter-spacing:0.06em;font-size:10px;">Bulan Ini</p>
                    <h2 class="mt-1 text-base font-semibold text-[var(--bento-text)]">Ringkasan</h2>
                </div>
                <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-[var(--bento-purple)]">Laporan</a>
            </div>
            @php $netCashflow = (float) $thisMonthIncome - (float) $thisMonthExpense; @endphp
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-3xl border border-[var(--bento-border)] bg-[var(--bento-card-2)] p-4">
                    <p class="text-xs text-[var(--bento-text-muted)]">Pemasukan</p>
                    <p class="mt-2 text-sm font-bold tabular-nums" style="color:#A3E635 !important;">{{ $formatRupiah($thisMonthIncome) }}</p>
                </div>
                <div class="rounded-3xl border border-[var(--bento-border)] bg-[var(--bento-card-2)] p-4">
                    <p class="text-xs text-[var(--bento-text-muted)]">Pengeluaran</p>
                    <p class="mt-2 text-sm font-bold text-[var(--bento-rose)] tabular-nums">{{ $formatRupiah($thisMonthExpense) }}</p>
                </div>
                <div class="col-span-2 rounded-3xl bg-[var(--bento-card)]/70 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs text-[var(--bento-text-muted)]">Net cashflow</p>
                        <p class="text-sm font-bold tabular-nums {{ $netCashflow >= 0 ? 'text-[var(--bento-teal)]' : 'text-[var(--bento-rose)]' }}">{{ $netCashflow >= 0 ? '+' : '-' }} {{ $formatRupiah(abs($netCashflow)) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[28px] border border-[var(--bento-border)] bg-[var(--bento-card)] p-5 shadow-[0_16px_36px_rgba(16,185,129,0.14)]" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 4px 24px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--bento-text-muted)]" style="letter-spacing:0.06em;font-size:10px;">Anggaran</p>
                    <h2 class="mt-1 text-base font-semibold text-[var(--bento-text)]">Ringkasan Anggaran</h2>
                </div>
                <a href="{{ route('budgets.index') }}" class="text-xs font-semibold text-[var(--bento-purple)]">Atur</a>
            </div>
            @if (! $currentMonthBudgetInsight['hasBudgets'])
                <div class="rounded-3xl bg-[var(--bento-card)]/70 p-4">
                    <p class="text-sm font-semibold text-[var(--bento-text)]">Belum ada anggaran bulan ini.</p>
                    <p class="mt-1 text-xs text-[var(--bento-text-muted)]">Buat anggaran bulan ini untuk mulai memantau batas pengeluaran.</p>
                    <a href="{{ route('budgets.index') }}" aria-label="Buat anggaran bulan ini" class="mt-4 inline-flex rounded-full bg-[var(--bento-purple)] px-4 py-2 text-xs font-bold text-white">Buat Anggaran</a>
                </div>
            @else
                @php
                    $budgetRemainingLabel = $currentMonthBudgetInsight['totalRemaining'] >= 0
                        ? $formatRupiah($currentMonthBudgetInsight['totalRemaining'])
                        : 'Lewat '.$formatRupiah(abs($currentMonthBudgetInsight['totalRemaining']));
                @endphp
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs text-[var(--bento-text-muted)]">Terpakai</p>
                        <p class="mt-1 font-display text-3xl text-[var(--bento-amber)]">{{ $budgetPct }}%</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-[var(--bento-text-muted)]">Sisa</p>
                        <p class="mt-1 text-sm font-bold {{ $currentMonthBudgetInsight['totalRemaining'] >= 0 ? 'text-[var(--bento-teal)]' : 'text-[var(--bento-rose)]' }} tabular-nums">{{ $budgetRemainingLabel }}</p>
                    </div>
                </div>
                <div class="mt-4" style="height:8px !important;border-radius:999px !important;background:rgba(255,255,255,0.1) !important;">
                    <div class="" style="height:8px !important;border-radius:999px !important;background:linear-gradient(90deg,#A3E635,#4ADE80) !important; width: {{ $budgetPct }}%"></div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div class="rounded-2xl bg-[var(--bento-card)]/20 p-3"><p class="text-[var(--bento-text-muted)]">Total Anggaran</p><p class="mt-1 font-bold text-[var(--bento-text)] tabular-nums">{{ $formatRupiah($currentMonthBudgetInsight['totalBudget']) }}</p></div>
                    <div class="rounded-2xl bg-[var(--bento-card)]/20 p-3"><p class="text-[var(--bento-text-muted)]">Terpakai</p><p class="mt-1 font-bold text-[var(--bento-rose)] tabular-nums">{{ $formatRupiah($currentMonthBudgetInsight['totalUsed']) }}</p></div>
                    <div class="rounded-2xl bg-[var(--bento-card)]/20 p-3"><p class="text-[var(--bento-text-muted)]">Sisa</p><p class="mt-1 font-bold {{ $currentMonthBudgetInsight['totalRemaining'] >= 0 ? 'text-[var(--bento-teal)]' : 'text-[var(--bento-rose)]' }} tabular-nums">{{ $budgetRemainingLabel }}</p></div>
                    <div class="rounded-2xl bg-[var(--bento-card)]/20 p-3"><p class="text-[var(--bento-text-muted)]">Lewat Batas</p><p class="mt-1 font-bold text-[var(--bento-text)]">{{ $currentMonthBudgetInsight['overLimitCount'] }} kategori</p></div>
                </div>
            @endif
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between gap-3 px-1">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--bento-text-muted)]" style="letter-spacing:0.06em;font-size:10px;">Aktivitas</p>
                    <h2 class="mt-1 text-base font-semibold text-[var(--bento-text)]">Transaksi Terakhir</h2>
                </div>
                <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-[var(--bento-purple)]">Semua</a>
            </div>
            @if ($latestTransactions->isEmpty())
                <div class="rounded-[26px] border border-[var(--bento-border)] bg-[var(--bento-card)] p-8 text-center" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 4px 24px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;">
                    <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--bento-card-2)] text-[var(--bento-text-muted)]"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h10M8 12h10M8 18h10"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h.01M4 12h.01M4 18h.01"/></svg></div>
                    <p class="mt-2 text-sm font-semibold text-[var(--bento-text)]">Belum ada transaksi</p>
                    <p class="mt-1 text-xs text-[var(--bento-text-muted)]">Tambah transaksi pertamamu sekarang</p>
                    <a href="{{ route('transactions.create') }}" class="mt-4 inline-flex rounded-full bg-[var(--bento-purple)] px-4 py-2 text-xs font-bold text-white">+ Tambah Transaksi</a>
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach ($latestTransactions->take(5) as $transaction)
                        @php
                            $isIncome = $transaction->type === \App\Models\Transaction::TYPE_INCOME;
                            $amountPrefix = $isIncome ? '+' : '-';
                        @endphp
                        <a href="{{ route('transactions.edit', $transaction) }}" class="block rounded-[24px] border border-[var(--bento-border)] bg-[var(--bento-card)] px-4 py-3.5 transition active:scale-[0.99]">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $isIncome ? 'bg-[var(--bento-teal)]' : 'bg-[var(--bento-rose)]' }}"></span>
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-lg {{ $isIncome ? 'bg-[var(--bento-card-2)] text-[var(--bento-teal)]' : 'bg-[var(--bento-card-2)] text-[var(--bento-rose)]' }}">{{ $transaction->category?->icon ?: '💸' }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-[var(--bento-text)]">{{ $transaction->note ?: $transaction->category?->name ?? 'Tanpa catatan' }}</p>
                                    <p class="mt-0.5 truncate text-xs text-[var(--bento-text-muted)]">{{ $transaction->category?->name ?? 'Tanpa kategori' }} · {{ $transaction->transaction_date->format('d M Y') }}</p>
                                </div>
                                <p class="shrink-0 text-sm font-bold tabular-nums {{ $isIncome ? 'text-[var(--bento-teal)]' : 'text-[var(--bento-rose)]' }}">{{ $amountPrefix }} {{ $formatRupiah($transaction->amount) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
    <div class="hidden lg:block px-4 pt-5 pb-28 lg:px-8 lg:py-8 xl:px-10">
        <div class="mx-auto max-w-7xl space-y-5 lg:space-y-6">
            <section class="bento-animate shadow-[0_24px_54px_rgba(79,70,229,0.24)]" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 4px 24px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;color:#FFFFFF !important;">
                <div class="grid gap-4 border-b border-[var(--bento-border)] pb-5 sm:grid-cols-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-400/10 text-amber-400">●</span>
                        <div>
                            <p class="text-[11px] font-medium text-white">Poin</p>
                            <p class="text-sm font-semibold text-white tabular-nums">{{ $pointsTotal }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 sm:border-x sm:border-[var(--bento-border)] sm:px-5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-400/10 text-amber-400">🔥</span>
                        <div>
                            <p class="text-[11px] font-medium text-white">Streak</p>
                            <p class="text-sm font-semibold text-white">{{ $streakTotal }} hari</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[var(--bento-card)] text-xs font-bold text-[var(--bento-purple)]">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                        <div>
                            <p class="text-[11px] font-medium text-white">Level</p>
                            <p class="text-sm font-semibold text-white">Lv {{ $levelTotal }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 text-center lg:py-8">
                    <p class="font-medium uppercase" style="color:#94A3B8 !important;font-size:0.75rem;letter-spacing:0.1em;">Saldo Saat Ini</p>
                    <div x-data="{ displayed: 0, target: {{ $balanceRaw }}, init() { if (this.target === 0) return; const duration = 800; const start = performance.now(); const update = (now) => { const elapsed = now - start; const progress = Math.min(elapsed / duration, 1); const ease = 1 - Math.pow(1 - progress, 3); this.displayed = Math.round(this.target * ease); if (progress < 1) requestAnimationFrame(update); }; requestAnimationFrame(update); } }" x-init="init()">
                        <p class="font-display mt-2 leading-none tracking-[-0.04em] tabular-nums" style="color: #FFFFFF !important; font-weight: 900; font-size: clamp(3rem, 6vw, 4.75rem); letter-spacing:-0.02em;">
                            Rp <span x-text="displayed.toLocaleString('id-ID')">{{ number_format($balanceRaw, 0, ',', '.') }}</span>
                        </p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 rounded-2xl bg-[var(--bento-card)]/20 p-3 sm:grid-cols-3 lg:mt-0 lg:p-4">
                    <div class="rounded-xl p-2">
                        <p class="text-[11px] font-medium text-white">Pemasukan</p>
                        <p class="mt-1 text-base font-semibold text-[var(--bento-teal)] tabular-nums">+ {{ $formatRupiah($thisMonthIncome) }}</p>
                    </div>
                    <div class="rounded-xl p-2 sm:border-x sm:border-[var(--bento-border)] sm:px-4">
                        <p class="text-[11px] font-medium text-white">Pengeluaran</p>
                        <p class="mt-1 text-base font-semibold text-[var(--bento-rose)] tabular-nums">- {{ $formatRupiah($thisMonthExpense) }}</p>
                    </div>
                    <div class="rounded-xl p-2">
                        <p class="text-[11px] font-medium text-white">Anggaran</p>
                        <p class="mt-1 text-base font-semibold text-[var(--bento-amber)] tabular-nums">{{ $budgetPct }}% terpakai</p>
                    </div>
                </div>
            </section>

            <div class="grid gap-5 xl:grid-cols-[minmax(0,1.35fr)_minmax(360px,0.65fr)] xl:gap-6">
                <div class="space-y-5 lg:space-y-6">
                    @if(isset($latestTransactions) && $latestTransactions->count() > 0)
                        @php
                            $categoryTotals = $latestTransactions
                                ->where('type', 'expense')
                                ->groupBy(fn($t) => $t->category?->name ?? 'Lainnya')
                                ->map(fn($g) => $g->sum('amount'));
                            $categoryTotalSum = (float) $categoryTotals->sum();
                            $safeCategoryTotal = max(1, $categoryTotalSum);
                            $last7 = collect(range(6,0))->map(fn($i) => now()->subDays($i));
                            $dailyExpense = $last7->map(fn($date) => $latestTransactions
                                ->where('type', 'expense')
                                ->filter(fn($t) => $t->transaction_date->format('Y-m-d') === $date->format('Y-m-d'))
                                ->sum('amount'));
                            $sevenDayExpenseTotal = (float) $dailyExpense->sum();
                            $dayLabels = $last7->map(fn($d) => $d->format('d M'));
                        @endphp
                        <section class="bento-animate grid gap-5 lg:grid-cols-2 lg:gap-6">
                            <div class="rounded-[28px] border border-[var(--bento-border)] bg-[var(--bento-card)] p-5 shadow-[0_14px_34px_rgba(99,102,241,0.08)] lg:p-6" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 4px 24px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;">
                                <div class="mb-5 flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--bento-text-muted)]">Pengeluaran</p>
                                        <h2 class="mt-1 text-lg font-semibold text-[var(--bento-text)]">Per Kategori</h2>
                                    </div>
                                    <p class="rounded-full bg-[var(--bento-rose)]/10 px-3 py-1 text-xs font-semibold text-[var(--bento-rose)] tabular-nums">{{ $formatRupiah($categoryTotalSum) }}</p>
                                </div>

                                @if($categoryTotals->isEmpty() || $categoryTotalSum <= 0)
                                    <div class="flex min-h-[220px] flex-col items-center justify-center rounded-2xl bg-[var(--bento-card)]/70 text-center">
                                        <p class="text-3xl">📊</p>
                                        <p class="mt-2 text-sm font-medium text-[var(--bento-text)]">Belum ada pengeluaran</p>
                                        <p class="mt-1 text-xs text-[var(--bento-text-muted)]">Grafik akan muncul setelah ada transaksi pengeluaran.</p>
                                    </div>
                                @else
                                    <div class="grid items-center gap-5 sm:grid-cols-[180px_1fr]">
                                        <div class="relative mx-auto h-[170px] w-[170px]">
                                            <canvas id="expenseDonut" height="170" width="170"></canvas>
                                            <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
                                                <p class="text-[10px] uppercase tracking-[0.12em] text-[var(--bento-text-muted)]">Total</p>
                                                <p class="mt-1 text-sm font-semibold text-[var(--bento-text)] tabular-nums">{{ $formatRupiah($categoryTotalSum) }}</p>
                                            </div>
                                        </div>
                                        <div class="space-y-3">
                                            @foreach($categoryTotals->take(4) as $categoryName => $total)
                                                <div class="flex items-center justify-between gap-3 rounded-2xl bg-[var(--bento-card)]/70 px-3 py-2.5">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-sm font-medium text-[var(--bento-text)]">{{ $categoryName }}</p>
                                                        <p class="text-xs text-[var(--bento-text-dim)] tabular-nums">{{ $formatRupiah($total) }}</p>
                                                    </div>
                                                    <span class="shrink-0 text-sm font-semibold text-[var(--bento-purple)] tabular-nums">{{ round(((float) $total / $safeCategoryTotal) * 100) }}%</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-[28px] border border-[var(--bento-border)] bg-[var(--bento-card)] p-5 shadow-[0_14px_34px_rgba(99,102,241,0.08)] lg:p-6" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 4px 24px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;">
                                <div class="mb-5 flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--bento-text-muted)]">7 Hari Terakhir</p>
                                        <h2 class="mt-1 text-lg font-semibold text-[var(--bento-text)]">Tren Pengeluaran</h2>
                                    </div>
                                    <p class="rounded-full bg-[var(--bento-card)]/20 px-3 py-1 text-xs font-semibold text-[var(--bento-purple)] tabular-nums">{{ $formatRupiah($sevenDayExpenseTotal) }}</p>
                                </div>

                                @if($sevenDayExpenseTotal <= 0)
                                    <div class="flex min-h-[220px] flex-col items-center justify-center rounded-2xl bg-[var(--bento-card)]/70 text-center">
                                        <p class="text-3xl">🌙</p>
                                        <p class="mt-2 text-sm font-medium text-[var(--bento-text)]">Belum ada pengeluaran 7 hari terakhir</p>
                                        <p class="mt-1 text-xs text-[var(--bento-text-muted)]">Chart akan terisi otomatis saat transaksi baru dicatat.</p>
                                    </div>
                                @else
                                    <div class="h-[240px]">
                                        <canvas id="weeklyBar" height="220"></canvas>
                                    </div>
                                @endif
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="space-y-5 lg:space-y-6">
                    <section class="bento-animate rounded-[28px] border border-[var(--bento-border)] bg-[var(--bento-card)] p-5 shadow-[0_14px_34px_rgba(99,102,241,0.08)] lg:p-6" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 4px 24px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;">
                        <div class="mb-5 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--bento-text-muted)]">Ringkasan Bulan Ini</p>
                                <h2 class="mt-1 text-lg font-semibold text-[var(--bento-text)]">Kondisi Keuangan</h2>
                            </div>
                            <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-[var(--bento-purple)] hover:text-[var(--bento-purple)]">Laporan →</a>
                        </div>

                        @php
                            $budgetRemainingLabel = $currentMonthBudgetInsight['totalRemaining'] >= 0
                                ? $formatRupiah($currentMonthBudgetInsight['totalRemaining'])
                                : 'Lewat '.$formatRupiah(abs($currentMonthBudgetInsight['totalRemaining']));
                        @endphp

                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-[var(--bento-card)]/20 p-4">
                                    <p class="text-xs text-[var(--bento-text-muted)]">Pemasukan</p>
                                    <p class="mt-1 font-display text-xl tabular-nums" style="color:#A3E635 !important;">{{ $formatRupiah($thisMonthIncome) }}</p>
                                </div>
                                <div class="rounded-2xl bg-[var(--bento-card)]/20 p-4">
                                    <p class="text-xs text-[var(--bento-text-muted)]">Pengeluaran</p>
                                    <p class="mt-1 font-display text-xl text-[var(--bento-rose)] tabular-nums">{{ $formatRupiah($thisMonthExpense) }}</p>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-[var(--bento-card)]/20 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs text-[var(--bento-text-muted)]">Ringkasan Anggaran</p>
                                        @if (! $currentMonthBudgetInsight['hasBudgets'])
                                            <p class="mt-1 text-sm text-[var(--bento-text)]">Belum ada anggaran bulan ini.</p>
                                            <p class="mt-1 text-xs text-[var(--bento-text-muted)]">Buat anggaran bulan ini untuk mulai memantau batas pengeluaran.</p>
                                        @else
                                            <p class="mt-1 text-sm text-[var(--bento-text-muted)]">Sisa</p>
                                            <p class="font-display text-2xl {{ $currentMonthBudgetInsight['totalRemaining'] >= 0 ? 'text-[var(--bento-teal)]' : 'text-[var(--bento-rose)]' }} tabular-nums">{{ $budgetRemainingLabel }}</p>
                                        @endif
                                    </div>
                                    <a href="{{ route('budgets.index') }}" aria-label="Buat anggaran bulan ini" class="shrink-0 rounded-xl bg-[var(--bento-purple)]/10 px-3 py-2 text-xs font-semibold text-[var(--bento-purple)] hover:bg-[var(--bento-purple)] hover:text-white">Lihat anggaran</a>
                                </div>
                                @if (! $currentMonthBudgetInsight['hasBudgets'])
                                    <a href="{{ route('budgets.index') }}" aria-label="Buat anggaran bulan ini" class="mt-3 inline-flex rounded-xl bg-[var(--bento-purple)] px-3 py-2 text-xs font-bold text-white">Buat Anggaran</a>
                                @else
                                    <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                                        <div><p class="text-[var(--bento-text-muted)]">Total Anggaran</p><p class="mt-1 font-semibold text-[var(--bento-text)] tabular-nums">{{ $formatRupiah($currentMonthBudgetInsight['totalBudget']) }}</p></div>
                                        <div><p class="text-[var(--bento-text-muted)]">Terpakai</p><p class="mt-1 font-semibold text-[var(--bento-rose)] tabular-nums">{{ $formatRupiah($currentMonthBudgetInsight['totalUsed']) }}</p></div>
                                        <div><p class="text-[var(--bento-text-muted)]">Sisa</p><p class="mt-1 font-semibold {{ $currentMonthBudgetInsight['totalRemaining'] >= 0 ? 'text-[var(--bento-teal)]' : 'text-[var(--bento-rose)]' }} tabular-nums">{{ $budgetRemainingLabel }}</p></div>
                                        <div><p class="text-[var(--bento-text-muted)]">Lewat Batas</p><p class="mt-1 font-semibold text-[var(--bento-text)]">{{ $currentMonthBudgetInsight['overLimitCount'] }} kategori</p></div>
                                    </div>
                                    <div class="mt-4" style="height:8px !important;border-radius:999px !important;background:rgba(255,255,255,0.1) !important;">
                                        <div class="" style="height:8px !important;border-radius:999px !important;background:linear-gradient(90deg,#A3E635,#4ADE80) !important; width: {{ $budgetPct }}%"></div>
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-2xl bg-[var(--bento-card)]/20 p-4">
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <p class="text-xs text-[var(--bento-text-muted)]">Target Tabungan</p>
                                    <a href="{{ route('savings.index') }}" class="text-xs font-semibold text-[var(--bento-purple)] hover:text-[var(--bento-purple)]">Lihat tabungan</a>
                                </div>
                                @if (! $savingsGoalInsight['hasGoals'])
                                    <p class="text-sm text-[var(--bento-text)]">Belum ada target tabungan aktif.</p>
                                    <p class="mt-1 text-xs text-[var(--bento-text-muted)]">Buat target untuk mulai memantau progres tabunganmu.</p>
                                @else
                                    <div class="flex items-center gap-4">
                                        <svg width="56" height="56" viewBox="0 0 52 52" class="shrink-0">
                                            <circle cx="26" cy="26" r="20" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="4" />
                                            <circle cx="26" cy="26" r="20" fill="none" stroke="#A3E635" stroke-width="4" stroke-linecap="round" stroke-dasharray="125.6" stroke-dashoffset="{{ $savingOffset }}" transform="rotate(-90 26 26)" />
                                        </svg>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-[var(--bento-text)]">{{ $primarySaving['goal']->name ?? 'Target tabungan' }}</p>
                                            <p class="font-display text-3xl font-bold text-[var(--bento-teal)]">{{ $savingProgress }}%</p>
                                            <p class="text-xs text-[var(--bento-text-muted)]">Progress</p>
                                        </div>
                                    </div>
                                    <div class="mt-3" style="height:8px !important;border-radius:999px !important;background:rgba(255,255,255,0.1) !important;" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $savingsGoalInsight['barPercentage'] }}" aria-label="Progress total target tabungan">
                                        <div class="" style="height:8px !important;border-radius:999px !important;background:linear-gradient(90deg,#A3E635,#4ADE80) !important;width: {{ $savingsGoalInsight['barPercentage'] }}%;"></div>
                                    </div>
                                    <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                                        <div><p class="text-[var(--bento-text-muted)]">Terkumpul</p><p class="mt-1 font-semibold text-[var(--bento-text)] tabular-nums">{{ $formatRupiah($savingsGoalInsight['totalSavingsCurrent']) }}</p></div>
                                        <div><p class="text-[var(--bento-text-muted)]">Total Target</p><p class="mt-1 font-semibold text-[var(--bento-text)] tabular-nums">{{ $formatRupiah($savingsGoalInsight['totalSavingsTarget']) }}</p></div>
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        @foreach ($savingsGoalInsight['topSavingsGoals'] as $item)
                                            <div class="flex items-center justify-between gap-2 rounded-xl bg-[var(--bento-card)]/70 px-3 py-2 text-xs">
                                                <span class="truncate text-[var(--bento-text)]">{{ $item['goal']->name }}</span>
                                                <span class="shrink-0 text-[var(--bento-text-dim)] tabular-nums">{{ $item['progressPercentage'] }}%</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between rounded-2xl border border-[var(--bento-lime)]/15 bg-[var(--bento-card)]/10 p-4">
                                <div>
                                    <p class="text-xs text-[var(--bento-text-muted)]">Aktivitas</p>
                                    <p class="mt-1 text-sm font-semibold text-[var(--bento-text)]">{{ $streakTotal }} hari berturut-turut</p>
                                </div>
                                <p class="font-display text-3xl font-bold text-[var(--bento-teal)] tabular-nums">{{ $pointsTotal }}</p>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>

            <section class="bento-animate rounded-[28px] border border-[var(--bento-border)] bg-[var(--bento-card)] p-5 shadow-[0_14px_34px_rgba(99,102,241,0.08)] lg:p-6" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;box-shadow:0 0 0 1px rgba(163,230,53,0.05),0 4px 24px rgba(0,0,0,0.4) !important;border-radius:28px !important;padding:20px 20px !important;margin-bottom:20px !important;">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--bento-text-muted)]">Riwayat</p>
                        <h2 class="mt-1 text-lg font-semibold text-[var(--bento-text)]">Transaksi Terakhir</h2>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="text-xs font-semibold text-[var(--bento-purple)] hover:text-[var(--bento-purple)]">Lihat semua →</a>
                </div>

                @if ($latestTransactions->isEmpty())
                    <div class="rounded-2xl bg-[var(--bento-card)]/70 py-10 text-center">
                        <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--bento-card-2)] text-[var(--bento-text-muted)]"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h10M8 12h10M8 18h10"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h.01M4 12h.01M4 18h.01"/></svg></div>
                        <p class="mt-2 text-sm font-medium text-[var(--bento-text)]">Belum ada transaksi</p>
                        <p class="mt-1 text-xs text-[var(--bento-text-muted)]">Tambah transaksi pertamamu sekarang</p>
                        <a href="{{ route('transactions.create') }}" class="mt-4 inline-flex rounded-xl bg-[var(--bento-purple)] px-4 py-2 text-xs font-bold text-white">+ Tambah Transaksi</a>
                    </div>
                @else
                    <div class="overflow-hidden rounded-2xl border border-[var(--bento-border)]">
                        <div class="hidden grid-cols-[44px_minmax(0,1.5fr)_minmax(120px,0.8fr)_120px_150px] gap-4 border-b border-[var(--bento-border)] bg-[var(--bento-card)]/20 px-4 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-[var(--bento-text-muted)] lg:grid">
                            <span></span>
                            <span>Transaksi</span>
                            <span>Kategori</span>
                            <span>Tanggal</span>
                            <span class="text-right">Nominal</span>
                        </div>
                        @foreach ($latestTransactions as $transaction)
                            @php
                                $isIncome = $transaction->type === \App\Models\Transaction::TYPE_INCOME;
                                $amountPrefix = $isIncome ? '+' : '-';
                            @endphp
                            <a href="{{ route('transactions.edit', $transaction) }}" class="grid gap-3 border-b border-[var(--bento-border)] px-4 py-3 transition hover:bg-[var(--bento-card)]/70 last:border-b-0 lg:grid-cols-[44px_minmax(0,1.5fr)_minmax(120px,0.8fr)_120px_150px] lg:items-center lg:gap-4">
                                <span class="relative flex h-10 w-10 items-center justify-center rounded-xl text-lg {{ $isIncome ? 'bg-[var(--bento-card-2)] text-[var(--bento-teal)]' : 'bg-[var(--bento-card-2)] text-[var(--bento-rose)]' }}"><span class="absolute -left-1 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rounded-full {{ $isIncome ? 'bg-[var(--bento-teal)]' : 'bg-[var(--bento-rose)]' }}"></span>{{ $transaction->category?->icon ?: '💸' }}</span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-[var(--bento-text)]">{{ $transaction->note ?: $transaction->category?->name ?? 'Tanpa catatan' }}</p>
                                    <p class="mt-0.5 text-xs text-[var(--bento-text-muted)] lg:hidden">{{ $transaction->category?->name ?? 'Tanpa kategori' }} · {{ $transaction->transaction_date->format('d M Y') }}</p>
                                </div>
                                <p class="hidden truncate text-sm text-[var(--bento-text-muted)] lg:block">{{ $transaction->category?->name ?? 'Tanpa kategori' }}</p>
                                <p class="hidden text-sm text-[var(--bento-text-muted)] lg:block">{{ $transaction->transaction_date->format('d M Y') }}</p>
                                <p class="text-sm font-semibold tabular-nums {{ $isIncome ? 'text-[var(--bento-teal)]' : 'text-[var(--bento-rose)]' }} lg:text-right">{{ $amountPrefix }} {{ $formatRupiah($transaction->amount) }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>

    @if(isset($latestTransactions) && $latestTransactions->count() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Chart === 'undefined') return;

                Chart.defaults.color = '#94A3B8';
                Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

                const colors = ['#A3E635','#FCD34D','#F87171','#60A5FA'];
                const donutEl = document.getElementById('expenseDonut');
                const barEl = document.getElementById('weeklyBar');

                if (donutEl) {
                    new Chart(donutEl, {
                        type: 'doughnut',
                        data: {
                            labels: @json($categoryTotals->keys()->values()),
                            datasets: [{ data: @json($categoryTotals->values()->values()), backgroundColor: colors, borderWidth: 0, hoverOffset: 3 }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '74%',
                            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => `${ctx.label}: Rp ${Number(ctx.raw).toLocaleString('id-ID')}` } } }
                        }
                    });
                }

                if (barEl) {
                    new Chart(barEl, {
                        type: 'bar',
                        data: {
                            labels: @json($dayLabels->values()),
                            datasets: [{ data: @json($dailyExpense->values()), backgroundColor: '#A3E635', borderRadius: 8, maxBarThickness: 34 }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => `Rp ${Number(ctx.raw).toLocaleString('id-ID')}` } } },
                            scales: {
                                x: { grid: { display: false }, ticks: { color: '#94A3B8', maxRotation: 0, minRotation: 0, font: { size: 11 } } },
                                y: { display: false, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#94A3B8' } }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
