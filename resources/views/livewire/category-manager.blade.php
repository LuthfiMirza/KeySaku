<div class="space-y-5">
    @if (session('category_status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-100 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('category_status') }}
        </div>
    @endif

    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-4 shadow-sm border border-white/60">
        <div class="mb-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-500">
                {{ $editingCategory ? 'Edit Kategori' : 'Kategori Baru' }}
            </p>
            <h2 class="text-lg font-bold text-slate-800">Atur kategori transaksi</h2>
            <p class="mt-1 text-xs text-slate-400">Kategori ini akan dipakai untuk input manual dan suara nanti.</p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">Nama kategori</label>
                <input id="name" type="text" wire:model="name" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Makanan">
                @error('name')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700">Jenis</label>
                <div class="mt-2 grid grid-cols-2 gap-2 rounded-2xl bg-slate-100 p-1">
                    <button type="button" wire:click="$set('type', 'expense')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'expense' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-500' }}">
                        Pengeluaran
                    </button>
                    <button type="button" wire:click="$set('type', 'income')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'income' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-500' }}">
                        Pemasukan
                    </button>
                </div>
                @error('type')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="icon" class="block text-sm font-semibold text-slate-700">Ikon</label>
                    <input id="icon" type="text" wire:model="icon" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="🍜">
                    @error('icon')
                        <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="color" class="block text-sm font-semibold text-slate-700">Warna</label>
                    <input id="color" type="text" wire:model="color" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="#6366f1">
                    @error('color')
                        <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 transition active:scale-[0.99]">
                    {{ $editingCategory ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                </button>

                @if ($editingCategory)
                    <button type="button" wire:click="resetForm" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-500">
                        Batal
                    </button>
                @endif
            </div>
        </form>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-700">Kategori Pengeluaran</h2>
            <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-500">{{ $expenseCategories->count() }}</span>
        </div>

        <div class="space-y-2">
            @foreach ($expenseCategories as $category)
                <div wire:key="expense-{{ $category->id }}" class="bg-white/80 backdrop-blur-sm rounded-2xl p-3 shadow-sm border border-white/60 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-lg" style="background-color: {{ $category->color ?? '#f1f5f9' }}22; color: {{ $category->color ?? '#64748b' }};">
                            {{ $category->icon ?: '💸' }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $category->name }}</p>
                            <p class="text-xs text-slate-400">Pengeluaran</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="edit({{ $category->id }})" class="text-xs font-bold text-indigo-600">Edit</button>
                        <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Hapus kategori ini?" class="text-xs font-bold text-rose-500">Hapus</button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-700">Kategori Pemasukan</h2>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-500">{{ $incomeCategories->count() }}</span>
        </div>

        <div class="space-y-2">
            @foreach ($incomeCategories as $category)
                <div wire:key="income-{{ $category->id }}" class="bg-white/80 backdrop-blur-sm rounded-2xl p-3 shadow-sm border border-white/60 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-lg" style="background-color: {{ $category->color ?? '#f1f5f9' }}22; color: {{ $category->color ?? '#64748b' }};">
                            {{ $category->icon ?: '💰' }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $category->name }}</p>
                            <p class="text-xs text-slate-400">Pemasukan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="edit({{ $category->id }})" class="text-xs font-bold text-indigo-600">Edit</button>
                        <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Hapus kategori ini?" class="text-xs font-bold text-rose-500">Hapus</button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
