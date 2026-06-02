<div class="space-y-5">
    @if (session('transaction_status'))
        <div class="rounded-2xl bg-[#A3E635]/10 border border-[#A3E635]/25 px-4 py-3 text-sm font-medium text-[#A3E635]">
            {{ session('transaction_status') }}
        </div>
    @endif

    <section class="rounded-3xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div class="mb-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-[#A3E635]">Perbarui Data</p>
            <h2 class="text-lg font-bold text-white">Edit transaksi</h2>
            <p class="mt-1 text-xs text-[#64748B]">Ubah catatan pemasukan atau pengeluaranmu.</p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-white">Jenis transaksi</label>
                <div class="mt-2 grid grid-cols-2 gap-2 rounded-2xl bg-[#1A2B52] p-1">
                    <button type="button" wire:click="$set('type', 'expense')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'expense' ? 'bg-[#F87171] text-[#0A1628] shadow-sm' : 'text-[#64748B]' }}">
                        Pengeluaran
                    </button>
                    <button type="button" wire:click="$set('type', 'income')" class="rounded-xl px-3 py-2 text-sm font-bold transition {{ $type === 'income' ? 'bg-[#A3E635]/100 text-white shadow-sm' : 'text-[#64748B]' }}">
                        Pemasukan
                    </button>
                </div>
                @error('type')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-semibold text-white">Nominal</label>
                <div class="mt-2 flex rounded-xl border border-[rgba(255,255,255,0.1)] bg-[#1A2B52] shadow-sm focus-within:border-[#A3E635] focus-within:ring-2 focus-within:ring-[#A3E635]/20">
                    <span class="flex items-center px-4 text-sm font-bold text-[#64748B]">Rp</span>
                    <input id="amount" type="number" min="0" step="0.01" wire:model="amount" class="block w-full rounded-r-2xl border-0 bg-transparent px-2 py-3 text-sm focus:ring-0" style="color:#FFFFFF !important;" placeholder="50000">
                </div>
                @error('amount')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-semibold text-white">Kategori</label>
                @if ($categories->isEmpty())
                    <div class="mt-2 rounded-2xl border border-[#F59E0B]/25 bg-[#F59E0B]/10 p-4">
                        <p class="text-sm font-bold text-[#F59E0B]">Belum ada kategori untuk tipe ini.</p>
                        <p class="mt-1 text-xs text-[#F59E0B]">Buat kategori dulu di Pengaturan.</p>
                        <a href="{{ route('categories') }}" class="mt-3 inline-flex rounded-xl bg-[#F59E0B] px-3 py-2 text-xs font-bold text-white">
                            Atur kategori
                        </a>
                    </div>
                @else
                    <select id="category_id" wire:model="category_id" class="mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                @endif
                @error('category_id')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="transaction_date" class="block text-sm font-semibold text-white">Tanggal</label>
                <input id="transaction_date" type="date" wire:model="transaction_date" class="mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;">
                @error('transaction_date')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="note" class="block text-sm font-semibold text-white">Catatan <span class="font-normal text-[#64748B]">opsional</span></label>
                <textarea id="note" wire:model="note" rows="3" class="mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm focus:border-[#A3E635] focus:ring-[#A3E635]/20" style="background:#1A2B52 !important;border:1px solid rgba(255,255,255,0.1) !important;color:#FFFFFF !important;" placeholder="Contoh: Beli nasi goreng"></textarea>
                @error('note')
                    <p class="mt-2 text-xs font-medium text-[#F87171]">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded-2xl px-4 py-3 text-sm font-bold shadow-lg shadow-lime-500/20 transition active:scale-[0.99] disabled:opacity-60" style="background:#A3E635 !important;color:#0A1628 !important;" @disabled($categories->isEmpty())>
                Simpan Perubahan
            </button>
        </form>
    </section>

    <section class="rounded-3xl p-4" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-white">Hapus transaksi</h2>
                <p class="mt-1 text-xs text-[#64748B]">Data akan disembunyikan dari riwayat, bukan dihapus permanen.</p>
            </div>
            <button type="button" wire:click="delete" wire:confirm="Yakin ingin menghapus transaksi ini? Data akan disembunyikan dari riwayat, bukan dihapus permanen." class="shrink-0 rounded-2xl bg-[#e24b4a]/10 px-4 py-3 text-sm font-bold text-[#F87171]">
                Hapus Transaksi
            </button>
        </div>
    </section>

    <a href="{{ route('transactions.index') }}" class="block text-center text-sm font-bold text-[#A3E635]">
        Kembali ke Riwayat Transaksi
    </a>
</div>
