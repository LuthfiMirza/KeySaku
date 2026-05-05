<div class="space-y-5">
    @if (session('transaction_status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-100 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('transaction_status') }}
        </div>
    @endif

    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-4 shadow-sm border border-white/60">
        <div class="mb-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-500">Perbarui Data</p>
            <h2 class="text-lg font-bold text-slate-800">Edit transaksi</h2>
            <p class="mt-1 text-xs text-slate-400">Ubah catatan pemasukan atau pengeluaranmu.</p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700">Jenis transaksi</label>
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

            <div>
                <label for="amount" class="block text-sm font-semibold text-slate-700">Nominal</label>
                <div class="mt-2 flex rounded-2xl border border-slate-200 bg-white/80 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                    <span class="flex items-center px-4 text-sm font-bold text-slate-400">Rp</span>
                    <input id="amount" type="number" min="0" step="0.01" wire:model="amount" class="block w-full rounded-r-2xl border-0 bg-transparent px-2 py-3 text-sm focus:ring-0" placeholder="50000">
                </div>
                @error('amount')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-semibold text-slate-700">Kategori</label>
                @if ($categories->isEmpty())
                    <div class="mt-2 rounded-2xl border border-amber-100 bg-amber-50 p-4">
                        <p class="text-sm font-bold text-amber-800">Belum ada kategori untuk tipe ini.</p>
                        <p class="mt-1 text-xs text-amber-700">Buat kategori dulu di Pengaturan.</p>
                        <a href="{{ route('categories') }}" class="mt-3 inline-flex rounded-xl bg-amber-500 px-3 py-2 text-xs font-bold text-white">
                            Atur kategori
                        </a>
                    </div>
                @else
                    <select id="category_id" wire:model="category_id" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                @endif
                @error('category_id')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="transaction_date" class="block text-sm font-semibold text-slate-700">Tanggal</label>
                <input id="transaction_date" type="date" wire:model="transaction_date" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('transaction_date')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="note" class="block text-sm font-semibold text-slate-700">Catatan <span class="font-normal text-slate-400">opsional</span></label>
                <textarea id="note" wire:model="note" rows="3" class="mt-2 block w-full rounded-2xl border-slate-200 bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Beli nasi goreng"></textarea>
                @error('note')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 transition active:scale-[0.99] disabled:opacity-60" @disabled($categories->isEmpty())>
                Simpan Perubahan
            </button>
        </form>
    </section>

    <section class="bg-white/80 backdrop-blur-sm rounded-3xl p-4 shadow-sm border border-white/60">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Hapus transaksi</h2>
                <p class="mt-1 text-xs text-slate-400">Data akan disembunyikan dari riwayat, bukan dihapus permanen.</p>
            </div>
            <button type="button" wire:click="delete" wire:confirm="Yakin ingin menghapus transaksi ini? Data akan disembunyikan dari riwayat, bukan dihapus permanen." class="shrink-0 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600">
                Hapus Transaksi
            </button>
        </div>
    </section>

    <a href="{{ route('transactions.index') }}" class="block text-center text-sm font-bold text-indigo-600">
        Kembali ke Riwayat Transaksi
    </a>
</div>
