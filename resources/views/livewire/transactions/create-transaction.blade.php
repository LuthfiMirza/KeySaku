<div class="space-y-5">
    @if (session('transaction_status'))
        <div class="rounded-2xl bg-emerald-400/15 border border-emerald-400/25 px-4 py-3 text-sm font-medium text-emerald-300">
            {{ session('transaction_status') }}
        </div>
    @endif

    <section class="bg-white/10 backdrop-blur-sm rounded-3xl p-4 shadow-sm border border-white/15">
        <div class="mb-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-300">Input Manual</p>
            <h2 class="text-lg font-bold text-white">Tambah transaksi</h2>
            <p class="mt-1 text-xs text-white/40">Catat pemasukan atau pengeluaran dengan cepat.</p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-white/70">Jenis transaksi</label>
                <div class="mt-2 grid grid-cols-2 gap-2 rounded-2xl bg-white/10 border border-white/15 p-1">
                    <button type="button" wire:click="$set('type', 'expense')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'expense' ? 'bg-gradient-to-r from-rose-500 to-pink-600 text-white shadow-md shadow-rose-900/30' : 'text-white/50 hover:text-white/70' }}">
                        Pengeluaran
                    </button>
                    <button type="button" wire:click="$set('type', 'income')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'income' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-900/30' : 'text-white/50 hover:text-white/70' }}">
                        Pemasukan
                    </button>
                </div>
                @error('type')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-semibold text-white/70">Nominal</label>
                <div class="mt-2 flex rounded-2xl border border-white/15 bg-white/10 shadow-sm focus-within:border-indigo-400/60 focus-within:bg-white/15">
                    <span class="flex items-center px-4 text-sm font-bold text-white/40">Rp</span>
                    <input id="amount" type="number" min="0" step="0.01" wire:model="amount" class="block w-full rounded-r-2xl border-0 bg-transparent px-2 py-3 text-sm text-white placeholder-white/25 focus:ring-0 outline-none" placeholder="50000">
                </div>
                @error('amount')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-semibold text-white/70">Kategori</label>
                @if ($categories->isEmpty())
                    <div class="mt-2 rounded-2xl border border-amber-400/25 bg-amber-400/10 p-4">
                        <p class="text-sm font-bold text-amber-200">Belum ada kategori {{ $type === 'expense' ? 'pengeluaran' : 'pemasukan' }}.</p>
                        <p class="mt-1 text-xs text-amber-300">Buat kategori dulu agar transaksi bisa dicatat rapi.</p>
                        <a href="{{ route('categories') }}" class="mt-3 inline-flex rounded-full bg-indigo-500/30 border border-indigo-400/30 px-4 py-2 text-xs font-bold text-indigo-300">
                            Atur kategori
                        </a>
                    </div>
                @else
                    <select id="category_id" wire:model="category_id" class="mt-2 block w-full rounded-2xl border-white/15 bg-white/10 px-4 py-3 text-sm text-white shadow-sm focus:border-indigo-400/60 focus:bg-white/15 focus:ring-indigo-500 [color-scheme:dark]">
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
                <label for="transaction_date" class="block text-sm font-semibold text-white/70">Tanggal</label>
                <input id="transaction_date" type="date" wire:model="transaction_date" class="mt-2 block w-full rounded-2xl border-white/15 bg-white/10 px-4 py-3 text-sm text-white shadow-sm focus:border-indigo-400/60 focus:bg-white/15 focus:ring-indigo-500 [color-scheme:dark]">
                @error('transaction_date')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="note" class="block text-sm font-semibold text-white/70">Catatan <span class="font-normal text-white/40">opsional</span></label>
                <textarea id="note" wire:model="note" rows="3" class="mt-2 block w-full rounded-2xl border-white/15 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/25 shadow-sm focus:border-indigo-400/60 focus:bg-white/15 focus:ring-indigo-500" placeholder="Contoh: makan siang keluarga"></textarea>
                @error('note')
                    <p class="mt-2 text-xs font-medium text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-900/30 transition active:scale-[0.99] disabled:opacity-60" @disabled($categories->isEmpty())>
                Simpan Transaksi
            </button>
        </form>
    </section>

    <a href="{{ route('dashboard') }}" class="block text-center text-sm font-bold text-indigo-300 hover:text-indigo-200">
        Kembali ke Beranda
    </a>
</div>
