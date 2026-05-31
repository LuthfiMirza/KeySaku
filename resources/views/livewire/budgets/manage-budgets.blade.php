<div class="space-y-5 lg:max-w-5xl lg:mx-auto">
    @if (session('budget_status'))
        <div class="rounded-2xl border border-emerald-100 bg-[#1d9e75]/10 px-4 py-3 text-sm font-bold text-emerald-700 shadow-sm">
            {{ session('budget_status') }}
        </div>
    @endif

    @php
        $summaryRemainingLabel = $summary['totalRemaining'] >= 0
            ? 'Rp '.number_format($summary['totalRemaining'], 0, ',', '.')
            : 'Lewat Rp '.number_format(abs($summary['totalRemaining']), 0, ',', '.');
    @endphp

    <section class="rounded-3xl p-5" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold" style="color:#FFFFFF !important;">Ringkasan Anggaran</h3>
                <p class="mt-1 text-xs" style="color:#64748B !important;">Total anggaran untuk {{ $selectedMonthLabel }}.</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-bold" style="background:rgba(163,230,53,0.15) !important;color:#A3E635 !important;border-radius:999px !important;padding:4px 12px !important;font-size:11px !important;">{{ $selectedMonthLabel }}</span>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                <p class="text-xs" style="color:#64748B !important;font-size:11px !important;">Total Anggaran</p>
                <p class="mt-1 text-sm font-extrabold tabular-nums" style="color:#FFFFFF !important;font-weight:700 !important;">Rp {{ number_format($summary['totalBudget'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                <p class="text-xs" style="color:#64748B !important;font-size:11px !important;">Terpakai</p>
                <p class="mt-1 text-sm font-extrabold tabular-nums" style="color:#F87171 !important;font-weight:700 !important;">Rp {{ number_format($summary['totalUsed'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                <p class="text-xs" style="color:#64748B !important;font-size:11px !important;">Sisa</p>
                <p class="mt-1 text-sm font-extrabold tabular-nums" style="color:{{ $summary['totalRemaining'] >= 0 ? '#A3E635' : '#F87171' }} !important;font-weight:700 !important;">{{ $summaryRemainingLabel }}</p>
            </div>
            <div class="rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                <p class="text-xs" style="color:#64748B !important;font-size:11px !important;">Lewat Batas</p>
                <p class="mt-1 text-sm font-extrabold" style="color:#F87171 !important;font-weight:700 !important;">{{ $summary['overLimitCount'] }} kategori</p>
            </div>
        </div>
    </section>

    <section id="buat-anggaran" class="rounded-3xl p-5 scroll-mt-6" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold" style="color:#FFFFFF !important;">Buat Anggaran</h3>
                <p id="budget-period-helper" class="mt-1 max-w-[14rem] text-xs leading-relaxed" style="color:#64748B !important;">Pilih bulan dan tahun untuk melihat atau membuat anggaran.</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-bold" style="background:rgba(163,230,53,0.15) !important;color:#A3E635 !important;border-radius:999px !important;padding:4px 12px !important;font-size:11px !important;">{{ $selectedMonthLabel }}</span>
        </div>

        <form wire:submit="save" class="mt-5 space-y-5">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="month" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Bulan</label>
                    <select id="month" wire:model.live="month" aria-describedby="budget-period-helper" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                        @foreach (range(1, 12) as $monthOption)
                            <option value="{{ $monthOption }}">{{ now()->setDate((int) $year, $monthOption, 1)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    @error('month')
                        <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="year" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Tahun</label>
                    <input id="year" type="number" wire:model.live="year" aria-describedby="budget-period-helper" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                    @error('year')
                        <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                <p class="text-xs font-medium" style="color:#64748B !important;">Gunakan anggaran bulan lalu sebagai dasar bulan ini.</p>
                <button type="button" wire:click="copyPreviousMonthBudgets" class="mt-3 inline-flex min-h-11 w-full items-center justify-center text-xs" style="background:#1A2B52 !important;border:1px solid rgba(163,230,53,0.2) !important;color:#A3E635 !important;border-radius:14px !important;padding:14px !important;font-weight:600 !important;">
                    Salin dari Bulan Lalu
                </button>
            </div>

            <div>
                <label for="category_id" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Kategori</label>
                <select id="category_id" wire:model="category_id" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                    <option value="">Pilih kategori</option>
                    @foreach ($expenseCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-xs font-bold uppercase tracking-wide" style="color:#64748B !important;font-size:11px !important;letter-spacing:0.08em !important;">Nominal Anggaran</label>
                <input id="amount" type="number" min="0" step="1000" wire:model="amount" inputmode="numeric" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;" placeholder="Contoh: 1500000">
                @error('amount')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            @if ($expenseCategories->isEmpty())
                <div class="rounded-2xl bg-amber-50 px-4 py-3 text-xs font-medium text-amber-700">
                    Tambahkan kategori pengeluaran terlebih dahulu sebelum membuat anggaran.
                </div>
            @endif

            <button type="submit" @disabled($expenseCategories->isEmpty()) class="min-h-12 w-full rounded-2xl px-4 py-3 text-sm font-bold transition {{ $expenseCategories->isEmpty() ? 'bg-slate-200 text-[#64748B] cursor-not-allowed' : 'bg-[#A3E635] text-[#0A1628] shadow-lg shadow-lime-500/20' }}">
                Simpan Anggaran
            </button>
        </form>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold" style="color:#FFFFFF !important;">Progress Anggaran</h3>
                <p class="mt-1 text-xs" style="color:#64748B !important;">Pantau pemakaian anggaran untuk {{ $selectedMonthLabel }}.</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-bold" style="background:rgba(163,230,53,0.15) !important;color:#A3E635 !important;border-radius:999px !important;padding:4px 12px !important;font-size:11px !important;" aria-label="Jumlah anggaran bulan ini: {{ $budgets->count() }}">{{ $budgets->count() }}</span>
        </div>

        @if ($budgets->isEmpty())
            <div class="rounded-3xl p-6 text-center" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                <p class="text-4xl mb-3">💰</p>
                <h3 class="text-sm font-bold" style="color:#FFFFFF !important;">Belum ada anggaran untuk bulan ini.</h3>
                <p class="mt-1 text-xs" style="color:#64748B !important;">Buat anggaran per kategori agar KeySaku bisa memantau batas pengeluaranmu.</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <a href="#buat-anggaran" aria-label="Buat anggaran untuk bulan yang dipilih" class="inline-flex min-h-11 items-center rounded-2xl px-4 py-2 text-xs font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">Buat Anggaran</a>
                    <a href="{{ route('transactions.index') }}" aria-label="Lihat riwayat transaksi" class="inline-flex min-h-11 items-center rounded-2xl bg-[#1A2B52] px-4 py-2 text-xs font-bold text-white">Lihat Riwayat</a>
                </div>
            </div>
        @else
            @if ((float) $summary['totalUsed'] <= 0)
                <div class="rounded-2xl px-4 py-3" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                    <p class="text-xs font-medium" style="color:#64748B !important;">Belum ada pengeluaran untuk kategori anggaran bulan ini.</p>
                    <a href="{{ route('transactions.create') }}" aria-label="Tambah transaksi pengeluaran" class="mt-2 inline-flex min-h-11 items-center rounded-2xl px-3 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(163,230,53,0.2) !important;color:#A3E635 !important;">Tambah Transaksi</a>
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

                    <article wire:key="budget-{{ $budget->id }}" class="rounded-2xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-lg" style="background-color: {{ $budget->category?->color ?? '#f1f5f9' }}22; color: {{ $budget->category?->color ?? '#64748b' }};">
                                    {{ $budget->category?->icon ?: '💸' }}
                                </span>
                                <div class="min-w-0 leading-tight">
                                    <p class="truncate text-base font-extrabold text-white">{{ $budget->category?->name ?? 'Tanpa kategori' }}</p>
                                    <p class="mt-0.5 text-xs text-[#64748B]">{{ $selectedMonthLabel }}</p>
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <span class="rounded-full px-3 py-1 text-[10px] font-bold {{ $item['statusClass'] }}">{{ $item['statusLabel'] }}</span>
                                @if ($editingBudgetId !== $budget->id)
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="startEdit({{ $budget->id }})" class="inline-flex min-h-9 items-center rounded-2xl bg-[#A3E635]/10 px-3 py-1.5 text-xs font-bold text-[#A3E635]">Edit</button>
                                        <button type="button" wire:click="deleteBudget({{ $budget->id }})" wire:confirm="Yakin ingin menghapus anggaran ini? Transaksi yang sudah tercatat tidak akan ikut terhapus." class="inline-flex min-h-9 items-center rounded-2xl bg-[#e24b4a]/10 px-3 py-1.5 text-xs font-bold text-[#F87171]">Hapus</button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($editingBudgetId === $budget->id)
                            <form wire:submit="updateBudget" class="mt-4 rounded-2xl border border-indigo-100 bg-[#A3E635]/10 px-4 py-3">
                                <label for="edit-budget-{{ $budget->id }}" class="block text-xs font-bold uppercase tracking-wide text-[#A3E635]">Nominal Anggaran</label>
                                <input id="edit-budget-{{ $budget->id }}" type="number" min="0" step="1000" inputmode="numeric" wire:model="editAmount" class="mt-2 block min-h-12 w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;border-radius:12px !important;color:#FFFFFF !important;padding:12px 16px !important;">
                                @error('editAmount')
                                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                                @enderror

                                <div class="mt-3 flex gap-2">
                                    <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl px-4 py-2 text-xs font-bold shadow-lg shadow-lime-500/20" style="background:#A3E635 !important;color:#0A1628 !important;">Simpan</button>
                                    <button type="button" wire:click="cancelEdit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl px-4 py-2 text-xs font-bold shadow-sm" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;">Batal</button>
                                </div>
                            </form>
                        @endif

                        <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                            <div class="rounded-2xl px-3 py-2" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                                <p class="text-[#64748B]">Anggaran</p>
                                <p class="mt-1 break-words font-extrabold leading-tight text-white tabular-nums">Rp {{ number_format($item['amount'], 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl px-3 py-2" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                                <p class="text-[#64748B]">Terpakai</p>
                                <p class="mt-1 break-words font-extrabold leading-tight text-[#F87171] tabular-nums">Rp {{ number_format($item['used'], 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl px-3 py-2" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.06) !important;border-radius:14px !important;">
                                <p class="text-[#64748B]">Sisa</p>
                                <p class="mt-1 break-words font-extrabold leading-tight tabular-nums {{ $item['remaining'] >= 0 ? 'text-[#A3E635]' : 'text-[#F87171]' }}">{{ $remainingLabel }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between text-xs font-bold">
                                <span class="text-[#64748B]">{{ $item['percentage'] }}% terpakai</span>
                                <span class="text-[#64748B]">{{ $item['statusLabel'] }}</span>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-[#1A2B52]" role="progressbar" aria-label="Pemakaian anggaran {{ $budget->category?->name ?? 'Tanpa kategori' }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $item['barPercentage'] }}">
                                <div class="h-full rounded-full {{ $item['barClass'] }}" style="width: {{ $item['barPercentage'] }}%"></div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
