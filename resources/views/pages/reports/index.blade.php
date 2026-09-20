<x-app-layout>
    <x-slot:title>Laporan Bulanan</x-slot:title>

    @php
        $balanceIsPositive = $monthlyBalance >= 0;
    @endphp

    <div class="px-4 pt-6 space-y-5 pb-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Laporan Bulanan</h2>
                <p class="mt-1 text-xs text-slate-400">Ringkasan pemasukan dan pengeluaran.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="shrink-0 rounded-full bg-slate-100 px-3 py-2 text-xs font-bold text-slate-500">
                Beranda
            </a>
        </div>

        <div class="grid grid-cols-3 gap-2 text-xs font-bold">
            <a href="{{ route('reports.export.csv', ['month' => $selectedMonthValue]) }}" class="rounded-2xl bg-white/80 px-2 py-2.5 text-center text-slate-600 shadow-sm border border-white/60">CSV</a>
            <a href="{{ route('reports.export.xlsx', ['month' => $selectedMonthValue]) }}" class="rounded-2xl bg-white/80 px-2 py-2.5 text-center text-slate-600 shadow-sm border border-white/60">Excel</a>
            <a href="{{ route('reports.export.pdf', ['month' => $selectedMonthValue]) }}" class="rounded-2xl bg-white/80 px-2 py-2.5 text-center text-slate-600 shadow-sm border border-white/60">PDF</a>
        </div>

        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-4 shadow-sm border border-white/60">
            <form method="GET" action="{{ route('reports.index') }}" class="space-y-3">
                <div>
                    <label for="month" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Bulan Laporan</label>
                    <input id="month" name="month" type="month" value="{{ $selectedMonthValue }}" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
                    Tampilkan Laporan
                </button>
            </form>

            <div class="mt-4 flex items-center justify-between gap-3 text-xs font-bold">
                <a href="{{ route('reports.index', ['month' => $previousMonthValue]) }}" class="rounded-full bg-slate-100 px-3 py-2 text-slate-500">← Bulan Sebelumnya</a>
                <span class="text-center text-slate-700">{{ $selectedMonthLabel }}</span>
                <a href="{{ route('reports.index', ['month' => $nextMonthValue]) }}" class="rounded-full bg-slate-100 px-3 py-2 text-slate-500">Bulan Berikutnya →</a>
            </div>
        </section>

        {{-- Summary Cards --}}
        <section class="grid grid-cols-2 gap-3">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-500">Pemasukan</p>
                <p class="mt-2 text-lg font-extrabold text-slate-800 tabular-nums"><x-money :amount="$totalIncome" /></p>
            </div>
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
                <p class="text-xs font-bold uppercase tracking-wide text-rose-500">Pengeluaran</p>
                <p class="mt-2 text-lg font-extrabold text-slate-800 tabular-nums"><x-money :amount="$totalExpense" /></p>
            </div>
            <div class="col-span-2 bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
                <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">Saldo Bulan Ini</p>
                <p class="mt-2 text-2xl font-extrabold tabular-nums {{ $balanceIsPositive ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $balanceIsPositive ? '+' : '-' }} <x-money :amount="abs($monthlyBalance)" />
                </p>
                <p class="mt-1 text-xs text-slate-400">Pemasukan dikurangi pengeluaran pada {{ $selectedMonthLabel }}.</p>
            </div>
        </section>

        {{-- ── CHART 1: Tren 6 Bulan (Bar Chart) ── --}}
        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Tren 6 Bulan Terakhir</h3>
                    <p class="mt-1 text-xs text-slate-400">Perbandingan pemasukan dan pengeluaran.</p>
                </div>
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-500">Bar Chart</span>
            </div>

            @if ($sixMonthTrend->every(fn ($m) => $m['income'] == 0 && $m['expense'] == 0))
                <div class="rounded-2xl bg-slate-50 px-4 py-8 text-center">
                    <p class="text-3xl">📊</p>
                    <p class="mt-2 text-sm font-bold text-slate-700">Belum ada data transaksi.</p>
                    <p class="mt-1 text-xs text-slate-400">Catat transaksi untuk melihat tren keuangan.</p>
                </div>
            @else
                <div class="relative h-52">
                    <canvas id="trendChart"></canvas>
                </div>
                <div class="mt-3 flex items-center gap-4 text-xs font-medium text-slate-500">
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>Pemasukan</span>
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-500 inline-block"></span>Pengeluaran</span>
                </div>
            @endif
        </section>

        {{-- ── CHART 2: Doughnut per Kategori ── --}}
        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Pengeluaran per Kategori</h3>
                    <p class="mt-1 text-xs text-slate-400">Kategori pengeluaran terbesar bulan ini.</p>
                </div>
                <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-500"><x-money :amount="$totalExpense" /></span>
            </div>

            @if ($expenseByCategory->isEmpty())
                <div class="rounded-2xl bg-slate-50 px-4 py-8 text-center">
                    <p class="text-3xl">🧾</p>
                    <p class="mt-2 text-sm font-bold text-slate-700">Belum ada data bulan ini.</p>
                    <p class="mt-1 text-xs text-slate-400">Catat pengeluaran untuk melihat laporan bulanan.</p>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <a href="{{ route('transactions.create') }}" class="rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-3 py-3 text-xs font-bold text-white shadow-lg shadow-indigo-500/30">Tambah Transaksi</a>
                        <a href="#" @click.prevent="$dispatch('open-voice-modal')" class="rounded-2xl bg-white px-3 py-3 text-xs font-bold text-indigo-600 border border-indigo-100">Input Suara</a>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center">
                    <div class="relative w-44 h-44">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    @foreach ($expenseByCategory as $row)
                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl text-base"
                                          style="background-color: {{ $row['color'] }}22; color: {{ $row['color'] }};">
                                        {{ $row['category']?->icon ?: '💸' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $row['category']?->name ?? 'Tanpa kategori' }}</p>
                                        <p class="text-xs text-slate-400">{{ $row['percentage'] }}% dari pengeluaran</p>
                                    </div>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold text-slate-800 tabular-nums"><x-money :amount="$row['total']" /></p>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width: {{ $row['percentage'] }}%; background-color: {{ $row['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Transaksi Bulan Ini --}}
        <section class="space-y-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Transaksi Bulan Ini</h3>
                    <p class="mt-1 text-xs text-slate-400">5 transaksi terbaru pada bulan terpilih.</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-indigo-500">Riwayat →</a>
            </div>

            @if ($latestTransactions->isEmpty())
                <div class="bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-sm border border-white/60 text-center">
                    <p class="text-4xl mb-3">📋</p>
                    <h3 class="text-sm font-bold text-slate-700">Belum ada data bulan ini.</h3>
                    <p class="mt-1 text-xs text-slate-400">Catat pemasukan atau pengeluaran untuk melihat laporan bulanan.</p>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <a href="{{ route('transactions.create') }}" class="rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-3 py-3 text-xs font-bold text-white shadow-lg shadow-indigo-500/30">Tambah Transaksi</a>
                        <a href="#" @click.prevent="$dispatch('open-voice-modal')" class="rounded-2xl bg-indigo-50 px-3 py-3 text-xs font-bold text-indigo-600">Input Suara</a>
                    </div>
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

                        <a href="{{ route('transactions.edit', $transaction) }}" class="block bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-lg"
                                          style="background-color: {{ $transaction->category?->color ?? '#f1f5f9' }}22; color: {{ $transaction->category?->color ?? '#64748b' }};">
                                        {{ $transaction->category?->icon ?: '💸' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $transaction->category?->name ?? 'Tanpa kategori' }}</p>
                                        <p class="mt-0.5 line-clamp-2 text-xs text-slate-400">{{ $transaction->note ?: 'Tidak ada catatan' }}</p>
                                        <div class="mt-2 flex items-center gap-2">
                                            <span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $badgeClass }}">{{ $typeLabel }}</span>
                                            <span class="text-[10px] font-medium text-slate-400">{{ $transaction->transaction_date->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="shrink-0 text-sm font-extrabold tabular-nums {{ $amountClass }}">
                                    {{ $amountPrefix }} <x-money :amount="$transaction->amount" />
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    {{-- Chart.js Scripts --}}
    @if (!$sixMonthTrend->every(fn ($m) => $m['income'] == 0 && $m['expense'] == 0) || !$expenseByCategory->isEmpty())
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.color = '#94a3b8';

            // ── Bar Chart: Tren 6 Bulan ──
            const trendEl = document.getElementById('trendChart');
            if (trendEl) {
                const trend = @json($sixMonthTrend);
                new Chart(trendEl, {
                    type: 'bar',
                    data: {
                        labels: trend.map(m => m.label),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: trend.map(m => m.income),
                                backgroundColor: 'rgba(34,197,94,0.75)',
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                            {
                                label: 'Pengeluaran',
                                data: trend.map(m => m.expense),
                                backgroundColor: 'rgba(239,68,68,0.75)',
                                borderRadius: 8,
                                borderSkipped: false,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID'),
                                },
                            },
                        },
                        scales: {
                            x: { grid: { display: false }, border: { display: false } },
                            y: {
                                grid: { color: 'rgba(148,163,184,0.12)' },
                                border: { display: false },
                                ticks: {
                                    callback: v => 'Rp ' + (v >= 1_000_000 ? (v / 1_000_000).toFixed(1) + 'jt' : v.toLocaleString('id-ID')),
                                },
                            },
                        },
                    },
                });
            }

            // ── Doughnut Chart: Kategori ──
            const doughnutEl = document.getElementById('doughnutChart');
            if (doughnutEl) {
                const categories = @json($expenseByCategory);
                new Chart(doughnutEl, {
                    type: 'doughnut',
                    data: {
                        labels: categories.map(r => r.category ? r.category.name : 'Lainnya'),
                        datasets: [{
                            data: categories.map(r => r.total),
                            backgroundColor: categories.map(r => r.color),
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '68%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID') + ' (' + categories[ctx.dataIndex].percentage + '%)',
                                },
                            },
                        },
                    },
                });
            }
        });
    </script>
    @endpush
    @endif
</x-app-layout>
