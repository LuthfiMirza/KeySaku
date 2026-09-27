<div class="space-y-5">
    @if (session('budget_status'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-700 shadow-sm flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>{{ session('budget_status') }}</span>
        </div>
    @endif

    @php
        $summaryIsOverLimit = $summary['totalRemaining'] < 0;
        $budgetPercent = $summary['totalBudget'] > 0
            ? min(100, round(($summary['totalUsed'] / $summary['totalBudget']) * 100))
            : 0;

        $healthStatus = 'Sangat Aman';
        $healthBadgeClass = 'bg-emerald-50 text-emerald-600 border border-emerald-200/60';
        if ($summary['overLimitCount'] > 0 || $summaryIsOverLimit) {
            $healthStatus = 'Lewat Batas';
            $healthBadgeClass = 'bg-rose-50 text-rose-600 border border-rose-200/60';
        } elseif ($budgetPercent >= 80) {
            $healthStatus = 'Mendekati Batas';
            $healthBadgeClass = 'bg-amber-50 text-amber-600 border border-amber-200/60';
        }
    @endphp

    {{-- Overall Budget Health Card --}}
    <section class="card-porcelain rounded-[32px] p-6 relative overflow-hidden">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-bold text-[#8E8E93] uppercase tracking-wider">Kesehatan Anggaran</p>
                <h3 class="text-base font-extrabold text-[#121316] mt-0.5">Ringkasan Anggaran</h3>
                <p class="text-[11px] text-[#8E8E93] mt-0.5">Total plafon untuk {{ $selectedMonthLabel }}.</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $healthBadgeClass }}">
                {{ $healthStatus }}
            </span>
        </div>

        {{-- Main Percentage & Progress --}}
        <div class="mt-4 flex items-baseline gap-2">
            <span class="text-4xl font-extrabold tracking-tight text-[#121316] tabular-nums">{{ $budgetPercent }}%</span>
            <span class="text-xs font-semibold text-[#8E8E93]">terpakai dari plafon</span>
        </div>

        {{-- Horizontal dual-color progress bar --}}
        <div class="mt-3 h-3 w-full rounded-full bg-slate-100 overflow-hidden relative">
            <div class="h-full rounded-full transition-all duration-500 {{ $summaryIsOverLimit ? 'bg-gradient-to-r from-amber-400 to-rose-500' : 'bg-gradient-to-r from-[#10B981] to-[#34D399]' }}"
                 style="width: {{ $budgetPercent }}%"></div>
        </div>

        {{-- Breakdown: Terpakai, Sisa Kuota, Total Plafon --}}
        <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
            <div class="rounded-2xl bg-[#FCF8F8] p-3 border border-[#F0EBEB]/80">
                <p class="text-[11px] text-[#8E8E93] font-medium">Terpakai</p>
                <x-money :value="$summary['totalUsed']" class="mt-1 block text-sm font-extrabold text-rose-500 tabular-nums" />
            </div>
            <div class="rounded-2xl bg-[#FCF8F8] p-3 border border-[#F0EBEB]/80">
                <p class="text-[11px] text-[#8E8E93] font-medium">Sisa Kuota / Sisa</p>
                <x-money
                    :value="abs($summary['totalRemaining'])"
                    :prefix="$summaryIsOverLimit ? 'Lewat ' : ''"
                    class="mt-1 block text-sm font-extrabold tabular-nums {{ $summaryIsOverLimit ? 'text-rose-500' : 'text-emerald-500' }}"
                />
            </div>
            <div class="rounded-2xl bg-[#FCF8F8] p-3 border border-[#F0EBEB]/80">
                <p class="text-[11px] text-[#8E8E93] font-medium">Total Anggaran</p>
                <x-money :value="$summary['totalBudget']" class="mt-1 block text-sm font-extrabold text-[#121316] tabular-nums" />
            </div>
            <div class="rounded-2xl bg-[#FCF8F8] p-3 border border-[#F0EBEB]/80">
                <p class="text-[11px] text-[#8E8E93] font-medium">Lewat Batas</p>
                <p class="mt-1 text-sm font-extrabold text-[#121316]">{{ $summary['overLimitCount'] }} kategori</p>
            </div>
        </div>
    </section>

    {{-- Horizontal Savings Goals Carousel (Pot Tabungan) --}}
    <section class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <div>
                <h3 class="text-sm font-bold text-[#121316] tracking-tight">Pot Tabungan</h3>
                <p class="text-[11px] text-[#8E8E93]">Target tabungan keluarga yang sedang berjalan.</p>
            </div>
            <a href="{{ route('savings.index') }}" class="text-xs font-semibold text-[#FF6B6B] hover:text-[#FF8E53] transition inline-flex items-center gap-1">
                <span>Kelola</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </a>
        </div>

        <div class="flex gap-3.5 overflow-x-auto pb-2 scrollbar-none snap-x">
            {{-- Pot Card 1: Dana Darurat --}}
            <div class="min-w-[260px] snap-start card-porcelain rounded-[26px] p-4.5 border border-[#F0EBEB] relative">
                <div class="flex items-start justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shadow-sm">
                        🛡️
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-extrabold">
                        75%
                    </span>
                </div>
                <h4 class="text-sm font-extrabold text-[#121316] mt-3">Dana Darurat</h4>
                <p class="text-xs text-[#8E8E93] mt-0.5 tabular-nums">Rp 15.000.000 / Rp 20.000.000</p>
                <div class="mt-3 h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-500" style="width: 75%"></div>
                </div>
                <a href="{{ route('savings.index') }}" class="mt-4 w-full py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-[#121316] text-xs font-bold flex items-center justify-center border border-[#F0EBEB] transition active:scale-95">
                    Setor Dana
                </a>
            </div>

            {{-- Pot Card 2: Liburan Impian --}}
            <div class="min-w-[260px] snap-start card-porcelain rounded-[26px] p-4.5 border border-[#F0EBEB] relative">
                <div class="flex items-start justify-between">
                    <span class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-lg shadow-sm">
                        ✈️
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-[#FF6B6B] text-[10px] font-extrabold">
                        45%
                    </span>
                </div>
                <h4 class="text-sm font-extrabold text-[#121316] mt-3">Liburan Impian</h4>
                <p class="text-xs text-[#8E8E93] mt-0.5 tabular-nums">Rp 4.500.000 / Rp 10.000.000</p>
                <div class="mt-3 h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-[#FF6B6B] to-[#FFA07A]" style="width: 45%"></div>
                </div>
                <a href="{{ route('savings.index') }}" class="mt-4 w-full py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-[#121316] text-xs font-bold flex items-center justify-center border border-[#F0EBEB] transition active:scale-95">
                    Setor Dana
                </a>
            </div>
        </div>
    </section>

    {{-- Category Progress Cards Section --}}
    <section class="space-y-3">
        <div class="flex items-center justify-between gap-3 px-1">
            <div>
                <h3 class="text-sm font-bold text-[#121316] tracking-tight">Progress Anggaran</h3>
                <p class="mt-0.5 text-[11px] text-[#8E8E93]">Pantau pemakaian anggaran per kategori untuk {{ $selectedMonthLabel }}.</p>
            </div>
            <span class="rounded-full bg-[#121316] text-white px-3 py-1 text-xs font-bold shadow-sm" aria-label="Jumlah anggaran bulan ini: {{ $budgets->count() }}">
                {{ $budgets->count() }}
            </span>
        </div>

        @if ($budgets->isEmpty())
            <div class="card-porcelain rounded-[28px] p-6 text-center">
                <p class="text-4xl mb-3">💰</p>
                <h3 class="text-sm font-bold text-[#121316]">Belum ada anggaran untuk bulan ini.</h3>
                <p class="mt-1 text-xs text-[#8E8E93]">Buat anggaran per kategori agar KeySaku bisa memantau batas pengeluaranmu.</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <a href="#buat-anggaran" aria-label="Buat anggaran untuk bulan yang dipilih"
                       class="inline-flex min-h-11 items-center rounded-2xl bg-[#121316] px-5 py-2 text-xs font-bold text-white shadow-md active:scale-95 transition">
                        Buat Anggaran
                    </a>
                    <a href="{{ route('transactions.index') }}" aria-label="Lihat riwayat transaksi"
                       class="inline-flex min-h-11 items-center rounded-2xl bg-slate-100 px-4 py-2 text-xs font-bold text-[#121316] active:scale-95 transition">
                        Lihat Riwayat
                    </a>
                </div>
            </div>
        @else
            @if ((float) $summary['totalUsed'] <= 0)
                <div class="rounded-2xl border border-amber-200/60 bg-amber-50/60 p-4">
                    <p class="text-xs font-medium text-amber-800">Belum ada pengeluaran untuk kategori anggaran bulan ini.</p>
                    <a href="{{ route('transactions.create') }}" aria-label="Tambah transaksi pengeluaran"
                       class="mt-2 inline-flex min-h-10 items-center rounded-xl bg-white px-3.5 py-1.5 text-xs font-bold text-[#121316] border border-[#F0EBEB] shadow-sm">
                        Tambah Transaksi
                    </a>
                </div>
            @endif

            <div class="space-y-3">
                @foreach ($budgets as $item)
                    @php
                        $budget = $item['budget'];
                        $itemIsOverLimit = $item['remaining'] < 0;
                    @endphp

                    <article wire:key="budget-{{ $budget->id }}" class="card-porcelain rounded-[28px] p-4.5 transition hover:shadow-md">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-xl shadow-sm"
                                      style="background-color: {{ $budget->category?->color ?? '#F0EBEB' }}22; color: {{ $budget->category?->color ?? '#121316' }};">
                                    {{ $budget->category?->icon ?: '💸' }}
                                </span>
                                <div class="min-w-0 leading-tight">
                                    <p class="truncate text-base font-extrabold text-[#121316]">{{ $budget->category?->name ?? 'Tanpa kategori' }}</p>
                                    <p class="mt-0.5 text-xs text-[#8E8E93]">{{ $selectedMonthLabel }}</p>
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-1.5">
                                <span class="rounded-full px-3 py-1 text-[10px] font-bold {{ $item['statusClass'] }}">{{ $item['statusLabel'] }}</span>
                                @if ($editingBudgetId !== $budget->id)
                                    <div class="flex gap-1.5">
                                        <button type="button" wire:click="startEdit({{ $budget->id }})" class="inline-flex min-h-8 items-center rounded-xl bg-slate-100 px-3 py-1 text-[11px] font-bold text-[#121316] hover:bg-slate-200 transition">Edit</button>
                                        <button type="button" wire:click="deleteBudget({{ $budget->id }})" wire:confirm="Yakin ingin menghapus anggaran ini? Transaksi yang sudah tercatat tidak akan ikut terhapus." class="inline-flex min-h-8 items-center rounded-xl bg-rose-50 px-3 py-1 text-[11px] font-bold text-rose-600 hover:bg-rose-100 transition">Hapus</button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($editingBudgetId === $budget->id)
                            <form wire:submit="updateBudget" class="mt-4 rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4">
                                <label for="edit-budget-{{ $budget->id }}" class="block text-xs font-bold uppercase tracking-wide text-indigo-600">Nominal Anggaran</label>
                                <input id="edit-budget-{{ $budget->id }}" type="number" min="0" step="1000" inputmode="numeric" wire:model="editAmount" class="mt-2 block min-h-12 w-full rounded-2xl border-indigo-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('editAmount')
                                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                                @enderror

                                <div class="mt-3 flex gap-2">
                                    <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl bg-[#121316] px-4 py-2 text-xs font-bold text-white shadow-md active:scale-95 transition">Simpan</button>
                                    <button type="button" wire:click="cancelEdit" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-2xl bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm border border-slate-200">Batal</button>
                                </div>
                            </form>
                        @endif

                        <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                            <div class="rounded-2xl bg-[#FCF8F8] px-3 py-2.5 border border-[#F0EBEB]/60">
                                <p class="text-[#8E8E93] text-[11px]">Anggaran</p>
                                <x-money :value="$item['amount']" class="mt-1 block break-words font-extrabold leading-tight text-[#121316] tabular-nums" />
                            </div>
                            <div class="rounded-2xl bg-[#FCF8F8] px-3 py-2.5 border border-[#F0EBEB]/60">
                                <p class="text-[#8E8E93] text-[11px]">Terpakai</p>
                                <x-money :value="$item['used']" class="mt-1 block break-words font-extrabold leading-tight text-rose-500 tabular-nums" />
                            </div>
                            <div class="rounded-2xl bg-[#FCF8F8] px-3 py-2.5 border border-[#F0EBEB]/60">
                                <p class="text-[#8E8E93] text-[11px]">Sisa</p>
                                <x-money
                                    :value="abs($item['remaining'])"
                                    :prefix="$itemIsOverLimit ? 'Lewat ' : ''"
                                    class="mt-1 block break-words font-extrabold leading-tight tabular-nums {{ $itemIsOverLimit ? 'text-rose-500' : 'text-emerald-500' }}"
                                />
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between text-xs font-bold">
                                <span class="text-[#121316]">{{ $item['percentage'] }}% terpakai</span>
                                <span class="text-[#8E8E93]">{{ $item['statusLabel'] }}</span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-label="Pemakaian anggaran {{ $budget->category?->name ?? 'Tanpa kategori' }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $item['barPercentage'] }}">
                                <div class="h-full rounded-full transition-all duration-500 {{ $item['barClass'] }}" style="width: {{ $item['barPercentage'] }}%"></div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Form Buat Anggaran Baru --}}
    <section id="buat-anggaran" class="card-porcelain rounded-[32px] p-6 scroll-mt-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-[#121316]">Buat Anggaran</h3>
                <p id="budget-period-helper" class="mt-0.5 text-xs text-[#8E8E93]">Pilih bulan dan tahun untuk melihat atau membuat anggaran.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-[#121316]">{{ $selectedMonthLabel }}</span>
        </div>

        <form wire:submit="save" class="mt-5 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="month" class="block text-xs font-bold uppercase tracking-wider text-[#8E8E93]">Bulan</label>
                    <select id="month" wire:model.live="month" aria-describedby="budget-period-helper" class="mt-1.5 block min-h-12 w-full rounded-2xl border-[#F0EBEB] bg-[#FCF8F8] px-4 py-3 text-sm font-medium focus:border-[#121316] focus:ring-0">
                        @foreach (range(1, 12) as $monthOption)
                            <option value="{{ $monthOption }}">{{ now()->setDate((int) $year, $monthOption, 1)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    @error('month')
                        <p class="mt-1.5 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="year" class="block text-xs font-bold uppercase tracking-wider text-[#8E8E93]">Tahun</label>
                    <input id="year" type="number" wire:model.live="year" aria-describedby="budget-period-helper" class="mt-1.5 block min-h-12 w-full rounded-2xl border-[#F0EBEB] bg-[#FCF8F8] px-4 py-3 text-sm font-medium focus:border-[#121316] focus:ring-0">
                    @error('year')
                        <p class="mt-1.5 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200/50 bg-amber-50/50 p-4">
                <p class="text-xs font-medium text-amber-800">Gunakan anggaran bulan lalu sebagai dasar bulan ini.</p>
                <button type="button" wire:click="copyPreviousMonthBudgets" class="mt-2.5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-white px-4 py-2 text-xs font-bold text-[#121316] border border-[#F0EBEB] shadow-sm hover:bg-slate-50 transition active:scale-98">
                    Salin dari Bulan Lalu
                </button>
            </div>

            <div>
                <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-[#8E8E93]">Kategori</label>
                <select id="category_id" wire:model="category_id" class="mt-1.5 block min-h-12 w-full rounded-2xl border-[#F0EBEB] bg-[#FCF8F8] px-4 py-3 text-sm font-medium focus:border-[#121316] focus:ring-0">
                    <option value="">Pilih kategori</option>
                    @foreach ($expenseCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1.5 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-[#8E8E93]">Nominal Anggaran</label>
                <input id="amount" type="number" min="0" step="1000" wire:model="amount" inputmode="numeric" class="mt-1.5 block min-h-12 w-full rounded-2xl border-[#F0EBEB] bg-[#FCF8F8] px-4 py-3 text-sm font-semibold focus:border-[#121316] focus:ring-0" placeholder="Contoh: 1500000">
                @error('amount')
                    <p class="mt-1.5 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            @if ($expenseCategories->isEmpty())
                <div class="rounded-2xl bg-amber-50 p-4 text-xs font-medium text-amber-800">
                    Tambahkan kategori pengeluaran terlebih dahulu sebelum membuat anggaran.
                </div>
            @endif

            <button type="submit" @disabled($expenseCategories->isEmpty()) class="min-h-12 w-full rounded-2xl px-4 py-3 text-sm font-bold transition {{ $expenseCategories->isEmpty() ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-[#121316] text-white shadow-md active:scale-98 hover:bg-black' }}">
                Simpan Anggaran
            </button>
        </form>
    </section>
</div>

