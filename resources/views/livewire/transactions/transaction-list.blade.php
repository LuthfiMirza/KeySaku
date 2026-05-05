<div class="space-y-5">
    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-4 shadow-sm border border-white/60">
        <div class="grid gap-3">
            <div>
                <label for="month" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Bulan</label>
                <input id="month" type="month" wire:model.live="month" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="type" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Jenis</label>
                    <select id="type" wire:model.live="type" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">Semua</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label for="categoryId" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Kategori</label>
                    <select id="categoryId" wire:model.live="categoryId" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">Semua</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </section>

    @if ($transactions->isEmpty())
        <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-sm border border-white/60 text-center">
            <p class="text-4xl mb-3">📋</p>
            <h2 class="text-sm font-bold text-slate-700">Belum ada transaksi</h2>
            <p class="mt-1 text-xs text-slate-400">Mulai catat pemasukan atau pengeluaran bulan ini.</p>
            <a href="{{ route('transactions.create') }}" class="mt-4 inline-flex rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">
                + Tambah Transaksi
            </a>
        </section>
    @else
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-700">Daftar Transaksi</h2>
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-500">{{ $transactions->count() }}</span>
            </div>

            <div class="space-y-2">
                @foreach ($transactions as $transaction)
                    @php
                        $isIncome = $transaction->type === \App\Models\Transaction::TYPE_INCOME;
                        $amountPrefix = $isIncome ? '+' : '-';
                        $amountClass = $isIncome ? 'text-emerald-600' : 'text-rose-600';
                        $badgeClass = $isIncome ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600';
                        $typeLabel = $isIncome ? 'Pemasukan' : 'Pengeluaran';
                    @endphp

                    <article wire:key="transaction-{{ $transaction->id }}" class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-lg" style="background-color: {{ $transaction->category?->color ?? '#f1f5f9' }}22; color: {{ $transaction->category?->color ?? '#64748b' }};">
                                    {{ $transaction->category?->icon ?: '💸' }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ $transaction->category?->name ?? 'Tanpa kategori' }}</p>
                                    <p class="mt-0.5 line-clamp-2 text-xs text-slate-400">{{ $transaction->note ?: 'Tidak ada catatan' }}</p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $badgeClass }}">{{ $typeLabel }}</span>
                                        <span class="text-[10px] font-medium text-slate-400">{{ $transaction->transaction_date->format('d M Y') }}</span>
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="text-[10px] font-bold text-indigo-600">Edit</a>
                                    </div>
                                </div>
                            </div>

                            <p class="shrink-0 text-sm font-extrabold tabular-nums {{ $amountClass }}">
                                {{ $amountPrefix }} Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
