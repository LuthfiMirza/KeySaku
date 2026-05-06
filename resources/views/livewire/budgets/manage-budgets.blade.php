<div class="space-y-5 lg:max-w-5xl lg:mx-auto">
    @if (session('budget_status'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm">
            {{ session('budget_status') }}
        </div>
    @endif

    @php
        $summaryRemainingLabel = $summary['totalRemaining'] >= 0
            ? 'Rp '.number_format($summary['totalRemaining'], 0, ',', '.')
            : 'Lewat Rp '.number_format(abs($summary['totalRemaining']), 0, ',', '.');
    @endphp

    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Ringkasan Anggaran</h3>
                <p class="mt-1 text-xs text-slate-400">Total anggaran untuk {{ $selectedMonthLabel }}.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-500">{{ $selectedMonthLabel }}</span>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                <p class="text-xs text-slate-400">Total Anggaran</p>
                <p class="mt-1 text-sm font-extrabold text-slate-800 tabular-nums">Rp {{ number_format($summary['totalBudget'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                <p class="text-xs text-slate-400">Terpakai</p>
                <p class="mt-1 text-sm font-extrabold text-rose-600 tabular-nums">Rp {{ number_format($summary['totalUsed'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                <p class="text-xs text-slate-400">Sisa</p>
                <p class="mt-1 text-sm font-extrabold tabular-nums {{ $summary['totalRemaining'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $summaryRemainingLabel }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                <p class="text-xs text-slate-400">Lewat Batas</p>
                <p class="mt-1 text-sm font-extrabold text-slate-800">{{ $summary['overLimitCount'] }} kategori</p>
            </div>
        </div>
    </section>

    <section id="buat-anggaran" class="bg-white/80 backdrop-blur-sm rounded-3xl p-5 shadow-sm border border-white/60 scroll-mt-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Buat Anggaran</h3>
                <p id="budget-period-helper" class="mt-1 max-w-[14rem] text-xs leading-relaxed text-slate-400">Pilih bulan dan tahun untuk melihat atau membuat anggaran.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-500">{{ $selectedMonthLabel }}</span>
        </div>

        <form wire:submit="save" class="mt-5 space-y-5">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="month" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Bulan</label>
                    <select id="month" wire:model.live="month" aria-describedby="budget-period-helper" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach (range(1, 12) as $monthOption)
                            <option value="{{ $monthOption }}">{{ now()->setDate((int) $year, $monthOption, 1)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    @error('month')
                        <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="year" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Tahun</label>
                    <input id="year" type="number" wire:model.live="year" aria-describedby="budget-period-helper" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('year')
                        <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                <p class="text-xs font-medium text-indigo-700">Gunakan anggaran bulan lalu sebagai dasar bulan ini.</p>
                <button type="button" wire:click="copyPreviousMonthBudgets" class="mt-3 inline-flex min-h-11 w-full items-center justify-center rounded-2xl bg-white px-4 py-2 text-xs font-bold text-indigo-600 shadow-sm">
                    Salin dari Bulan Lalu
                </button>
            </div>

            <div>
                <label for="category_id" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Kategori</label>
                <select id="category_id" wire:model="category_id" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih kategori</option>
                    @foreach ($expenseCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-xs font-bold uppercase tracking-wide text-slate-400">Nominal Anggaran</label>
                <input id="amount" type="number" min="0" step="1000" wire:model="amount" inputmode="numeric" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 1500000">
                @error('amount')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            @if ($expenseCategories->isEmpty())
                <div class="rounded-2xl bg-amber-50 px-4 py-3 text-xs font-medium text-amber-700">
                    Tambahkan kategori pengeluaran terlebih dahulu sebelum membuat anggaran.
                </div>
            @endif

            <button type="submit" @disabled($expenseCategories->isEmpty()) class="min-h-12 w-full rounded-2xl px-4 py-3 text-sm font-bold transition {{ $expenseCategories->isEmpty() ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30' }}">
                Simpan Anggaran
            </button>
        </form>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Progress Anggaran</h3>
                <p class="mt-1 text-xs text-slate-400">Pantau pemakaian anggaran untuk {{ $selectedMonthLabel }}.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-500" aria-label="Jumlah anggaran bulan ini: {{ $budgets->count() }}">{{ $budgets->count() }}</span>
        </div>

        @if ($budgets->isEmpty())
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-sm border border-white/60 text-center">
                <p class="text-4xl mb-3">💰</p>
                <h3 class="text-sm font-bold text-slate-700">Belum ada anggaran untuk bulan ini.</h3>
                <p class="mt-1 text-xs text-slate-400">Buat anggaran per kategori agar KeySaku bisa memantau batas pengeluaranmu.</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <a href="#buat-anggaran" aria-label="Buat anggaran untuk bulan yang dipilih" class="inline-flex min-h-11 items-center rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-indigo-500/30">Buat Anggaran</a>
                    <a href="{{ route('transactions.index') }}" aria-label="Lihat riwayat transaksi" class="inline-flex min-h-11 items-center rounded-2xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600">Lihat Riwayat</a>
                </div>
            </div>
        @else
            @if ((float) $summary['totalUsed'] <= 0)
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                    <p class="text-xs font-medium text-indigo-700">Belum ada pengeluaran untuk kategori anggaran bulan ini.</p>
                    <a href="{{ route('transactions.create') }}" aria-label="Tambah transaksi pengeluaran" class="mt-2 inline-flex min-h-11 items-center rounded-2xl bg-white px-3 py-2 text-xs font-bold text-indigo-600 shadow-sm">Tambah Transaksi</a>
                </div>
            @endif

            <div class="space-y-3">
                @foreach ($budgets as $item)
                    @php
                        $budget = $item['budget'];
                        $remainingLabel = $item['remaining'] >= 0
                            ? 'Rp '.number_format($item['remaining'], 0, ',', '.')
                            : 'Lewat Rp '.number_format(abs($item['remaining']), 0, ',', '.');
                    @endphp

                    <article wire:key="budget-{{ $budget->id }}" class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-white/60">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-lg" style="background-color: {{ $budget->category?->color ?? '#f1f5f9' }}22; color: {{ $budget->category?->color ?? '#64748b' }};">
                                    {{ $budget->category?->icon ?: '💸' }}
                                </span>
                                <div class="min-w-0 leading-tight">
                                    <p class="truncate text-base font-extrabold text-slate-800">{{ $budget->category?->name ?? 'Tanpa kategori' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-400">{{ $selectedMonthLabel }}</p>
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <span class="rounded-full px-3 py-1 text-[10px] font-bold {{ $item['statusClass'] }}">{{ $item['statusLabel'] }}</span>
                                @if ($editingBudgetId !== $budget->id)
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="startEdit({{ $budget->id }})" class="inline-flex min-h-9 items-center rounded-2xl bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-600">Edit</button>
                                        <button type="button" wire:click="deleteBudget({{ $budget->id }})" wire:confirm="Yakin ingin menghapus anggaran ini? Transaksi yang sudah tercatat tidak akan ikut terhapus." class="inline-flex min-h-9 items-center rounded-2xl bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600">Hapus</button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($editingBudgetId === $budget->id)
                            <form wire:submit="updateBudget" class="mt-4 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                                <label for="edit-budget-{{ $budget->id }}" class="block text-xs font-bold uppercase tracking-wide text-indigo-500">Nominal Anggaran</label>
                                <input id="edit-budget-{{ $budget->id }}" type="number" min="0" step="1000" inputmode="numeric" wire:model="editAmount" class="mt-2 block min-h-12 w-full rounded-2xl border-indigo-100 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('editAmount')
                                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                                @enderror

                                <div class="mt-3 flex gap-2">
                                    <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-indigo-500/30">Simpan</button>
                                    <button type="button" wire:click="cancelEdit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm">Batal</button>
                                </div>
                            </form>
                        @endif

                        <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                            <div class="rounded-2xl bg-slate-50 px-3 py-2">
                                <p class="text-slate-400">Anggaran</p>
                                <p class="mt-1 break-words font-extrabold leading-tight text-slate-800 tabular-nums">Rp {{ number_format($item['amount'], 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-3 py-2">
                                <p class="text-slate-400">Terpakai</p>
                                <p class="mt-1 break-words font-extrabold leading-tight text-rose-600 tabular-nums">Rp {{ number_format($item['used'], 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-3 py-2">
                                <p class="text-slate-400">Sisa</p>
                                <p class="mt-1 break-words font-extrabold leading-tight tabular-nums {{ $item['remaining'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $remainingLabel }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-500">{{ $item['percentage'] }}% terpakai</span>
                                <span class="text-slate-400">{{ $item['statusLabel'] }}</span>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-label="Pemakaian anggaran {{ $budget->category?->name ?? 'Tanpa kategori' }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $item['barPercentage'] }}">
                                <div class="h-full rounded-full {{ $item['barClass'] }}" style="width: {{ $item['barPercentage'] }}%"></div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
