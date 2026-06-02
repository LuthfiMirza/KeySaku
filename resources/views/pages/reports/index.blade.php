<x-app-layout>
    <x-slot:title>Laporan Bulanan</x-slot:title>

    @php
        $formatRupiah = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
        $balanceIsPositive = $monthlyBalance >= 0;
    @endphp

    <div class="px-4 pt-6 space-y-5 pb-4 lg:px-8 lg:pt-8 lg:pb-8 lg:max-w-5xl lg:mx-auto">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-[#0f0f5e]">Laporan Bulanan</h2>
                <p class="mt-1 text-xs text-[#8890b0]">Ringkasan pemasukan dan pengeluaran.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="shrink-0 rounded-full bg-white border border-[#dde3f5] px-3 py-2 text-xs font-bold text-[#8890b0]">
                Beranda
            </a>
        </div>

        <section class="bg-white rounded-3xl p-4 shadow-sm border border-[#dde3f5]">
            <form method="GET" action="{{ route('reports.index') }}" class="space-y-3">
                <div>
                    <label for="month" class="block text-xs font-bold uppercase tracking-wide text-[#8890b0]">Bulan Laporan</label>
                    <input id="month" name="month" type="month" value="{{ $selectedMonthValue }}" class="mt-2 block w-full rounded-2xl border-[#dde3f5] bg-white text-[#0f0f5e] [color-scheme:dark] px-4 py-3 text-sm shadow-sm focus:border-[#1a1aff] focus:ring-[#1a1aff]/20">
                </div>
                <button type="submit" class="w-full rounded-2xl bg-[#ff3d8b] px-4 py-3 text-sm font-bold text-[#0f0f5e] shadow-lg shadow-pink-500/20">
                    Tampilkan Laporan
                </button>
            </form>

            <div class="mt-4 flex items-center justify-between gap-3 text-xs font-bold">
                <a href="{{ route('reports.index', ['month' => $previousMonthValue]) }}" class="rounded-full bg-white border border-[#dde3f5] px-3 py-2 text-[#8890b0]">← Bulan Sebelumnya</a>
                <span class="text-center text-[#8890b0]">{{ $selectedMonthLabel }}</span>
                <a href="{{ route('reports.index', ['month' => $nextMonthValue]) }}" class="rounded-full bg-white border border-[#dde3f5] px-3 py-2 text-[#8890b0]">Bulan Berikutnya →</a>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-[#dde3f5]">
                <p class="text-xs font-bold uppercase tracking-wide text-[#1d9e75]">Pemasukan</p>
                <p class="mt-2 text-lg font-extrabold text-[#0f0f5e] tabular-nums">{{ $formatRupiah($totalIncome) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-[#dde3f5]">
                <p class="text-xs font-bold uppercase tracking-wide text-[#e24b4a]">Pengeluaran</p>
                <p class="mt-2 text-lg font-extrabold text-[#0f0f5e] tabular-nums">{{ $formatRupiah($totalExpense) }}</p>
            </div>
            <div class="col-span-2 relative overflow-hidden bg-white rounded-3xl p-5 shadow-xl shadow-pink-500/20 border border-[#dde3f5]">
                <div class="absolute inset-x-0 top-0 h-px bg-[#dde3f5]"></div>
                <p class="text-xs font-bold uppercase tracking-wide text-[#1a1aff]">Saldo Bulan Ini</p>
                <p class="mt-2 text-2xl font-extrabold tabular-nums {{ $balanceIsPositive ? 'text-[#1d9e75]' : 'text-[#e24b4a]' }}">
                    {{ $balanceIsPositive ? '+' : '-' }} {{ $formatRupiah(abs($monthlyBalance)) }}
                </p>
                <p class="mt-1 text-xs text-[#8890b0]">Pemasukan dikurangi pengeluaran pada {{ $selectedMonthLabel }}.</p>
            </div>
        </section>

        <section class="bg-white rounded-3xl p-5 shadow-sm border border-[#dde3f5]">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-[#0f0f5e]">Income vs Expense</h3>
                    <p class="mt-1 text-xs text-[#8890b0]">Perbandingan arus uang bulan ini.</p>
                </div>
                <span class="rounded-full bg-[#1a1aff]/10 border border-[#1a1aff]/20 px-3 py-1 text-xs font-bold text-[#1a1aff] hover:text-[#4d4dff]">{{ $selectedMonthLabel }}</span>
            </div>

            @if ($totalIncome <= 0 && $totalExpense <= 0)
                <div class="mt-4 rounded-2xl bg-white px-4 py-5 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-xl">📊</div>
                    <p class="mt-2 text-sm font-bold text-[#8890b0]">Belum ada data bulan ini.</p>
                    <p class="mt-1 text-xs text-[#8890b0]">Catat pemasukan atau pengeluaran untuk melihat laporan bulanan.</p>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <a href="{{ route('transactions.create') }}" class="rounded-full bg-[#1a1aff]/10 border border-[#1a1aff]/20 px-4 py-2 text-xs font-bold text-[#1a1aff]">Tambah Transaksi</a>
                        <a href="{{ route('transactions.voice') }}" class="rounded-2xl bg-white px-3 py-3 text-xs font-bold text-[#1a1aff] border border-[#dde3f5]">Input Suara</a>
                    </div>
                </div>
            @else
                @php
                    $totalFlow = $totalIncome + $totalExpense;
                    $incomePercentage = $totalFlow > 0 ? round(($totalIncome / $totalFlow) * 100) : 0;
                    $expensePercentage = $totalFlow > 0 ? round(($totalExpense / $totalFlow) * 100) : 0;
                @endphp

                <div class="mt-4 space-y-3">
                    <div>
                        <div class="mb-2 flex items-center justify-between text-xs font-bold">
                            <span class="text-[#1d9e75]">Pemasukan</span>
                            <span class="text-[#8890b0]">{{ $incomePercentage }}%</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-white">
                            <div class="h-full rounded-full bg-[#1d9e75]/100" style="width: {{ $incomePercentage }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between text-xs font-bold">
                            <span class="text-[#e24b4a]">Pengeluaran</span>
                            <span class="text-[#8890b0]">{{ $expensePercentage }}%</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-white">
                            <div class="h-full rounded-full bg-[#e24b4a]/100" style="width: {{ $expensePercentage }}%"></div>
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <section class="bg-white rounded-3xl p-5 shadow-sm border border-[#dde3f5]">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-[#0f0f5e]">Pengeluaran per Kategori</h3>
                    <p class="mt-1 text-xs text-[#8890b0]">Kategori pengeluaran terbesar bulan ini.</p>
                </div>
                <span class="rounded-full bg-[#e24b4a]/10 px-3 py-1 text-xs font-bold text-[#e24b4a]">{{ $formatRupiah($totalExpense) }}</span>
            </div>

            @if ($expenseByCategory->isEmpty())
                <div class="mt-4 rounded-2xl bg-white px-4 py-5 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-xl">🧾</div>
                    <p class="mt-2 text-sm font-bold text-[#8890b0]">Belum ada data bulan ini.</p>
                    <p class="mt-1 text-xs text-[#8890b0]">Catat pemasukan atau pengeluaran untuk melihat laporan bulanan.</p>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <a href="{{ route('transactions.create') }}" class="rounded-full bg-[#1a1aff]/10 border border-[#1a1aff]/20 px-4 py-2 text-xs font-bold text-[#1a1aff]">Tambah Transaksi</a>
                        <a href="{{ route('transactions.voice') }}" class="rounded-2xl bg-white px-3 py-3 text-xs font-bold text-[#1a1aff] border border-[#dde3f5]">Input Suara</a>
                    </div>
                </div>
            @else
                <div class="mt-4 space-y-4">
                    @foreach ($expenseByCategory as $row)
                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl text-base" style="background-color: {{ $row['category']?->color ?? '#f1f5f9' }}22; color: {{ $row['category']?->color ?? '#64748b' }};">
                                        {{ $row['category']?->icon ?: '💸' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-[#0f0f5e]">{{ $row['category']?->name ?? 'Tanpa kategori' }}</p>
                                        <p class="text-xs text-[#8890b0]">{{ $row['percentage'] }}% dari pengeluaran</p>
                                    </div>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold text-[#0f0f5e] tabular-nums">{{ $formatRupiah($row['total']) }}</p>
                            </div>
                            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white">
                                <div class="h-full rounded-full bg-emerald-400" style="width: {{ $row['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-[#0f0f5e]">Transaksi Bulan Ini</h3>
                    <p class="mt-1 text-xs text-[#8890b0]">5 transaksi terbaru pada bulan terpilih.</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-[#1a1aff] hover:text-[#4d4dff]">Riwayat →</a>
            </div>

            @if ($latestTransactions->isEmpty())
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-[#dde3f5] text-center">
                    <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-xl">📋</div>
                    <h3 class="text-sm font-bold text-[#8890b0]">Belum ada data bulan ini.</h3>
                    <p class="mt-1 text-xs text-[#8890b0]">Catat pemasukan atau pengeluaran untuk melihat laporan bulanan.</p>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <a href="{{ route('transactions.create') }}" class="rounded-full bg-[#1a1aff]/10 border border-[#1a1aff]/20 px-4 py-2 text-xs font-bold text-[#1a1aff]">Tambah Transaksi</a>
                        <a href="{{ route('transactions.voice') }}" class="rounded-2xl bg-[#1a1aff]/10 border border-[#1a1aff]/20 px-3 py-3 text-xs font-bold text-[#1a1aff] hover:text-[#4d4dff]">Input Suara</a>
                    </div>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($latestTransactions as $transaction)
                        @php
                            $isIncome = $transaction->type === \App\Models\Transaction::TYPE_INCOME;
                            $amountPrefix = $isIncome ? '+' : '-';
                            $amountClass = $isIncome ? 'text-[#1d9e75]' : 'text-[#e24b4a]';
                            $badgeClass = $isIncome ? 'bg-[#1d9e75]/10 text-[#1d9e75]' : 'bg-[#e24b4a]/10 text-[#e24b4a]';
                            $typeLabel = $isIncome ? 'Pemasukan' : 'Pengeluaran';
                        @endphp

                        <a href="{{ route('transactions.edit', $transaction) }}" class="block bg-white rounded-2xl p-4 shadow-sm border border-[#dde3f5]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-lg" style="background-color: {{ $transaction->category?->color ?? '#f1f5f9' }}22; color: {{ $transaction->category?->color ?? '#64748b' }};">
                                        {{ $transaction->category?->icon ?: '💸' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-[#0f0f5e]">{{ $transaction->category?->name ?? 'Tanpa kategori' }}</p>
                                        <p class="mt-0.5 line-clamp-2 text-xs text-[#8890b0]">{{ $transaction->note ?: 'Tidak ada catatan' }}</p>
                                        <div class="mt-2 flex items-center gap-2">
                                            <span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $badgeClass }}">{{ $typeLabel }}</span>
                                            <span class="text-[10px] font-medium text-[#8890b0]">{{ $transaction->transaction_date->format('d M Y') }}</span>
                                        </div>
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
