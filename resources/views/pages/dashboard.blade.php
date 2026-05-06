<x-app-layout>
    @php
        $formatRupiah = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
    @endphp
    <x-slot:title>Beranda</x-slot:title>

    <div class="px-4 pt-6 space-y-6 pb-4">

        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 text-sm font-extrabold text-white shadow-lg shadow-indigo-900/30">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-white/50">Akun aktif</p>
                <p class="mt-1 truncate text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-white/40">{{ auth()->user()->email }}</p>
            </div>
        </div>

        {{-- Balance Overview Card --}}
        <div class="bg-gradient-to-br from-indigo-500/30 via-purple-600/20 to-pink-500/20 rounded-3xl p-5 text-white shadow-xl shadow-indigo-900/40 border border-white/20 relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent"></div>
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-12 translate-x-12 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-10 -translate-x-8 blur-xl"></div>
            <div class="relative">
                <p class="text-sm font-medium text-white/70">Total Saldo</p>
                <p class="text-4xl font-bold mt-1 tabular-nums">{{ $formatRupiah($netBalance) }}</p>
                <p class="text-xs text-white/60 mt-2">Saldo bersih dari semua transaksi</p>
                <div class="flex gap-4 mt-4 pt-4 border-t border-white/20">
                    <div>
                        <p class="text-xs text-white/60">Pemasukan</p>
                        <p class="text-sm font-semibold tabular-nums">{{ $formatRupiah($thisMonthIncome) }}</p>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div>
                        <p class="text-xs text-white/60">Pengeluaran</p>
                        <p class="text-sm font-semibold tabular-nums">{{ $formatRupiah($thisMonthExpense) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Bulan Ini --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-white">Ringkasan Bulan Ini</h2>
                <a href="{{ route('reports.index') }}" class="text-xs text-indigo-300 hover:text-indigo-200 font-medium">Buka laporan bulanan →</a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <x-dashboard-card
                    label="Pemasukan"
                    value="{{ $formatRupiah($thisMonthIncome) }}"
                    color="emerald"
                />
                <x-dashboard-card
                    label="Pengeluaran"
                    value="{{ $formatRupiah($thisMonthExpense) }}"
                    color="rose"
                />
            </div>
        </section>

        {{-- Insight Anggaran --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-white">Ringkasan Anggaran</h2>
                <a href="{{ route('budgets.index') }}" aria-label="Lihat halaman anggaran" class="inline-flex min-h-9 items-center text-xs text-indigo-300 hover:text-indigo-200 font-medium">Lihat anggaran →</a>
            </div>

            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/15">
                @if (! $currentMonthBudgetInsight['hasBudgets'])
                    <p class="text-sm font-medium text-white/60">Belum ada anggaran bulan ini.</p>
                    <p class="mt-1 text-xs text-white/40">Buat anggaran bulan ini untuk mulai memantau batas pengeluaran.</p>
                    <a href="{{ route('budgets.index') }}" aria-label="Buat anggaran bulan ini" class="mt-3 inline-flex min-h-11 items-center rounded-2xl bg-indigo-500/30 border border-indigo-400/30 px-3 py-2 text-xs font-bold text-indigo-300">Buat Anggaran</a>
                @else
                    @php
                        $budgetRemainingLabel = $currentMonthBudgetInsight['totalRemaining'] >= 0
                            ? $formatRupiah($currentMonthBudgetInsight['totalRemaining'])
                            : 'Lewat '.$formatRupiah(abs($currentMonthBudgetInsight['totalRemaining']));
                    @endphp

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <p class="text-white/40">Total Anggaran</p>
                            <p class="mt-1 text-sm font-extrabold text-white tabular-nums">{{ $formatRupiah($currentMonthBudgetInsight['totalBudget']) }}</p>
                        </div>
                        <div>
                            <p class="text-white/40">Terpakai</p>
                            <p class="mt-1 text-sm font-extrabold text-rose-400 tabular-nums">{{ $formatRupiah($currentMonthBudgetInsight['totalUsed']) }}</p>
                        </div>
                        <div>
                            <p class="text-white/40">Sisa</p>
                            <p class="mt-1 text-sm font-extrabold tabular-nums {{ $currentMonthBudgetInsight['totalRemaining'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">{{ $budgetRemainingLabel }}</p>
                        </div>
                        <div>
                            <p class="text-white/40">Lewat Batas</p>
                            <p class="mt-1 text-sm font-extrabold text-white">{{ $currentMonthBudgetInsight['overLimitCount'] }} kategori</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Pengeluaran per Kategori --}}
        <section>
            <h2 class="text-sm font-bold text-white mb-3">Pengeluaran per Kategori</h2>

            @if ($monthlyExpenseByCategory->isEmpty() || (float) $thisMonthExpense <= 0)
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/15 text-center py-8">
                    <p class="text-3xl mb-2">📊</p>
                    <p class="text-sm text-white/60">Belum ada pengeluaran bulan ini.</p>
                    <p class="text-xs text-white/40 mt-1">Catat transaksi pertamamu untuk melihat pola pengeluaran.</p>
                </div>
            @else
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/15 space-y-4">
                    @foreach ($monthlyExpenseByCategory as $item)
                        @php
                            $category = $item['category'];
                            $percentage = min(100, max(0, $item['percentage']));
                        @endphp

                        <div>
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl text-base" style="background-color: {{ $category?->color ?? '#f1f5f9' }}22; color: {{ $category?->color ?? '#64748b' }};">
                                        {{ $category?->icon ?: '💸' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-white">{{ $category?->name ?? 'Tanpa kategori' }}</p>
                                        <p class="text-xs text-white/40">{{ $percentage }}% dari pengeluaran</p>
                                    </div>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold text-white tabular-nums">{{ $formatRupiah($item['total']) }}</p>
                            </div>
                            <div class="h-2 w-full rounded-full bg-white/10 overflow-hidden">
                                <div class="h-2 rounded-full bg-gradient-to-r from-rose-400 to-pink-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Aksi Cepat --}}
        <section>
            <h2 class="text-sm font-bold text-white mb-3">Aksi Cepat</h2>
            <div class="grid grid-cols-4 gap-2">
                <x-quick-action label="Tambah Manual" icon="✏️" color="indigo" href="{{ route('transactions.create') }}" />
                <x-quick-action label="Input Suara" icon="🎙️" color="purple" href="{{ route('transactions.voice') }}" />
                <x-quick-action label="Anggaran" icon="📊" color="emerald" href="{{ route('budgets.index') }}" />
                <x-quick-action label="Tabungan" icon="🎯" color="amber" href="{{ route('savings.index') }}" />
            </div>
        </section>

        {{-- Target Tabungan --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-white">Target Tabungan</h2>
                <a href="{{ route('savings.index') }}" aria-label="Lihat halaman tabungan" class="inline-flex min-h-9 items-center text-xs text-indigo-300 hover:text-indigo-200 font-medium">Lihat tabungan →</a>
            </div>

            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/15">
                @if (! $savingsGoalInsight['hasGoals'])
                    <p class="text-sm font-medium text-white/60">Belum ada target tabungan aktif.</p>
                    <p class="mt-1 text-xs text-white/40">Buat target untuk mulai memantau progres tabunganmu.</p>
                    <a href="{{ route('savings.index') }}" aria-label="Buat target tabungan" class="mt-3 inline-flex min-h-11 items-center rounded-2xl bg-indigo-500/30 border border-indigo-400/30 px-3 py-2 text-xs font-bold text-indigo-300">Lihat tabungan</a>
                @else
                    <div class="grid grid-cols-3 gap-3 text-xs">
                        <div>
                            <p class="text-white/40">Terkumpul</p>
                            <p class="mt-1 text-sm font-extrabold text-emerald-400 tabular-nums">{{ $formatRupiah($savingsGoalInsight['totalSavingsCurrent']) }}</p>
                        </div>
                        <div>
                            <p class="text-white/40">Total Target</p>
                            <p class="mt-1 text-sm font-extrabold text-white tabular-nums">{{ $formatRupiah($savingsGoalInsight['totalSavingsTarget']) }}</p>
                        </div>
                        <div>
                            <p class="text-white/40">Progress</p>
                            <p class="mt-1 text-sm font-extrabold text-indigo-300">{{ $savingsGoalInsight['savingsProgressPercentage'] }}%</p>
                        </div>
                    </div>

                    <div class="mt-4 h-2 rounded-full bg-white/10" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $savingsGoalInsight['barPercentage'] }}" aria-label="Progress total target tabungan">
                        <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $savingsGoalInsight['barPercentage'] }}%"></div>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach ($savingsGoalInsight['topSavingsGoals'] as $item)
                            <div class="rounded-2xl border border-white/10 bg-white/8 p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-white">{{ $item['goal']->name }}</p>
                                        <p class="mt-1 text-xs text-white/40 tabular-nums">{{ $formatRupiah($item['goal']->current_amount) }} dari {{ $formatRupiah($item['goal']->target_amount) }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-indigo-500/30 border border-indigo-400/30 px-2 py-1 text-xs font-bold text-indigo-300">{{ $item['progressPercentage'] }}%</span>
                                </div>
                                <div class="mt-2 h-1.5 rounded-full bg-white/10" aria-hidden="true">
                                    <div class="h-1.5 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $item['barPercentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Insight --}}
        <section>
            <h2 class="text-sm font-bold text-white mb-3">Insight Keuangan</h2>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/15">
                <p class="text-sm font-medium text-white/60">Insight otomatis belum tersedia.</p>
                <p class="mt-1 text-xs leading-relaxed text-white/40">Gunakan ringkasan transaksi, anggaran, dan tabungan di atas untuk memantau kondisi keuangan keluarga.</p>
            </div>
        </section>

        {{-- Transaksi Terakhir --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-white">Transaksi Terakhir</h2>
                <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-300 hover:text-indigo-200 font-medium">Lihat semua →</a>
            </div>

            @if ($latestTransactions->isEmpty())
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/15 text-center py-8">
                    <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-xl">📋</div>
                    <p class="text-sm text-white/60">Belum ada transaksi</p>
                    <p class="text-xs text-white/40 mt-1">Tambah transaksi pertamamu sekarang</p>
                    <a href="{{ route('transactions.create') }}" class="mt-4 inline-flex rounded-full bg-indigo-500/30 border border-indigo-400/30 px-4 py-2 text-xs font-bold text-indigo-300 shadow-lg shadow-indigo-500/20">
                        + Tambah Transaksi
                    </a>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($latestTransactions as $transaction)
                        @php
                            $isIncome = $transaction->type === \App\Models\Transaction::TYPE_INCOME;
                            $amountPrefix = $isIncome ? '+' : '-';
                            $amountClass = $isIncome ? 'text-emerald-400' : 'text-rose-400';
                            $badgeClass = $isIncome ? 'bg-emerald-400/15 text-emerald-400' : 'bg-rose-400/15 text-rose-400';
                            $typeLabel = $isIncome ? 'Pemasukan' : 'Pengeluaran';
                        @endphp

                        <a href="{{ route('transactions.edit', $transaction) }}" class="block bg-white/8 backdrop-blur-sm rounded-xl px-3.5 py-3 shadow-sm border border-white/10">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-lg" style="background-color: {{ $transaction->category?->color ?? '#f1f5f9' }}22; color: {{ $transaction->category?->color ?? '#64748b' }};">
                                        {{ $transaction->category?->icon ?: '💸' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-white">{{ $transaction->category?->name ?? 'Tanpa kategori' }}</p>
                                        <p class="truncate text-xs text-white/40">{{ $transaction->note ?: $transaction->transaction_date->format('d M Y') }}</p>
                                        <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold {{ $badgeClass }}">{{ $typeLabel }}</span>
                                    </div>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold tabular-nums {{ $amountClass }}">
                                    {{ $amountPrefix }} {{ $formatRupiah($transaction->amount) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

    </div>
</x-app-layout>
