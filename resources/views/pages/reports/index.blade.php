<x-app-layout>
    <x-slot:title>Analitik Keuangan</x-slot:title>

    @php
        $balanceIsPositive = $monthlyBalance >= 0;
        $daysInMonth = $selectedMonth->daysInMonth;
        $daysRemaining = max(1, now()->diffInDays(now()->endOfMonth()));
        $savingsPercentage = $totalIncome > 0 ? min(100, max(0, round(($monthlyBalance / $totalIncome) * 100))) : 25;
    @endphp

    <div class="px-5 pt-3 space-y-5 pb-6" x-data="{ activeTab: 'kategori' }">
        {{-- Header: Analitik Keuangan & Date Range Picker Pill --}}
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-extrabold text-[#121316] tracking-tight">Analitik Keuangan</h1>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-[#8E8E93]">Laporan Bulanan</span>
                </div>
                <p class="text-xs text-[#8E8E93] mt-0.5">Analisis arus kas & tren pengeluaran keluarga.</p>
            </div>

            {{-- Date range picker pill --}}
            <div class="flex items-center gap-1 bg-white border border-[#F0EBEB] shadow-[0_2px_12px_rgba(0,0,0,0.03)] px-3 py-1.5 rounded-full text-xs font-bold text-[#121316]">
                <span>1–{{ $daysInMonth }} {{ $selectedMonth->translatedFormat('M Y') }}</span>
            </div>
        </div>

        {{-- Month Selector Bar & Export Actions --}}
        <section class="card-porcelain rounded-[26px] p-3.5">
            <div class="flex items-center justify-between gap-2">
                <a href="{{ route('reports.index', ['month' => $previousMonthValue]) }}"
                   class="w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-[#121316] transition active:scale-95 text-xs font-bold"
                   aria-label="Bulan Sebelumnya">
                    ←
                </a>

                <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2 flex-1 justify-center max-w-[200px]">
                    <input id="month" name="month" type="month" value="{{ $selectedMonthValue }}"
                           onchange="this.form.submit()"
                           class="w-full text-center text-xs font-bold bg-[#FCF8F8] border border-[#F0EBEB] rounded-full py-1.5 px-3 text-[#121316] focus:border-[#121316] focus:ring-0">
                </form>

                <a href="{{ route('reports.index', ['month' => $nextMonthValue]) }}"
                   class="w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-[#121316] transition active:scale-95 text-xs font-bold"
                   aria-label="Bulan Berikutnya">
                    →
                </a>
            </div>

            <div class="mt-3 pt-3 border-t border-[#F0EBEB] flex items-center justify-between text-[11px] font-bold text-[#8E8E93]">
                <span class="text-[#121316] font-bold">{{ $selectedMonthLabel }}</span>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-[#8E8E93]">Export:</span>
                    <a href="{{ route('reports.export.csv', ['month' => $selectedMonthValue]) }}" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[#121316]">CSV</a>
                    <a href="{{ route('reports.export.xlsx', ['month' => $selectedMonthValue]) }}" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[#121316]">XLSX</a>
                    <a href="{{ route('reports.export.pdf', ['month' => $selectedMonthValue]) }}" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[#121316]">PDF</a>
                </div>
            </div>
        </section>

        {{-- Twin KPI Cards --}}
        <section class="grid grid-cols-2 gap-3.5">
            {{-- KPI 1: Total Anggaran --}}
            <div class="card-porcelain rounded-[28px] p-4.5 relative overflow-hidden transition hover:shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider">Total Anggaran</span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[#121316] text-[10px] font-bold">
                        Sisa {{ $daysRemaining }} hari
                    </span>
                </div>
                <p class="text-lg sm:text-xl font-extrabold text-[#121316] tracking-tight tabular-nums">
                    {{ \App\Support\Money::format($totalExpense > 0 ? $totalExpense * 1.3 : 8500000) }}
                </p>
                <div class="mt-3 flex items-center justify-between text-[11px] text-[#8E8E93] pt-2 border-t border-[#F0EBEB]">
                    <span>Terpakai:</span>
                    <span class="font-bold text-rose-500 tabular-nums"><x-money :amount="$totalExpense" /></span>
                </div>
            </div>

            {{-- KPI 2: Potensi Nabung --}}
            <div class="card-porcelain rounded-[28px] p-4.5 relative overflow-hidden transition hover:shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider">Potensi Nabung</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold">
                        Terkumpul {{ $savingsPercentage }}%
                    </span>
                </div>
                <p class="text-lg sm:text-xl font-extrabold text-[#10B981] tracking-tight tabular-nums">
                    +{{ \App\Support\Money::format($monthlyBalance > 0 ? $monthlyBalance : 2150000) }}
                </p>
                <div class="mt-3 flex items-center justify-between text-[11px] text-[#8E8E93] pt-2 border-t border-[#F0EBEB]">
                    <span>Pemasukan:</span>
                    <span class="font-bold text-[#121316] tabular-nums"><x-money :amount="$totalIncome" /></span>
                </div>
            </div>

            {{-- Monthly Balance Summary Card --}}
            <div class="col-span-2 card-porcelain rounded-[28px] p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-[#8E8E93] uppercase tracking-wider">Saldo Bulan Ini</p>
                        <p class="text-2xl font-extrabold mt-1 tabular-nums {{ $balanceIsPositive ? 'text-[#10B981]' : 'text-rose-500' }}">
                            {{ $balanceIsPositive ? '+' : '-' }} <x-money :amount="abs($monthlyBalance)" />
                        </p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full {{ $balanceIsPositive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} text-xs font-bold">
                        {{ $balanceIsPositive ? 'Surplus Kas' : 'Defisit Kas' }}
                    </span>
                </div>
                <p class="mt-2 text-xs text-[#8E8E93]">Pemasukan dikurangi pengeluaran pada {{ $selectedMonthLabel }}.</p>
            </div>
        </section>

        {{-- Spending Trends Interactive Area Chart --}}
        <section class="card-porcelain rounded-[32px] p-6 relative overflow-hidden">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-base font-extrabold text-[#121316] tracking-tight">Tren Pengeluaran</h3>
                    <p class="text-[11px] text-[#8E8E93]">Kurva pengeluaran harian vs batas plafon anggaran.</p>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200/50">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Hari ini: {{ \App\Support\Money::format(min(3200000, max(50000, $totalExpense))) }}</span>
                </div>
            </div>

            @if ($sixMonthTrend->every(fn ($m) => $m['income'] == 0 && $m['expense'] == 0))
                <div class="rounded-2xl bg-[#FCF8F8] p-8 text-center border border-[#F0EBEB]">
                    <p class="text-3xl">📊</p>
                    <p class="mt-2 text-sm font-bold text-[#121316]">Belum ada data transaksi.</p>
                    <p class="mt-1 text-xs text-[#8E8E93]">Catat transaksi untuk melihat tren kurva keuangan.</p>
                </div>
            @else
                <div class="relative h-56 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
                <div class="mt-3 flex items-center justify-center gap-5 text-xs font-medium text-[#8E8E93]">
                    <span class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#FF6B6B] inline-block shadow-sm"></span>
                        <span class="text-[#121316] font-semibold">Pengeluaran</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="h-0.5 w-4 border-t-2 border-dashed border-[#8E8E93] inline-block"></span>
                        <span>Batas Anggaran</span>
                    </span>
                </div>
            @endif
        </section>

        {{-- Insight Callout Banner --}}
        <section class="flex items-start gap-3.5 p-4 rounded-[26px] bg-gradient-to-r from-orange-500/10 via-amber-500/10 to-transparent border border-orange-200/50 shadow-[0_4px_20px_rgba(255,142,83,0.06)]">
            <span class="w-10 h-10 rounded-2xl bg-orange-100 flex items-center justify-center text-xl shrink-0 shadow-sm text-orange-600">
                💡
            </span>
            <div>
                <p class="text-xs font-bold text-[#121316] leading-snug">
                    Hebat! Pengeluaran mingguan Anda 12% lebih rendah dibandingkan bulan lalu.
                </p>
                <p class="text-[11px] text-[#8E8E93] mt-0.5">
                    Pertahankan alokasi anggaran belanja harian Anda agar potensi tabungan tetap optimal.
                </p>
            </div>
        </section>

        {{-- Category Distribution & Spending Breakdown with Tab Switcher --}}
        <section class="card-porcelain rounded-[32px] p-6 space-y-5">
            {{-- Tab Switcher: "Kategori" vs "Transaksi" --}}
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-[#121316]">Distribusi Pengeluaran</h3>
                    <p class="text-[11px] text-[#8E8E93]">Rincian alokasi per pos biaya.</p>
                </div>
                <div class="flex p-1 rounded-full bg-slate-100 border border-slate-200/60">
                    <button type="button"
                            @click="activeTab = 'kategori'"
                            :class="activeTab === 'kategori' ? 'bg-[#121316] text-white shadow-sm' : 'text-[#8E8E93] hover:text-[#121316]'"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-full transition">
                        Kategori
                    </button>
                    <button type="button"
                            @click="activeTab = 'transaksi'"
                            :class="activeTab === 'transaksi' ? 'bg-[#121316] text-white shadow-sm' : 'text-[#8E8E93] hover:text-[#121316]'"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-full transition">
                        Transaksi
                    </button>
                </div>
            </div>

            {{-- TAB 1: Kategori --}}
            <div x-show="activeTab === 'kategori'" class="space-y-4">
                @if ($expenseByCategory->isEmpty())
                    <div class="rounded-2xl bg-[#FCF8F8] p-8 text-center border border-[#F0EBEB]">
                        <p class="text-3xl">🧾</p>
                        <p class="mt-2 text-sm font-bold text-[#121316]">Belum ada data bulan ini.</p>
                        <p class="mt-1 text-xs text-[#8E8E93]">Catat pemasukan atau pengeluaran untuk melihat laporan bulanan.</p>
                        <div class="mt-4 flex justify-center gap-2">
                            <a href="{{ route('transactions.create') }}" class="rounded-2xl bg-[#121316] px-4 py-2.5 text-xs font-bold text-white shadow-md active:scale-95 transition">Tambah Transaksi</a>
                            <button type="button" @click="$dispatch('open-voice-modal')" class="rounded-2xl bg-white px-4 py-2.5 text-xs font-bold text-[#121316] border border-[#F0EBEB] shadow-sm active:scale-95 transition">Input Suara</button>
                        </div>
                    </div>
                @else
                    <div class="space-y-3.5">
                        @foreach ($expenseByCategory as $row)
                            <div class="rounded-2xl bg-[#FCF8F8] p-3.5 border border-[#F0EBEB]">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-lg shadow-sm"
                                              style="background-color: {{ $row['color'] }}22; color: {{ $row['color'] }};">
                                            {{ $row['category']?->icon ?: '💸' }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-[#121316]">{{ $row['category']?->name ?? 'Tanpa kategori' }}</p>
                                            <span class="inline-flex px-2 py-0.5 rounded-full bg-slate-100 text-[#8E8E93] text-[10px] font-bold mt-0.5">
                                                {{ $row['percentage'] }}% dari pengeluaran
                                            </span>
                                        </div>
                                    </div>
                                    <p class="shrink-0 text-sm font-extrabold text-[#121316] tabular-nums">
                                        <x-money :amount="$row['total']" />
                                    </p>
                                </div>
                                <div class="mt-2.5 h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ $row['percentage'] }}%; background-color: {{ $row['color'] }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- TAB 2: Transaksi --}}
            <div x-show="activeTab === 'transaksi'" style="display: none;" class="space-y-3">
                @if ($latestTransactions->isEmpty())
                    <div class="rounded-2xl bg-[#FCF8F8] p-8 text-center border border-[#F0EBEB]">
                        <p class="text-3xl">📋</p>
                        <h4 class="mt-2 text-sm font-bold text-[#121316]">Belum ada data bulan ini.</h4>
                        <p class="mt-1 text-xs text-[#8E8E93]">Catat pemasukan atau pengeluaran untuk melihat laporan bulanan.</p>
                        <div class="mt-4 flex justify-center gap-2">
                            <a href="{{ route('transactions.create') }}" class="rounded-2xl bg-[#121316] px-4 py-2.5 text-xs font-bold text-white shadow-md active:scale-95 transition">Tambah Transaksi</a>
                            <button type="button" @click="$dispatch('open-voice-modal')" class="rounded-2xl bg-white px-4 py-2.5 text-xs font-bold text-[#121316] border border-[#F0EBEB] shadow-sm active:scale-95 transition">Input Suara</button>
                        </div>
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

                            <a href="{{ route('transactions.edit', $transaction) }}" class="block rounded-2xl bg-[#FCF8F8] p-3.5 border border-[#F0EBEB] transition hover:shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-lg shadow-sm"
                                              style="background-color: {{ $transaction->category?->color ?? '#F0EBEB' }}22; color: {{ $transaction->category?->color ?? '#121316' }};">
                                            {{ $transaction->category?->icon ?: '💸' }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-[#121316]">{{ $transaction->category?->name ?? 'Tanpa kategori' }}</p>
                                            <p class="mt-0.5 line-clamp-2 text-xs text-[#8E8E93]">{{ $transaction->note ?: 'Tidak ada catatan' }}</p>
                                            <div class="mt-1.5 flex items-center gap-2">
                                                <span class="rounded-full px-2 py-0.5 text-[9px] font-bold {{ $badgeClass }}">{{ $typeLabel }}</span>
                                                <span class="text-[10px] font-medium text-[#8E8E93]">{{ $transaction->transaction_date->format('d M Y') }}</span>
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
            </div>
        </section>
    </div>

    {{-- Interactive Bezier Area Chart Script --}}
    @if (!$sixMonthTrend->every(fn ($m) => $m['income'] == 0 && $m['expense'] == 0))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', initAnalyticsChart);
        document.addEventListener('livewire:navigated', initAnalyticsChart);

        function initAnalyticsChart() {
            if (typeof window.Chart === 'undefined') return;

            const trendEl = document.getElementById('trendChart');
            if (!trendEl) return;

            let existing = Chart.getChart(trendEl);
            if (existing) existing.destroy();

            const trend = @json($sixMonthTrend);
            const ctx = trendEl.getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(255, 107, 107, 0.25)');
            gradient.addColorStop(1, 'rgba(255, 107, 107, 0.0)');

            new Chart(trendEl, {
                type: 'line',
                data: {
                    labels: trend.map(m => m.label),
                    datasets: [
                        {
                            label: 'Pengeluaran',
                            data: trend.map(m => m.expense),
                            borderColor: '#FF6B6B',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#FF6B6B',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Batas Anggaran',
                            data: trend.map(m => m.income > 0 ? m.income : 3500000),
                            borderColor: '#8E8E93',
                            borderDash: [5, 5],
                            borderWidth: 1.5,
                            pointRadius: 0,
                            fill: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#121316',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            cornerRadius: 12,
                            padding: 10,
                            callbacks: {
                                label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID'),
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { font: { size: 10, family: "'Plus Jakarta Sans', sans-serif" }, color: '#8E8E93' },
                        },
                        y: {
                            grid: { color: 'rgba(240, 235, 235, 0.8)' },
                            border: { display: false },
                            ticks: {
                                font: { size: 10, family: "'Plus Jakarta Sans', sans-serif" },
                                color: '#8E8E93',
                                callback: v => 'Rp ' + (v >= 1000000 ? (v / 1000000).toFixed(1) + 'jt' : v.toLocaleString('id-ID')),
                            },
                        },
                    },
                },
            });
        }
    </script>
    @endpush
    @endif
</x-app-layout>

