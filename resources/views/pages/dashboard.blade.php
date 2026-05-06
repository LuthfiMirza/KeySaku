<x-app-layout>
    @php
        $formatRupiah = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
    @endphp
    <x-slot:title>Beranda</x-slot:title>

    <div class="px-4 pt-6 space-y-6 pb-4">

        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
            <p class="text-xs font-medium text-slate-400">Akun aktif</p>
            <p class="mt-1 text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
            <p class="text-xs text-slate-400">{{ auth()->user()->email }}</p>
        </div>

        {{-- Balance Overview Card --}}
        <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-3xl p-5 text-white shadow-xl shadow-indigo-500/30 relative overflow-hidden">
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
                <h2 class="text-sm font-bold text-slate-700">Ringkasan Bulan Ini</h2>
                <a href="{{ route('reports.index') }}" class="text-xs text-indigo-500 font-medium">Buka laporan bulanan →</a>
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
                <h2 class="text-sm font-bold text-slate-700">Ringkasan Anggaran</h2>
                <a href="{{ route('budgets.index') }}" aria-label="Lihat halaman anggaran" class="inline-flex min-h-9 items-center text-xs text-indigo-500 font-medium">Lihat anggaran →</a>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
                @if (! $currentMonthBudgetInsight['hasBudgets'])
                    <p class="text-sm font-medium text-slate-500">Belum ada anggaran bulan ini.</p>
                    <p class="mt-1 text-xs text-slate-400">Buat anggaran bulan ini untuk mulai memantau batas pengeluaran.</p>
                    <a href="{{ route('budgets.index') }}" aria-label="Buat anggaran bulan ini" class="mt-3 inline-flex min-h-11 items-center rounded-2xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-600">Buat Anggaran</a>
                @else
                    @php
                        $budgetRemainingLabel = $currentMonthBudgetInsight['totalRemaining'] >= 0
                            ? $formatRupiah($currentMonthBudgetInsight['totalRemaining'])
                            : 'Lewat '.$formatRupiah(abs($currentMonthBudgetInsight['totalRemaining']));
                    @endphp

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <p class="text-slate-400">Total Anggaran</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-800 tabular-nums">{{ $formatRupiah($currentMonthBudgetInsight['totalBudget']) }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Terpakai</p>
                            <p class="mt-1 text-sm font-extrabold text-rose-600 tabular-nums">{{ $formatRupiah($currentMonthBudgetInsight['totalUsed']) }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Sisa</p>
                            <p class="mt-1 text-sm font-extrabold tabular-nums {{ $currentMonthBudgetInsight['totalRemaining'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $budgetRemainingLabel }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Lewat Batas</p>
                            <p class="mt-1 text-sm font-extrabold text-slate-800">{{ $currentMonthBudgetInsight['overLimitCount'] }} kategori</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Pengeluaran per Kategori --}}
        <section>
            <h2 class="text-sm font-bold text-slate-700 mb-3">Pengeluaran per Kategori</h2>

            @if ($monthlyExpenseByCategory->isEmpty() || (float) $thisMonthExpense <= 0)
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60 text-center py-8">
                    <p class="text-3xl mb-2">📊</p>
                    <p class="text-sm text-slate-500">Belum ada pengeluaran bulan ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Catat transaksi pertamamu untuk melihat pola pengeluaran.</p>
                </div>
            @else
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60 space-y-4">
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
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $category?->name ?? 'Tanpa kategori' }}</p>
                                        <p class="text-xs text-slate-400">{{ $percentage }}% dari pengeluaran</p>
                                    </div>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold text-slate-700 tabular-nums">{{ $formatRupiah($item['total']) }}</p>
                            </div>
                            <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-2 rounded-full bg-gradient-to-r from-rose-400 to-pink-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Aksi Cepat --}}
        <section>
            <h2 class="text-sm font-bold text-slate-700 mb-3">Aksi Cepat</h2>
            <div class="grid grid-cols-4 gap-2">
                <x-quick-action label="Tambah Manual" icon="✏️" color="indigo" href="{{ route('transactions.create') }}" />
                <x-quick-action label="Input Suara" icon="🎙️" color="purple" href="{{ route('transactions.voice') }}" />
                <x-quick-action label="Anggaran" icon="📊" color="emerald" href="{{ route('budgets.index') }}" />
                <x-quick-action label="Tabungan" icon="🎯" color="amber" href="{{ route('goals') }}" />
            </div>
        </section>

        {{-- Target Tabungan --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-slate-700">Target Tabungan</h2>
                <a href="{{ route('goals') }}" class="text-xs text-indigo-500 font-medium">Lihat semua →</a>
            </div>
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🏠</span>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Dana Darurat</p>
                            <p class="text-xs text-slate-400">Target: Rp 10.000.000</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-indigo-600">42%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full" style="width: 42%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-2">Terkumpul: Rp 4.200.000</p>
            </div>
        </section>

        {{-- Insight --}}
        <section>
            <h2 class="text-sm font-bold text-slate-700 mb-3">Insight Keuangan</h2>
            <div class="space-y-3">
                <x-insight-card
                    title="Pengeluaran makanan naik 18%"
                    description="Coba kurangi makan di luar untuk menghemat lebih banyak bulan ini."
                    icon="🍜"
                    type="warning"
                />
                <x-insight-card
                    title="Anggaran transportasi aman"
                    description="Kamu masih dalam batas anggaran transportasi bulan ini. Bagus!"
                    icon="🚗"
                    type="success"
                />
            </div>
        </section>

        {{-- Transaksi Terakhir --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-slate-700">Transaksi Terakhir</h2>
                <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-500 font-medium">Lihat semua →</a>
            </div>

            @if ($latestTransactions->isEmpty())
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60 text-center py-8">
                    <p class="text-3xl mb-2">📋</p>
                    <p class="text-sm text-slate-500">Belum ada transaksi</p>
                    <p class="text-xs text-slate-400 mt-1">Tambah transaksi pertamamu sekarang</p>
                    <a href="{{ route('transactions.create') }}" class="mt-4 inline-flex rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
                        + Tambah Transaksi
                    </a>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($latestTransactions as $transaction)
                        @php
                            $isIncome = $transaction->type === \App\Models\Transaction::TYPE_INCOME;
                            $amountPrefix = $isIncome ? '+' : '-';
                            $amountClass = $isIncome ? 'text-emerald-600' : 'text-rose-600';
                            $badgeClass = $isIncome ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600';
                            $typeLabel = $isIncome ? 'Pemasukan' : 'Pengeluaran';
                        @endphp

                        <a href="{{ route('transactions.edit', $transaction) }}" class="block bg-white/80 backdrop-blur-sm rounded-2xl p-3 shadow-sm border border-white/60">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-lg" style="background-color: {{ $transaction->category?->color ?? '#f1f5f9' }}22; color: {{ $transaction->category?->color ?? '#64748b' }};">
                                        {{ $transaction->category?->icon ?: '💸' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $transaction->category?->name ?? 'Tanpa kategori' }}</p>
                                        <p class="truncate text-xs text-slate-400">{{ $transaction->note ?: $transaction->transaction_date->format('d M Y') }}</p>
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
