<div class="space-y-5">
    @if (session('transaction_status'))
        <div role="alert" class="rounded-2xl border border-[#1d9e75]/25 bg-[#A3E635]/10 px-4 py-3 text-sm font-bold text-[#A3E635] shadow-sm">
            ✅ {{ session('transaction_status') }}
        </div>
    @endif

    <section class="rounded-3xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div class="grid gap-3">
            <div>
                <label for="month" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Bulan</label>
                <input id="month" type="month" wire:model.live="month" class="mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="type" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Jenis</label>
                    <select id="type" wire:model.live="type" class="mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                        <option value="all">Semua</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label for="categoryId" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Kategori</label>
                    <select id="categoryId" wire:model.live="categoryId" class="mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
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
        <section class="rounded-3xl p-6 text-center" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
            <p class="text-4xl mb-3">📋</p>
            <h2 class="text-sm font-bold text-white">Belum ada transaksi</h2>
            <p class="mt-1 text-xs text-[#64748B]">Mulai catat pemasukan atau pengeluaran bulan ini.</p>
            <a href="{{ route('transactions.create') }}" class="mt-4 inline-flex rounded-2xl px-4 py-3 text-sm font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">
                + Tambah Transaksi
            </a>
        </section>
    @else
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-white">Daftar Transaksi</h2>
                <span class="rounded-full px-3 py-1 text-xs font-bold" style="background:rgba(163,230,53,0.15) !important;color:#A3E635 !important;border-radius:999px !important;">{{ $transactions->count() }}</span>
            </div>

            <div class="space-y-2">
                @foreach ($transactions as $transaction)
                    @php
                        $isIncome = $transaction->type === \App\Models\Transaction::TYPE_INCOME;
                        $amountPrefix = $isIncome ? '+' : '-';
                        $amountClass = $isIncome ? 'text-[#A3E635]' : 'text-[#F87171]';
                        $badgeClass = $isIncome ? 'bg-[#A3E635]/10 text-[#A3E635]' : 'bg-[#e24b4a]/10 text-[#F87171]';
                        $typeLabel = $isIncome ? 'Pemasukan' : 'Pengeluaran';
                    @endphp

                    <article wire:key="transaction-{{ $transaction->id }}" class="rounded-2xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:20px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-lg" style="background-color: {{ $transaction->category?->color ?? '#f1f5f9' }}22; color: {{ $transaction->category?->color ?? '#64748b' }};">
                                    {{ $transaction->category?->icon ?: '💸' }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-white">{{ $transaction->category?->name ?? 'Tanpa kategori' }}</p>
                                    <p class="mt-0.5 line-clamp-2 text-xs text-[#64748B]">{{ $transaction->note ?: 'Tidak ada catatan' }}</p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $badgeClass }}">{{ $typeLabel }}</span>
                                        <span class="text-[10px] font-medium text-[#64748B]">{{ $transaction->transaction_date->format('d M Y') }}</span>
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="text-[10px] font-bold text-[#A3E635]">Edit</a>
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
